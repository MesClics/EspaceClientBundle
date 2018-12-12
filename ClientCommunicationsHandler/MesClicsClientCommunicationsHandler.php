<?php

namespace MesClics\EspaceClientBundle\ClientCommunicationsHandler;

use MesClics\UtilsBundle\ApisManager\RestApi\TrelloApi\TrelloApi;

class MesClicsClientCommunicationsHandler{
    private $trello_api;

    public function __construct(TrelloApi $trello_api){
        $this->trello_api = $trello_api;
    }

    public function getTrelloApi(){
        return $this->trello_api;
    }
}