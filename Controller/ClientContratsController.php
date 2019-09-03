<?php
namespace MesClics\EspaceClientBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use MesClics\EspaceClientBundle\Form\ContratAssocierProjetsType;
use MesClics\EspaceClientBundle\Form\ContratDissocierProjetType;
use MesClics\EspaceClientBundle\Form\FormManager\ContratFormManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use MesClics\EspaceClientBundle\Form\FormManager\ContratAssocierProjetFormManager;
use MesClics\EspaceClientBundle\Form\FormManager\ContratDissocierProjetFormManager;

class ClientContratsController extends Controller{

    /**
     * @ParamConverter("client", options={"mapping":{"client_id": "id"}})
     */
    public function postAction(Client $client, ContratFormManager $form_manager, Request $request){
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat, array('client' => $client));

        if($request->isMethod('POST')){
            $form_manager->handle($form);
            if($form_manager->hasSucceeded()){
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
    public function getAction(Client $client, Contrat $contrat, ContratFormManager $contratFormManager, ContratAssocierProjetFormManager $contratAssocierProjetsFormManager, ContratDissocierProjetFormManager $contratDissocierProjetFormManager, Request $request){
        //on génère les formulaires :
        //MODIFICATION DE CONTRAT
        $contratForm = $this->createForm(ContratType::class, $contrat);

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
            //MODIFICATION DE CONTRAT
            $contratFormManager->handle($contratForm);
            if($contratFormManager->hasSucceeded()){
                return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
            }
            //ASSOCIATION DE PROJETS
            $contratAssocierProjetsFormManager->setContrat($contrat)->handle($contratAssocierProjetsForm);
            
            //DISSOCIATION DE PROJET
            foreach($projets as $projet){
                if(isset(${'dissociation'.$projet->getId()})){

                    ${'contratDissocierProjet'.$projet->getId().'FormManager'} = $contratDissocierProjetFormManager;
                    ${'contratDissocierProjet'.$projet->getId().'FormManager'}->handle(${'dissociation'.$projet->getId()});

                }
            }

            return $this->redirectToRoute('mesclics_admin_client_contrat', array('client_id' => $client->getId(), 'contrat_id' => $contrat->getId()));
        }

        $args = array(
            'currentSection' => 'clients',
            'subSection' => 'contrat',
            'client' => $client,
            'contrat_id' => $contrat->getId(),
            'contrat' => $contrat,
            'contratForm' => $contratForm->createView(),
            'contratAssocierProjetsForm' => $contratAssocierProjetsForm->createView(),
            'dissociationForms' => $dissociationForms
        );
        return $this->render('MesClicsEspaceClientBundle:Admin:client-contrat.html.twig', $args);
    }

    
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("contrat", options={"mapping":{"contrat_id": "id"}})
     */
    public function removeAction(Contrat $contrat, EntityManagerInterface $em, Request $request){
        if($request->isMethod('GET')){
            $confirm = $request->query->get('remove');
            if($confirm){
                $args = array(
                    "client_id" => $contrat->getClient()->getId()
                );
                $em->remove($contrat);
                $em->flush();
                // TODO: add a flash message
                return $this->redirectToRoute('mesclics_admin_client_contrats', $args);
            } else{
                // TODO: get back to the original page (or close the confirm panel)
            }
        }

        $args = array(
            'currentSection' => 'client',
            'subSection' => 'contrats',
            'contrat' => $contrat,
            'client' => $contrat->getClient(),
            'popups' => array(
                'remove' => array(
                    'options' => array(
                        'illustration' => array(
                            'url' => '@mesclicsespaceclientbundle/images/icones/contrats/svg/remove.svg',
                            'alt' => 'illustration de suppression de contrat',
                            'title' => 'supprimer un contrat',
                            'type' => 'svg',
                            'class' => 'contrat-remove'
                        ),
                        'class' => 'alert'
                    ),
                    'template' => 'MesClicsEspaceClientBundle:PopUps:client-contrats-remove.html.twig'
                )
            )
        );
        return $this->render('MesClicsAdminBundle:Panel:client.html.twig', $args);

    }
}