<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;

class ClientContratsStartWidget extends Widget{
    protected $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    public function setClient(Client $client){
        $this->client = $client;
        return $this;
    }

    public function getClient(){
        return $this->client;
    }

    public function getName(){
        return 'client_contrats_info';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-contrats-start.html.twig';
    }

    public function getVariables(){
        return null;
    }
}