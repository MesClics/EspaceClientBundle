<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use Doctrine\Common\Collections\ArrayCollection;


class ClientContratsListWidget extends Widget{
    protected $contrats;

    public function __construct(Client $client){
        $this->contrats = $client->getContrats();
    }

    public function getContrats(){
        return $this->contrats;
    }

    public function getName(){
        return 'client_contrats';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-contrats-list.html.twig';
    }

    public function getVariables(){
        return null;
    }
}