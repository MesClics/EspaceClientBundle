<?php

namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use MesClics\EspaceClientBundle\Entity\Projet;

class MesClicsClientProjetRemoveEvent extends Event{
    private $projet;

    public function __construct(Projet $projet){
        $this->projet = $projet;
    }

    public function getProjet(){
        return $this->projet;
    }
}