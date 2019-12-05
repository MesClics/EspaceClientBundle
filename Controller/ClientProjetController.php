<?php
namespace MesClics\EspaceClientBundle\Controller;

use Symfony\Component\Asset\Packages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ProjetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\EspaceClientBundle\Form\ProjetAssocierContratType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use MesClics\EspaceClientBundle\Form\ProjetDissocierContratType;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetAttachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetDetachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetRemoveEvent;
use MesClics\EspaceClientBundle\Popups\MesClicsEspaceClientProjetPopups;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetAssocierContratFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetDissocierContratFormManager;

class ClientProjetController extends Controller{

    private $entity_manager;
    private $event_dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $ed){
        $this->entity_manager = $em;
        $this->event_dispatcher = $ed;
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("projet", options={"mapping": {"projet_id": "id"}})
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function updateAction(Client $client, Projet $projet, ProjetFormManager $projetFormManager, ProjetAssocierContratFormManager $projetAssocierContratFormManager, ProjetDissocierContratFormManager $projetDissocierContratFormManager, Request $request){
        //on génère le formulaire de modification de projet
        $projet_form = $this->createForm(ProjetType::class, $projet);
        //on gère le formulaire
        if($request->isMethod('POST')){
            $projetFormManager->handle($projet_form);

            if($projetFormManager->hasSucceeded()){
                $redirect_args = array(
                    "client_id" => $client->getId(),
                    "projet_id" => $projet->getId()
                );
                return $this->redirectToRoute("mesclics_admin_client_projet", $redirect_args);
            }
        }
        //on génère les formulaires d'association / dissociation de contrat
        //on check si le projet est déjà associé à un contrat
        $hasContrat = $projet->getContrat();
        if(!$hasContrat){
            $projetAssocierContratForm = $this->createForm(ProjetAssocierContratType::class, $projet);
            //si la requête est de type Post
            if($request->isMethod('POST')){
                //on appelle le Projet Form Manager
                $projetAssocierContratFormManager->handle($projetAssocierContratForm);
                if($projetAssocierContratFormManager->hasSucceeded()){
                    $redirect_args = array(
                        'client_id' => $client->getId(),
                        'projet_id' => $projet->getId()
                    );
                    return $this->redirectToRoute('mesclics_admin_client_projet', $redirect_args);
                }
            }
        } else{ 
            $projetDissocierContratForm = $this->createForm(ProjetDissocierContratType::class, $projet);
            if($request->isMethod('POST')){
                $projetDissocierContratFormManager->handle($projetDissocierContratForm);
                if($projetDissocierContratFormManager->hasSucceeded()){
                    $redirect_args = array(
                        'client_id' => $client->getId(),
                        'projet_id' => $projet->getId()
                    );
                    return $this->redirectToRoute('mesclics_admin_client_projet', $redirect_args);
                }
            }
        }

        //on génère la vue
        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'projets',
            'mainContent' => 'client-projet',
            'projet' => $projet,
            'client' => $client,
            'projetForm' => $projet_form->createView()
        );

        if(isset($projetAssocierContratForm)){
            $args['projetAssocierContratForm'] = $projetAssocierContratForm->createView();
        }
        if(isset($projetDissocierContratForm)){
            $args['projetDissocierContratForm'] = $projetDissocierContratForm->createView();
        }

        return $this->render('MesClicsEspaceClientBundle:Admin:client-projet.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     */
    public function postAction(Client $client, ProjetFormManager $form_manager, Request $request){
        $projet = new Projet();
        $projet->setClient($client);
        $form = $this->createForm(ProjetType::class, $projet);

        if($request->getMethod('POST')){
            $form_manager->handle($form);

            if($form_manager->hasSucceeded()){
                $redirect_args = array(
                    'currentSection' => 'clients',
                    'subSection' => 'projets',
                    'mainContent' => 'client-projet',
                    'currentProjet' => $projet,
                    'client_id' => $client->getId(),
                    'projet_id' => $projet->getId()
                );
                return $this->redirectToRoute('mesclics_admin_client_projet', $redirect_args);
            }
        }

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'projets',
            'mainContent' => 'client-projet',
            'currentProjet' => $projet,
            'client' => $client,
            'projetForm' => $form->createView()
        );

        return $this->render('MesClicsEspaceClientBundle:Admin:client-projet.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     * @ParamConverter("projet", options={"mapping":{"projet_id": "id"}})
     */
    public function deleteAction(Client $client, Projet $projet){
        //TODO: check if projet is from the right client
        //TODO: check if user is part of the client's users
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
        $projet = new Projet();
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
    public function confirmAttachAction(Projet $projet, Contrat $contrat){
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
    public function confirmDetachAction(Client $client, Projet $projet){
        $contrat = $projet->getContrat();
        $contrat->removeProjet($projet);
        
        // dispatch event
        $event = new MesClicsClientProjetDetachEvent($projet, $contrat);
        $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::DETACHMENT, $event);

        $this->entity_manager->flush();

        

        return $this->redirectToRoute("mesclics_admin_client_contrat", array("client_id" => $client->getId(), "contrat_id" => $contrat->getId()));
    }
}