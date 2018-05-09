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

    // public function setErrorNotification(){
    //     if($this->getResult() > 1){
    //         $this->errorNotification = 'Les contrats n\'ont pas pu être ajoutés. Veuillez vérifier les données saisies';
    //     } else{
    //         $this->errorNotification = 'Le contrat n\'a pas pu être ajouté. Veuillez vérifier les données saisies';
    //     }
    // }

    // public function setErrorRedirection(){
    //     $this->errorRedirection = null;
    // }

    // public function setSuccessNotification(){
    //     if($this->getResult() > 1){
    //         $this->successNotification = 'Les contrats ont bien été ajoutés.';
    //     } else{
    //         $this->successNotification = 'Le contrat a bien été ajouté sous le numéro : ' . $this->getResult()->getNumero();
    //     }
    // }

    // public function setSuccessRedirection(){
    //     $this->successRedirection = null;
    // }

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
            //on associe le client au contrat
            $object->setClient($this->getClient());
            //on persiste notre objet en bdd
            $this->getEm()->persist($object);
            $this->setResult($this->getEm()->flush());
            $this->setSuccess(true);
        }

        if($addNotification){
            $this->setNotification();
        }

        return $this;
    }
}