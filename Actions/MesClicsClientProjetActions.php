<?php

namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Projet;

class MesClicsClientProjetActions{
    public function removal(Projet $projet){
        $label = "suppression du projet " . $projet->getNom() . " pour le client " . $projet->getClient()->getNom() . " (" . $projet->getClient()->getNumero() .").";
        $objects = array(
            "projet" => clone $projet
        );

        return new Action($label, $objects);
    }
}