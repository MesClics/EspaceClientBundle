<?php

namespace MesClics\EspaceClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use MesClics\EspaceClientBundle\Entity\Contrat;

class MesClicsClientContratSignatureEvent extends Event{
    private $contrat;

    public function __construct(Contrat $contrat){
        $this->contrat = $contrat;
    }
    
    public function getContrat(){
        return $this->contrat;
    }
}