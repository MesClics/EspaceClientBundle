<?php

namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use MesClics\EspaceClientBundle\Entity\Client;

class MesClicsClientCreationEvent extends Event{
    
    protected $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }
}