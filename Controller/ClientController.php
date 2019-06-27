<?php
namespace MesClics\EspaceClientBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Form\ContratType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\EspaceClientBundle\Form\FormManager\ClientFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ProjetFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ContratFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ClientController extends Controller{

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function getAction(Client $client, ClientFormManager $form_manager, Request $request){       
        //add update datas form
        $form = $this->createForm(ClientType::class, $client);
        $form_manager->handle($form);
        if($request->isMethod('POST')){
            if($form_manager->hasSucceeded()){
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
    public function postAction(ClientFormManager $client_form_manager, Request $request){
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
                return $this->redirectToRoute("mesclics_admin_client", $args);
            }
        }

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'new',
            'new_client_form' => $client_form->creatView()
        );

        return $this->render('MesClicsEspaceClientBundle:Admin:clients.html.twig');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function updateAction(Client $client, ClientFormManager $client_form_manager, Request $request){
        $args = array(
            'currentSection' => 'clients',
            'client' => $client
        );
        
        //on vérifie que le client n'a pas de contrat, auquel cas on ne pourrait pas modifier ses données
        if(empty($client->getContrats()->toArray())){
            //on ajoute le formulaire de modification du client
            $clientEditForm = $this->createForm(ClientEditType::class, $client);

            if($request->isMethod('POST')){
                $clientFormManager->handle($clientEditForm);
            }

            $args['client_edit_form'] = $clientEditForm->createView();
        } else{
            $request->getSession()->getFlashBag()->add('error', 'Le client a déjà signé un ou plusieurs contrats. Il est donc impossible de modifier ses données. Veuillez créer un nouveau client.');
        }
        return $this->render('MesClicsEspaceClientBundle:Admin:clients.html.twig', $args);
    }

    
    //CLIENTS > PROJETS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function projetsAction(Client $client, ProjetFormManager $projet_form_manager, Request $request){
        //AJOUT DE PROJET
        //on crée un objet qui sera hydraté par le formulaire
        $projet = new Projet();
        $projet->setClient($client);
        //on génère le formulaire
        $projetForm = $this->createForm(ProjetType::class, $projet);
        //si la requête est de type POST, on gère le formulaire
        if($request->isMethod('POST')){
            $projet_form_manager->handle($projetForm);

            if($projet_form_manager->hasSucceeded()){
                $this->redirectToRoute('mesclics_admin_client_projet', array('client_id' => $client->getId(), 'projet_id' => $projet->getId()));
            }
        }
                
        $args = array(
            'currentSection' => 'client',
            'subSection' => 'projets',
            'client' => $client,
            'projetsForm' => $projetForm->createView()
        );

        return $this->render('MesClicsAdminBundle:Panel:client.html.twig', $args);
    }

    


    //CLIENTS > CONTRATS
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     */
    public function contratsAction(Client $client, ContratFormManager $contrat_form_manager, Request $request){
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
            //on passe le client au manager, et on gère le formulaire
            $contrat_form_manager
                ->setClient($client)
                ->handle($contratForm);

                if($contrat_form_manager->hasSucceeded()){
                    return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat_form_manager->getResult()->getId()));
                }
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
}