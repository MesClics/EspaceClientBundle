<?php
namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MesClicsClientProjetAssociationToContractEvent extends Event{
    private $projet;
    private $contrat;
    
    public function __construct(Projet $projet, Contrat $contrat){
        $this->projet = $projet;
        $this->contrat = $contrat;
    }

    public function getProjet(){
        return $this->projet;
    }

    public function getContrat(){
        return $this->contrat;
    }
}