<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;
use MesClics\UtilsBundle\Notification\Notification;

class ProjetDissocierContratFormManager extends FormManager{

    const ERROR_NOTIFICATION_SINGULIER = "Le contrat n'a pas pu être dissocié du projet. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les contrats n'ont pas pu être dissociés du projet. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le contrat a bien été dissocié du projet.";
    const SUCCESS_NOTIFICATION_PLURIEL = "LEs contrats ont bien été dissociés du projet.";

    public function handle(Form $form, $addNotification = true){
        $this->hydrate(array(
            'form' => $form
        ));
        $this->getForm()->handleRequest($this->getRequest());
        //on vérifie la validité des données saisies
        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $this->setAction($this->getForm()->getClickedButton()->getName());
            $object = $this->getForm()->getData();
            $object->setContrat(null);
            $this->setResult($this->getEm()->flush());
            $this->setSuccess(true);
        }
        if($addNotification){
            $this->setNotification();
        }
        return $this;
    }
}