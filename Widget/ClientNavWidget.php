<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;

class ClientNavWidget extends Widget{
    protected $client;
    
    public function __construct(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }
    
    public function getName(){
        return 'clients_nav';
    }

    public function getVariables(){
        return null;
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-nav.html.twig';
    }
}