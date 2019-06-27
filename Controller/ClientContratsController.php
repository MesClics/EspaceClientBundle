<?php
namespace MesClics\EspaceClientBundle\Controller;

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
    public function postAction(Client $client, Request $request){

    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("client", options={"mapping": {"client_id": "id"}})
     * @ParamConverter("contrat", options={"mapping": {"contrat_id": "id"}})
     */
    public function getAction(Client $client, Contrat $contrat, ContratFormManager $contratFormManager, ContratAssocierProjetFormManager $contratAssocierProjetsFormManager, ContratDissocierProjetFormManager $contratDissocierProjetFormManager, Request $request){
        //on génère les formulaires :
        //MODIFICATION DE CONTRAT
        $contratForm = $this->createForm(ContratType::class, $contrat, array(
            'client' => $client
        ));

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
            $contratFormManager->handle($contrat);
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
}