<?php
namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class MesClicsClientProjetCreationEvent extends Event{
    private $projet;

    public function __construct(Projet $projet){
        $this->projet = $projet;
    }

    public function getProjet(){
        return $this->projet;
    }
}