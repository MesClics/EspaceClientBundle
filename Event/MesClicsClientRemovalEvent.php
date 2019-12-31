<?php
namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use MesClics\EspaceClientBundle\Entity\Client;

class MesClicsClientRemovalEvent extends Event{
    private $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }
}