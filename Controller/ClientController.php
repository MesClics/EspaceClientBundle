<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use MesClics\EspaceClientBundle\Form\DTO\ClientDTO;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\EspaceClientBundle\Event\MesClicsClientEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientCreationEvent;
use MesClics\EspaceClientBundle\Form\FormManager\ClientFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ClientController extends Controller{

    private $entity_manager;
    private $event_dispatcher;


    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
    }

    public function generateClientForm(Client $client = null){
        
        $clientDTO = new ClientDTO();
        if($client){
            $clientDTO->mapFrom($client);
        }

        $form = $this->createForm(ClientType::class, $clientDTO);

        return $form;
    }

    
    //CLIENTS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function clientsAction(ApisManager $apis_manager, Request $request){
        $clientRepo = $this->entity_manager->getRepository('MesClicsEspaceClientBundle:Client');
        $clients = $clientRepo->getClientsList();
        
        $trello_api = $apis_manager->getApi('trello');

        //DEBUG: remise à zero des éléments enregistrés en tant que paramètres de session pour l'api Trello
        // $this->container->get('session')->remove('_trello');
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

        //WIDGET Ajouter un client
        //création du formulaire
        $clientForm = $this->generateClientForm(null, $request);
        
        if($request->isMethod('POST')){

            $clientForm->handleRequest($request);

            if($clientForm->isSubmitted() && $clientForm->isValid()){
                $client = new Client();
                $clientDTO = $clientForm->getData();
                $clientDTO->mapTo($client);
                $this->entity_manager->persist($client);
                $event = new MesClicsClientCreationEvent($client);
                $this->entity_manager->flush();
                $this->event_dispatcher->dispatch(MesClicsClientEvents::CREATION, $event);
                return $this->redirectToRoute("mesclics_admin_client", array("client_id" => $client->getId()));
            }
        }

        //on génère la vue
        $args = array(
            'clients' => $clients,
            'client_new_form' => $clientForm->createView(),
            'currentSection' => 'clients',
            'trelloClientsBoard' => $trelloClientsBoard
        );

        return $this->render('MesClicsAdminBundle:Panel:clients.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function getAction(Client $client, Request $request){
        $form = $this->generateClientForm($client);

        if($request->isMethod('POST')){
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $before_update = clone $client;
                $clientDTO = $form->getData();
                $clientDTO->mapTo($client);

                if($client !== $before_update){
                    $event = new MesClicsClientUpdateEvent($before_update, $client);
                    $this->event_dispatcher->dispatch(MesClicsClientEvents::UPDATE, $event);
                    $this->entity_manager->flush();
                }
                
                $this->redirectToRoute("mesclics_admin_client", array("client_id" => $client->getId()));
            }
        }


            $args = array(
                'currentSection' => 'clients',
                'client' => $client,
                'client_new_form' => $form->createView()
            );
            return $this->render('MesClicsAdminBundle:Panel:client.html.twig', $args);
    }
}