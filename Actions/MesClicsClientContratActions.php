<?php

namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Contrat;

final class MesClicsClientContratActions{
    public static function onRemoval(Contrat $contrat){
        $label = "suppression du contrat n°" . $contrat->getNumero() . " pour le client " . $contrat->getClient()->getNom() . '.';
        $objects = array(
            "contrat" => clone $contrat
        );
        return new Action($label, $objects);
    }
}