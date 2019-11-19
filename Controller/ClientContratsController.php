<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Form\ContratAssocierProjetsType;
use MesClics\EspaceClientBundle\Form\ContratDissocierProjetType;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Form\FormManager\ContratFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratRemoveEvent;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientContratPopups;
use MesClics\EspaceClientBundle\Form\FormManager\ContratAssocierProjetFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ContratDissocierProjetFormManager;

class ClientContratsController extends Controller{

    private $entity_manager;
    private $session;
    private $event_dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed, SessionInterface $session){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
        $this->session = $session;
    }

    /**
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     */
    public function postAction(Client $client, ContratFormManager $form_manager, Request $request){
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat, array('client' => $client));

        if($request->isMethod('POST')){
            $form_manager->handle($form);
            if($form_manager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_clients_contrat', array("client_id" => $client->getId(), "contrat_id" => $form_manager->getResult()->getId()));
            }
        }

        $args = array(
            "currentSection" => "client",
            "subSection" => "contrat",
            "client" => $client,
            "contrat" => $contrat,
            "contratForm" => $form->createView()
        );

        return $this->render('MesClicsEspaceClientBundle:Admin:client-contrat.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping": {"contrat_id": "id"}})
     */
    public function getAction(Client $client, Contrat $contrat, ContratFormManager $contratFormManager, ContratAssocierProjetFormManager $contratAssocierProjetsFormManager, ContratDissocierProjetFormManager $contratDissocierProjetFormManager, Request $request){
        //on génère les formulaires :
        //MODIFICATION DE CONTRAT
        $contratForm = $this->createForm(ContratType::class, $contrat);

        //ASSOCIATION DE PROJETS
        //on crée le formulaire
        $contratAssocierProjetsForm = $this->createForm(ContratAssocierProjetsType::class, $contrat, array(
            'client' => $client
        ));
    
        //DISSOCIATION DE PROJET DU CONTRAT
        //on récupère les projets du contrat
        $projets = $contrat->getProjets();
        $dissociationForms = array();
        foreach($projets as $projet){
            //on génère le formulaire bouton de dissociation
            ${'dissociation'.$projet->getId()} = $this->createForm(ContratDissocierProjetType::class, $contrat, array(
                'projet' => $projet 
            ));
            //on ajoute le formulaire dans un tableau
            $key = 'dissociation'.$projet->getId();
            $value = ${'dissociation'.$projet->getId()}->createView();
            $dissociationForms[$key] = $value;
        }

        //on gère les formulaires
        //si la requête est de type POST
        if($request->isMethod('POST')){
            //MODIFICATION DE CONTRAT
            $contratFormManager->handle($contratForm);
            if($contratFormManager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
            }
            //ASSOCIATION DE PROJETS
            $contratAssocierProjetsFormManager->setContrat($contrat)->handle($contratAssocierProjetsForm);
            
            //DISSOCIATION DE PROJET
            foreach($projets as $projet){
                if(isset(${'dissociation'.$projet->getId()})){

                    ${'contratDissocierProjet'.$projet->getId().'FormManager'} = $contratDissocierProjetFormManager;
                    ${'contratDissocierProjet'.$projet->getId().'FormManager'}->handle(${'dissociation'.$projet->getId()});

                }
            }

            return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
        }

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'contrat',
            'client' => $client,
            'contrat_id' => $contrat->getId(),
            'contrat' => $contrat,
            'contratForm' => $contratForm->createView(),
            'contratAssocierProjetsForm' => $contratAssocierProjetsForm->createView(),
            'dissociationForms' => $dissociationForms
        );
        return $this->render('MesClicsEspaceClientBundle:Admin:client-contrat.html.twig', $args);
    }

    
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function deleteAction(Client $client, Contrat $contrat){
        $this->entity_manager->remove($contrat);
        
        // dispatch event
        $event = new MesClicsClientContratRemoveEvent($contrat);
        $this->event_dispatcher->dispatch(MesClicsClientContratEvents::REMOVAL, $event);
        $this->entity_manager->flush();

        $args = array(
            'client_id' => $client->getId()
        );

        return $this->redirectToRoute('mesclics_admin_client_contrats', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function removeAction(Contrat $contrat){
        $popups = array();

        MesClicsEspaceClientContratPopups::onDelete($popups);

        $args = array(
            'client' => $contrat->getClient(),
            'contrat' => $contrat,
            'popups' => $popups
        );

        $popup = $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
        return $popup;
    }
}