<?php
namespace MesClics\EspaceClientBundle\Form\DTO;

use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayIterableItem;

class ContratDTO extends DataTransportObjectToEntity{
    private $client;
    private $type;
    private $date_signature;
    private $last_update;

    public function __construct(Client $client = null){
        prent::__construct();
        if($client){
            $this->client = $client;
        }
    }

    public function getMappingArray(){
        return array(
            "client" => new MappingArrayItem("client", array("getClient", "setClient")),
            "type" => new MappingArrayItem("type", array("getType", "setType")),
            "date_signature" => new MappingArrayItem("date_signature", array("getDateSignature", "setDateSignature"))
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

    public function setDateSignature(\DateTime $date = null){
        $this->date_signature = $date;
    }

    public function getDateSignature(){
        return $this->date_signature;
    }

    public function setLastUpdate(\DateTime $date){
        $this->last_update = $date;
    }

    public function getProjets(){
        return $this->projets;
    }
}