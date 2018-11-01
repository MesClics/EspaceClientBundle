<?php

namespace MesClics\EspaceClientBundle\ClientCommunicationsHandler;

use MesClics\EspaceClientBundle\ClientCommunicationsHandler\RestApiConsumer;

class MesClicsClientCommunicationsHandler{
    private $trello_api_consumer;

    public function __construct($options){
        if($options['trello_api']){
            $this->setTrelloApiConsumer($options['trello_api']);
        }
    }

    public function setTrelloApiConsumer($trello_options){
        $this->trello_api_consumer = new MesClicsTrelloRestApiConsumer($trello_options);
    }

    public function getTrelloApiConsumer(){
        return $this->trello_api_consumer;
    }
}