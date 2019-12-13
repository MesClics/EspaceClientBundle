<?php

namespace MesClics\EspaceClientBundle\Form\DTO;

use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class ProjetDTO extends DataTransportObjectToEntity{

    private $client;
    private $type;
    private $nom;
    private $isClosed;

    public function __construct(Client $client = null){
        parent::__construct();
        if($client){
            $this->client = $client;
        }
        $this->isClosed = false;
    }

    public function getMappingArray(){
        return array(
            'client' => new MappingArrayItem("client", array("getClient", "setClient")),
            'type' => new MappingArrayItem("type", array("getType", "setType")),
            'nom' => new MappingArrayItem("nom", array("getNom", "setNom")),
            'isClosed' => new MappingArrayItem("isClosed", array("isClosed", "setIsClosed"))
        );
    }

    public function setClient(Client $client){
        $this->client = $client;
    }

    public function getClient(){
        return $this->client;
    }

    public function setType(string $type){
        $this->type = $type;
    }

    public function getType(){
        return $this->type;
    }

    public function setNom(string $nom){
        $this->nom = $nom;
    }

    public function getNom(){
        return $this->nom;
    }

    public function setIsClosed(bool $closed){
        $this->isClosed = $closed;
    }

    public function isClosed(){
        return $this->isClosed;
    }
}