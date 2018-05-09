<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class ClientFormManager extends FormManager{
    
    const ERROR_NOTIFICATION_SINGULIER = "Le client n'a pas pu être ajouté. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "L'opération  a échoué. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le client a été ajouté.";
    const SUCCESS_NOTIFICATION_PLURIEL = "L'opération  a réussi.";

 
}