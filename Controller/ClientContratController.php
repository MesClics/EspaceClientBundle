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
use MesClics\EspaceClientBundle\Widget\ClientContratWidgets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Form\FormManager\ContratFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratRemoveEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratCreationEvent;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientContratPopups;

class ClientContratController extends Controller{

    private $entity_manager;
    private $session;
    private $event_dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed, SessionInterface $session){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
        $this->session = $session;
    }

    
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function contratsAction(Client $client, ClientContratWidgets $widgets, Request $request){
        $params = array(
            'client' => $client
        );
        $widgets->initialize($params);
        $res = $widgets->handleRequest($request);

        if($res && $res instanceof Contrat){
            return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $res->getClient()->getId(), 'contrat_id' => $res->getId()));
        }

        //on génère la vue
        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array("client_id" => $client->getId())),
                'contrats' => $this->generateUrl('mesclics_admin_client_contrats', array("client_id" => $client->getId()))
            ),
            'widgets' => $widgets->getWidgets()
        );
        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("hasRole('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     */
    public function postAction(Client $client, Request $request){
        $contratDTO = new ContratDTO($client);
        $form = $this->createForm(ContratType::class, $contratDTO);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSumbitted() && $form->isValid()){
                $contrat = new Contrat();

                $this->entity_manager->persist($contrat);

                $contratDTO->mapTo($contrat);

                $event = new MesClicsClientContratCreationEvent($contrat);
                $this->event_dispatcher->dispatch(MesClicsClientContratEvents::CREATION, $event);
                
                $this->entity_manager->flush();
                
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
    public function getAction(Client $client, Contrat $contrat, ContratFormManager $contratFormManager, Request $request){
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