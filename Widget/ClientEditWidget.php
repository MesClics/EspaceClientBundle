<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ClientType;
use MesClics\EspaceClientBundle\Form\DTO\ClientDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientEditWidgetHandler;

class ClientEditWidget extends Widget{
    private $client;
    private $form;
    
    public function __construct(Client $client, ClientEditWidgetHandler $handler){
        $this->client = $client;
        $this->handler = $handler;
        $dto = new ClientDTO();
        $dto->mapFrom($this->client);

        $this->form = $this->createForm(ClientType::class, $dto);
    }

    public function getClient(){
        return $this->client;
    }

    public function getForm(){
        return $this->form;
    }
    
    public function getName(){
        return 'client_edit';
    }

    public function getVariables(){
        return null;
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-datas.html.twig';
    }
}