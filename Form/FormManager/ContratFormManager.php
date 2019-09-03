<?php
namespace MesClics\EspaceClientBundle\Form\FormManager;

use MesClics\UtilsBundle\FormManager\FormManager;
use Symfony\Component\Form\Form;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Contrat;

class ContratFormManager extends FormManager{

    private $client;
    
    const ERROR_NOTIFICATION_SINGULIER = "Le contrat n'a pas pu être ajouté. Veuillez vérifier les données saisies.";
    const ERROR_NOTIFICATION_PLURIEL = "Les contrats n'ont pas pu être ajoutés. Veuillez vérifier les données saisies.";
    const SUCCESS_NOTIFICATION_SINGULIER = "Les contrat a été ajouté.";
    const SUCCESS_NOTIFICATION_PLURIEL = "Les contrats ont été ajoutés.";

    public function setClient(Client $client){
        $this->client = $client;
        return $this;
    }

    protected function getClient(){
        return $this->client;
    }

    public function handle(Form $form, $addNotification = true){
        $this->hydrate(array(
            'form' => $form
        ));

        $this->getForm()->handleRequest($this->getRequest());

        if($this->getForm()->isSubmitted() && $this->getForm()->isValid()){
            $this->setAction($this->getForm()->getClickedButton()->getName());
            $object = $this->getForm()->getData();
            //on persiste notre objet en bdd
            $this->getEm()->persist($object);
            $this->getEm()->flush();
            $this->setResult($object);
            $this->setSuccess(true);
        }

        if($addNotification){
            $this->setNotification();
        }

        return $this;
    }
}