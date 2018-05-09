<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;
use MesClics\UtilsBundle\Notification\Notification;

class ProjetAssocierContratFormManager extends FormManager{

    const ERROR_NOTIFICATION_SINGULIER = "Le contrat n'a pas pu être associé au projet. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les contrats n'ont pas pu être associés au projet. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le contrat a bien été associé au projet.";
    const SUCCESS_NOTIFICATION_PLURIEL = "LEs contrats ont bien été associés au projet.";
}