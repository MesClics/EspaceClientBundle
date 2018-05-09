<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;


class ContratDissocierProjetFormManager extends FormManager{
    
    const ERROR_NOTIFICATION_SINGULIER = "Le projet n'a pas pu être dissocié du contrat. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les projets n'ont pas pu être dissociés du contrat. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le projet a été dissocié du contrat.";
    const SUCCESS_NOTIFICATION_PLURIEL = "Les projets ont été dissociés du contrat.";


    public function handle(Form $form, $addNotification = true, $redirect = true){
        $this->hydrate(array(
            'form' => $form
        ));
        //on fait le lien entre la requête et le formulaire, $object contient les valeurs entrées dans le formulaire
        $this->getForm()->handleRequest($this->getRequest());
        //on vérifie la validité des données saisies
        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $projet_repo = $this->getEm()->getRepository('MesClicsEspaceClientBundle:Projet');
            $projet = $projet_repo->find($this->getForm()->get('projetAssocie')->getData());
            $projet->setContrat(null);
            $this->setResult($this->getEm()->flush());
            $this->setSuccess(true);
        }
        if($addNotification){
            $this->setNotification();
        }
        return $this;
    }
}