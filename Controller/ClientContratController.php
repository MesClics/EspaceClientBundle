<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\EspaceClientBundle\Form\DTO\ContratDTO;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\EspaceClientBundle\Widget\ClientContratWidgets;
use MesClics\EspaceClientBundle\Widget\ClientContratsWidgets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Widget\ClientContratCreationWidget;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratRemoveEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratCreationEvent;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientContratPopups;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratCreationWidgetHandler;

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
    public function contratsAction(Client $client, ClientContratsWidgets $widgets, Request $request){
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
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     */
    public function postAction(Client $client, ClientContratCreationWidgetHandler $handler, Request $request){
        $nav_widget = new ClientNavWidget($client);
        $nav_widget->addClasses(['oocss-discret', 'not-closable', 'small']);
        
        $widget = new ClientContratCreationWidget($client, $handler);
        $res = $widget->handleRequest($request);

        $widgets = array(
            $nav_widget,
            $widget
        );

        if($res && $res instanceof Contrat){
            return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $res->getClient()->getId(), 'contrat_id' => $res->getId()));
        }

        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array('client_id' => $client->getId())),
                'contrats' => $this->generateUrl('mesclics_admin_client_contrats', array('client_id' => $client->getId())),
                'nouveau contrat' => $this->generateUrl('mesclics_admin_client_contrats_add', array('client_id' => $client->getId()))
            ),
            'widgets' => $widgets
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping": {"contrat_id": "id"}})
     */
    public function getAction(Client $client, Contrat $contrat, ClientContratWidgets $widgets, Request $request){
        $params = array(
            'contrat' => $contrat,
            'client' => $client
        );
        $widgets->initialize($params);
        
        $res = $widgets->handleRequest($request);

        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array('client_id' => $client->getId())),
                'contrats' => $this->generateUrl('mesclics_admin_client_contrats', array('client_id' => $client->getId())),
                $contrat->getNumero() => $this->generateUrl('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()))
            ),
            'widgets' => $widgets->getWidgets()
        );

        // on génère les formulaires

        // //ASSOCIATION DE PROJETS
        
        // //on gère le formulaire
        // //si la requête est de type POST
        // if($request->isMethod('POST')){
        //     //MODIFICATION DE CONTRAT
        //     $contratForm->handleRequest($request);
        //     if($contratForm->isSubmitted() && $contratForm->isValid()){
        //         $old_contrat = clone $contrat;
        //         $contrat_dto = $contratForm->getData();
        //         $contrat_dto->mapTo($contrat);

        //         $event = new MesClicsClientContratUpdateEvent($old_contrat, $contrat);
        //         $this->event_dispatcher->dispatch(MesClicsClientContratEvents::UPDATE, $event);

        //         $this->entity_manager->flush();
        //         return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
        //     }
        // }
        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
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