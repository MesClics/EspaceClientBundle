<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;

class ClientProjetsStartWidget extends Widget{
    protected $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }
    public function getName(){
        return 'client_projets_start';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-projets-start.html.twig';
    }

    public function getVariables(){
        return null;
    }
}