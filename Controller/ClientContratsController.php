<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\EspaceClientBundle\Form\DTO\ContratDTO;
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
use MesClics\EspaceClientBundle\Event\MesClicsClientContratUpdateEvent;
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
        // TODO: on vérifie que l'utilisateur courant peut géréer les contrats

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'contrat',
            'client' => $client,
            'contrat_id' => $contrat->getId(),
            'contrat' => $contrat
        );

        // on génère les formulaires
        // MODIFICATION DE CONTRAT
        $contratDTO = new ContratDTO();
        $contratDTO->mapFrom($contrat);
        $contratForm = $this->createForm(ContratType::class, $contratDTO);
        $args["contratForm"] = $contratForm->createView();

        //ASSOCIATION DE PROJETS
        // check if there are some unattached projects for this client
        $unattached_projets = $this->entity_manager->getRepository(Projet::class)->getProjetsWithNoContrat($client);
        $args["unattachedProjets"] = $unattached_projets;
        
        //on gère le formulaire
        //si la requête est de type POST
        if($request->isMethod('POST')){
            //MODIFICATION DE CONTRAT
            $contratForm->handleRequest($request);
            if($contratForm->isSubmitted() && $contratForm->isValid()){
                $old_contrat = clone $contrat;
                $contrat_dto = $contratForm->getData();
                $contrat_dto->mapTo($contrat);

                $event = new MesClicsClientContratUpdateEvent($old_contrat, $contrat);
                $this->event_dispatcher->dispatch(MesClicsClientContratEvents::UPDATE, $event);

                $this->entity_manager->flush();
                return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
            }
        }
        return $this->render('MesClicsEspaceClientBundle:Admin:client-contrat.html.twig', $args);
    }

    
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function deleteAction(Client $client, Contrat $contrat){
        //TODO: check if user can handle contrats
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