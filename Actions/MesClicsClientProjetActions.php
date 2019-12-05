<?php

namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;

class MesClicsClientProjetActions{
    public function removal(Projet $projet){
        $label = "suppression du projet " . $projet->getNom() . " pour le client " . $projet->getClient()->getNom() . " (" . $projet->getClient()->getNumero() .").";
        $objects = array(
            "projet" => clone $projet
        );

        return new Action($label, $objects);
    }

    public function attachment(Projet $projet, Contrat $contrat){
        $label = "association du projet " . $projet->getNom() . " au contrat " . $contrat->getType() . " n°" . $contrat->getNumero() . " pour le client " . $projet->getClient()->getNom() . " (" . $projet->getClient()->getNumero() . ").";
        $objects = array(
            "projet" => $projet,
            "contrat" => $contrat
        );

        return new Action($label, $objects);
    }

    public function detachment(Projet $projet, Contrat $contrat){
        $label = "dissociation du projet " . $projet->getNom() . " et du contrat " . $contrat->getType() . " n°" . $contrat->getNumero() . " pour le client " . $projet->getClient()->getNom() . " (" . $projet->getClient()->getNumero() . ").";
        $objects = array(
            "projet" => $projet,
            "contrat" => $contrat
        );

        return new Action($label, $objects);
    }
}