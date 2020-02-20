<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientsListWidgetHandler;


class ClientsListWidget extends Widget{
    private $clients;

    public function __construct(ClientsListWidgetHandler $handler){
        $this->handler = $handler;
        $this->clients = $this->handler->getClients();
        $this->setTitle("liste des clients");
        $this->setClass("clients-list");
    }

    public function getClients(){
        return $this->clients;
    }

    public function getName(){
        return 'clients_list';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:clients-list.html.twig';
    }

    public function getVariables(){
        return null;
    }
}