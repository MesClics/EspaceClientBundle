<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use MesClics\EspaceClientBundle\Widget\ClientEditWidgets;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\EspaceClientBundle\Widget\ClientsHomeWidgets;
use MesClics\EspaceClientBundle\Event\MesClicsClientEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientRemovalEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientClientPopups;

class ClientController extends Controller{

    private $entity_manager;
    private $event_dispatcher;


    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
    }
    
    //CLIENTS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function clientsAction(ClientsHomeWidgets $widgets, ApisManager $apis_manager, Request $request){        
        $trello_api = $apis_manager->getApi('trello');

        //GET CLIENTS BOARD
        $boards_options = array(
            'fields' => array(
                'name',
                'id',
                'url',
                'shortUrl',
            ),
            'lists' => 'open',
            'lists_fields' => array(
                'id',
                'name',
            )
        );
        $trelloClientsBoard = $trello_api->getBoardByName("CLIENTS", $boards_options);

        // return $this->render('MesClicsAdminBundle:Panel:clients.html.twig', $args);
        $widgets->initialize();
        $widgets->getWidget('clients_list')->addClass('large');
        $widgets->getWidget('client_creation')->addClass('highlight2 small');
        $widgets->getWidget('client_creation')->addVariable('isSlideshow', true);
        $widgets->handleRequest($request);

        $args = array(
            "navRails" => array(
                'clients' => $this->generateUrl('mesclics_admin_clients')
            ),
            "widgets" => $widgets->getWidgets(),
            'trelloClientsBoard' => $trelloClientsBoard
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function getAction(Client $client, ClientEditWidgets $widgets, Request $request){
        $params = array(
            'client' => $client
        );
        $widgets->initialize($params);
        $widgets->getWidget('client_edit')->addClass("medium");
        $widgets->getWidget('client_edit')->addVariable("submit_label", "enregistrer les modifications");
        $widgets->handleRequest($request);

        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array('client_id' => $client->getId()))
            ),
            'widgets' => $widgets->getWidgets()
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function removeAction(Client $client){
        $popups = array();
        MesClicsEspaceClientClientPopups::onRemoval($popups);

        $args = array(
            "client" => $client,
            "popups" => $popups
        );

        $popup = $this->render('MesClicsBundle:PopUps:renderer.html.twig', $args);

        return $popup;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function deleteAction(Client $client){
        $this->entity_manager->remove($client);
        $event = new MesClicsClientRemovalEvent($client);
        $this->event_dispatcher->dispatch(MesClicsClientEvents::REMOVAL, $event);
        $this->entity_manager->flush();

        return $this->redirectToRoute('mesclics_admin_clients');
    }
}