<?php
namespace MesClics\EspaceClientBundle\Controller;

use Symfony\Component\Asset\Packages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Form\ProjetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\EspaceClientBundle\Form\ProjetAssocierContratType;
use MesClics\EspaceClientBundle\Form\ProjetDissocierContratType;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetAssocierContratFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetDissocierContratFormManager;

class ClientProjetController extends Controller{

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
    public function removeAction(Client $client, Projet $projet, EntityManagerInterface $em, Packages $assets_manager, Request $request){
        if($request->isMethod("GET")){
            if($request->query->get('remove')){
                $redirect_args = array(
                    "client_id" => $client->getId()
                );
                $em->remove($projet);
                $em->flush();
                // TODO: add a flash message
                return $this->redirectToRoute('mesclics_admin_client_projets', $redirect_args);
            } 
        }

        $args = array(
            'currentSection' => 'client',
            'subSection' => 'projets',
            'projet' => $projet,
            'client' => $client,
            'popups' => array(
                'remove' => array(
                    'options' => array(
                        'illustration' => array(
                            'url' => '@mesclicsespaceclientbundle/images/icones/projets/svg/remove.svg',
                            'alt' => 'illustration de suppression de projet',
                            'title' => 'supprimer un projet',
                            'type' => 'svg',
                            'class' => 'projet-remove'
                        ),
                        'class' => 'alert'
                    ),
                    'template' => 'MesClicsEspaceClientBundle:PopUps:client-projets-remove.html.twig'
                )
            )
        );
        return $this->render('MesClicsAdminBundle:Panel:client.html.twig', $args);
    }

    public function addNewProjetForm(Client $client){
        $projet = new Projet();
        $projet->setClient($args['client']);

        $form = $this->createForm(ProjetType::class, $projet);
        return $form;
    }
}