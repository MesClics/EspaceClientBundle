<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientUpdateEvent;
use MesClics\EspaceClientBundle\Form\FormManager\ClientFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ClientController extends Controller{

    private $entity_manager;
    private $event_dispatcher;


    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function getAction(Client $client, ClientFormManager $form_manager, Request $request, EventDispatcherInterface $event_dispatcher){
        //clone the initial client object:
        $client_before_update = clone $client;
        //add update datas form
        $form = $this->createForm(ClientType::class, $client);
        $form_manager->handle($form);
        if($request->isMethod('POST')){
            if($form_manager->hasSucceeded()){
                //dispatch a MesClicsClientUpdateEvent
                if($client_before_update !== $form_manager->getResult()){
                    $event = new MesClicsClientUpdateEvent($client_before_update, $form_manager->getResult());
                    $event_dispatcher->dispatch('mesclics_client.update', $event);
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

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postAction(ClientFormManager $client_form_manager, Request $request, EventDispatcherInterface $event_dispatcher){
        $client = new Client();

        //création du formulaire
        $client_form = $this->createForm(ClientType::class, $client);

        //on traite éventuellement le formulaire
        if($request->isMethod('POST')){
            $client_form_manager->handle($client_form);
            if($client_form_manager->hasSucceeded()){
                $args = array(
                    'client_id' => $client_form_manager->getResult()->getID()
                );

                //TODO: dispatch client creation event
                $event = new MesClicsClientCreationEvent($client_form_manager->getResult());
                $event_dispatcher->dispatch('mesclics_client.creation', $event);

                return $this->redirectToRoute("mesclics_admin_client", $args);
            }
        }

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'new',
            'new_client_form' => $client_form->createView()
        );

        return $this->render('MesClicsEspaceClientBundle:Admin:clients.html.twig');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function updateAction(Client $client, ClientFormManager $client_form_manager, Request $request, EventDispatcherInterface $ed){
        $args = array(
            'currentSection' => 'clients',
            'client' => $client
        );

        $client_before_update = clone $client;
        
        //on vérifie que le client n'a pas de contrat, auquel cas on ne pourrait pas modifier ses données
        if(empty($client->getContrats()->toArray())){
            //on ajoute le formulaire de modification du client
            $clientEditForm = $this->createForm(ClientEditType::class, $client);

            if($request->isMethod('POST')){
                $clientFormManager->handle($clientEditForm);

                // dispatch MesClicsClientUpdateEvent
                if($clientFormManager->hasSucceeded()){
                    $event = new MesClicsClientUpdateEvent($client_before_update, $clientFormManager->getResult());
                    $ed->dispatch('mesclics_client.update', $event);
                }
            }

            $args['client_edit_form'] = $clientEditForm->createView();
        } else{
            $request->getSession()->getFlashBag()->add('error', 'Le client a déjà signé un ou plusieurs contrats. Il est donc impossible de modifier ses données. Veuillez créer un nouveau client.');
        }
        return $this->render('MesClicsEspaceClientBundle:Admin:clients.html.twig', $args);
    }
}