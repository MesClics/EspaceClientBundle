<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\EspaceClientBundle\Form\DTO\ClientDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientCreationWidgetHandler;

class ClientCreationWidget extends Widget{
    private $client;
    private $form;

    public function __construct(ClientCreationWidgetHandler $handler){
        $this->handler = $handler;

        $this->setTitle("ajouter un prospect ou un client");
        $this->setClass("client-new");

        $dto = new ClientDTO();
        $this->form = $this->createForm(ClientType::class, $dto);
    }

    public function setClient(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return 'client_creation';
    }

    public function getTemplate(){
        return "MesClicsEspaceClientBundle:Widgets:client-new.html.twig";
    }

    public function getVariables(){
        return null;
    }
}