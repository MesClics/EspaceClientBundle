<?php
namespace MesClics\EspaceClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\EspaceClientBundle\Form\ClientEditType;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\EspaceClientBundle\Form\ContratAssocierProjetsType;;
use MesClics\EspaceClientBundle\Form\ContratDissocierProjetType;
use MesClics\EspaceClientBundle\Form\ProjetAssocierContratType;
use MesClics\EspaceClientBundle\Form\ProjetDissocierContratType;
use MesClics\UserBundle\Entity\User;
use MesClics\UserBundle\Form\UserAdminRegistrationType;
use MesClics\UserBundle\Form\UserType;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ClientsController extends Controller{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function editAction(Client $client, Request $request){
        $args = array(
            'currentSection' => 'clients',
            'client' => $client
        );
        
        //on vérifie que le client n'a pas de contrat, auquel cas on ne pourrait pas modifier ses données
        if(empty($client->getContrats()->toArray())){
            //on ajoute le formulaire de modification du client
            $clientEditForm = $this->createForm(ClientEditType::class, $client);

            if($request->isMethod('POST')){
                $clientEditFormManager = $this->get('mesclics_espace_client.form_manager.client.edit');
                $clientEditFormManager->handle($clientEditForm);
            }

            $args['client_edit_form'] = $clientEditForm->createView();
        } else{
            $request->getSession()->getFlashBag()->add('error', 'Le client a déjà signé un ou plusieurs contrats. Il est donc impossible de modifier ses données. Veuillez créer un nouveau client.');
        }
        return $this->render('MesClicsEspaceClientBundle:Admin:client.html.twig', $args);
    }

    //CLIENTS > PROJETS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function projetsAction(Client $client, Request $request){
        //récupération des projets du client
        $projets = $client->getProjets();

        //AJOUT DE PROJET
        //on crée un objet qui sera hydraté par le formulaire
        $projet = new Projet();
        //on génère le formulaire
        $projetForm = $this->createForm(ProjetType::class, $projet, array(
            'client' => $client
        ));
        //si la requête est de type POST, on gère le formulaire
        if($request->isMethod('POST')){
            $projetFormManager = $this->get('mesclics_espace_client.form_manager.projet.new');
            $projetFormManager->setClient($client)->handle($projetForm);
        }
                
        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'projets',
            'mainContent' => 'client-projets',
            'client' => $client,
            'projets' => $projets,
            'projetsForm' => $projetForm->createView()
        );

        return $this->render('MesClicsEspaceClientBundle:Admin:client.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("projet", options={"mapping": {"projet_id": "id"}})
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function projetAction(Client $client, Projet $projet, Request $request){
        //on check si le projet est déjà associé à un contrat
        $hasContrat = $projet->getContrat();

        //on génère les formulaires d'association / dissociation de contrat
        if(!$hasContrat){
            $projetAssocierContratForm = $this->createForm(ProjetAssocierContratType::class, $projet);
            //si la requête est de type Post
            if($request->isMethod('POST')){
                //on appelle le Projet Form Manager
                $projetAssocierContratFormManager = $this->get('mesclics_espace_client.form_manager.projet.associer_contrat');
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
                $projetDissocierContratFormManager = $this->get('mesclics_espace_client.form_manager.projet.dissocier_contrat');
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
            'currentProjet' => $projet,
            'client' => $client
        );

        if(isset($projetAssocierContratForm)){
            $args['projetAssocierContratForm'] = $projetAssocierContratForm->createView();
        }
        if(isset($projetDissocierContratForm)){
            $args['projetDissocierContratForm'] = $projetDissocierContratForm->createView();
        }

        return $this->render('MesClicsEspaceClientBundle:Admin:client.html.twig', $args);
    }

    //CLIENTS > CONTRATS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function contratsAction(Client $client, Request $request){
        //récupération des contrats du client
        $contrats = $client->getContrats();

        //on crée un objet qui sera hydraté par le formulaire
        $contrat = new Contrat();
        //on génère le formulaire
        $contratForm = $this->createForm(ContratType::class, $contrat, array(
            'client' => $client
        ));
        
        //on gère le formulaire
        //si la requête est de type POST
        if($request->isMethod('POST')){
            $contratFormManager = $this->get('mesclics_espace_client.form_manager.contrat.new');
            //on passe le client au manager, et on gère le formulaire
            $contratFormManager
                ->setClient($client)
                ->handle($contratForm);
        }

        //on génère la vue
        $args = array(
            'currentSection' => 'clients',
            'mainContent' => 'client-contrats',
            'subSection' => 'contrats',
            'client' => $client,
            'contrats' => $contrats,
            'contratForm' => $contratForm->createView()
        );
        return $this->render('MesClicsAdminBundle:Panel:client.html.twig', $args);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping": {"contrat_id": "id"}})
     */
    public function contratAction(Client $client, Contrat $contrat, Request $request){
        //on génère les formulaires :
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
                //ASSOCIATION DE PROJETS
                $contratAssocierProjetsFormManager = $this->get('mesclics_espace_client.form_manager.contrat.associer_projet');
                $contratAssocierProjetsFormManager->setContrat($contrat)->handle($contratAssocierProjetsForm);
               
                //DISSOCIATION DE PROJET
                foreach($projets as $projet){
                    if(isset(${'dissociation'.$projet->getId()})){

                        ${'contratDissocierProjet'.$projet->getId().'FormManager'} = $this->get('mesclics_espace_client.form_manager.contrat.dissocier_projet');
                        ${'contratDissocierProjet'.$projet->getId().'FormManager'}->handle(${'dissociation'.$projet->getId()});

                    }
                }

                return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
            }

        $args = array(
            'currentSection' => 'clients',
            'mainContent' => 'client-contrat',
            'subSection' => 'contrats',
            'client' => $client,
            'currentContrat' => $contrat,
            'contratAssocierProjetsForm' => $contratAssocierProjetsForm->createView(),
            'dissociationForms' => $dissociationForms
        );
        return $this->render('MesClicsEspaceClientBundle:Admin:client.html.twig', $args);
    }
}