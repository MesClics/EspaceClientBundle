<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;

class ClientEditFormManager extends FormManager{
    
    const ERROR_NOTIFICATION_SINGULIER = "Le client n'a pas pu être modifié. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Le client a été modifié.";
}