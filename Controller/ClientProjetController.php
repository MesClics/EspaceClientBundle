<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Form\DTO\ProjetDTO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MesClics\EspaceClientBundle\Widget\ClientProjetsWidgets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Widget\ClientProjetWidgets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetAttachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetDetachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetRemoveEvent;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientProjetPopups;

class ClientProjetController extends Controller{

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
    public function projetsAction(Client $client, ClientProjetsWidgets $widgets, Request $request){
        //Widgets
        $widgets_params = array(
            'client' => $client
        );
        $widgets->initialize($widgets_params);
        $res = $widgets->handleRequest($request);
        
        if($res && $res instanceof Projet){
            return $this->redirectToRoute('mesclics_admin_client_projet', array('client_id' => $client->getId(), 'projet_id' => $res->getId()));
        }
        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array('client_id' => $client->getId())),
                'projets' => $this->generateUrl('mesclics_admin_client_projets', array('client_id' => $client->getId()))
            ),
            'widgets' => $widgets->getWidgets()
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("projet", options={"mapping": {"projet_id": "id"}})
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function updateAction(Client $client, Projet $projet, ClientProjetWidgets $widgets, Request $request){
        $params = array(
            'client' => $client,
            'projet' => $projet
        );
        $widgets->initialize($params);
        $res = $widgets->handleRequest($request);

        if($res && $res instanceof Projet){
            return $this->redirectToRoute('mesclics_admin_client_projet', array('client_id' => $res->getClient()->getId(), 'projet_id' => $res->getId()));
        }

        //on génère la vue
        $args = array(
            'navRails' => array(
                'clients' => $this->generateUrl('mesclics_admin_clients'),
                $client->getNom() => $this->generateUrl('mesclics_admin_client', array('client_id' => $client->getId())),
                $projet->getNom() => $this->generateUrl('mesclics_admin_client_projet', array('client_id' => $client->getId(), 'projet_id' => $projet->getId()))
            ),
            'widgets' => $widgets->getWidgets()
        );

        return $this->render('MesClicsAdminBundle::layout.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     */
    public function deleteAction(Client $client, Projet $projet){
        $redirect_args = array(
            "client_id" => $client->getId()
        );
        $this->entity_manager->remove($projet);
        $event = new MesClicsClientProjetRemoveEvent($projet);
        $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::REMOVAL, $event);
        $this->entity_manager->flush();
        return $this->redirectToRoute('mesclics_admin_client_projets', $redirect_args);
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     */
    public function removeAction(Client $client, Projet $projet){
        $popups = array();
        MesClicsEspaceClientProjetPopups::onDelete($popups);
        $args = array(
            "popups" => $popups,
            "client" => $client,
            "projet" => $projet
        );

        $popup = $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
        return $popup;
    }

    public function addNewProjetForm(Client $client){
        $projet = new ProjetDTO();
        $projet->setClient($args['client']);

        $form = $this->createForm(ProjetType::class, $projet);
        return $form;
    }

    /**
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function attachAction(Projet $projet, Contrat $contrat){
        // TODO: check if user can detach projects
        $popups = array();
        MesClicsEspaceClientProjetPopups::onAttach($popups);
        $args = array(
            "popups" => $popups,
            "contrat" => $contrat,
            "projet" => $projet
        );

        return $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
    }

     /**
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function proceedAttachAction(Projet $projet, Contrat $contrat){
        $contrat->addProjet($projet);

        $event = new MesClicsClientProjetAttachEvent($projet, $contrat);
        $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::ATTACHMENT, $event);
        
        $this->entity_manager->flush();

        return $this->redirectToRoute("mesclics_admin_client_contrat", array("client_id" => $contrat->getClient()->getId(), "contrat_id" => $contrat->getId()));
    }

    /**
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     */
    public function detachAction(Client $client, Projet $projet){
        // TODO: check if user can detach projects
        $popups = array();
        MesClicsEspaceClientProjetPopups::onDetach($popups);
        $args = array(
            "popups" => $popups,
            "client" => $client,
            "projet" => $projet
        );

        return $this->render("MesClicsBundle:PopUps:renderer.html.twig", $args);
    }

     /**
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     */
    public function proceedDetachAction(Client $client, Projet $projet){
        $contrat = $projet->getContrat();
        $contrat->removeProjet($projet);
        
        // dispatch event
        $event = new MesClicsClientProjetDetachEvent($projet, $contrat);
        $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::DETACHMENT, $event);

        $this->entity_manager->flush();

        return $this->redirectToRoute("mesclics_admin_client_contrat", array("client_id" => $client->getId(), "contrat_id" => $contrat->getId()));
    }
}