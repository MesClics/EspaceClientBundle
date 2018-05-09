<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;
use MesClics\EspaceClientBundle\Entity\Contrat;

class ContratAssocierProjetFormManager extends FormManager{
    private $contrat;
    
    const ERROR_NOTIFICATION_SINGULIER = "Le projet n'a pas pu être associé au contrat. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les projets n'ont pas pu être associés au contrat . Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Les projet a été associé au contrat.";
    const SUCCESS_NOTIFICATION_PLURIEL = "Les projets ont été associés au contrat.";

    public function setContrat(Contrat $contrat){
        $this->contrat = $contrat;
        return $this;
    }

    public function handle(Form $form, $addNotification = true){
        $this->hydrate(array(
            'form' => $form
        ));
        //on fait le lien entre la requête et le formulaire, $object contient les valeurs entrées dans le formulaire
        $this->getForm()->handleRequest($this->getRequest());
        //on vérifie la validité des données saisies
        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $this->setAction($this->getForm()->getClickedButton()->getName());
            $object = $this->getForm()->getData();
            
            //on associe les projets au contrat
            $projets = $object->getProjets();
            $compteur = 0;
            if($projets){
                foreach($projets as $projet){
                    $projet->setContrat($this->contrat);
                    $projet->setContrat($object);
                    $compteur++;
                }
                $this->setSuccess(true);
                $this->setResult($this->getEm()->flush());
            }  
        }
        if($addNotification){
            $this->setNotification();
        }
        return $this;
    }
}