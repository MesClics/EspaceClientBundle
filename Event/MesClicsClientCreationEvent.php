<?php

namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MesClicsClientCreationEvent extends Event{
    
    protected $client;

    public function __construct(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }
}