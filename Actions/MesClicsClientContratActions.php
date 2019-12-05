<?php

namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Contrat;

final class MesClicsClientContratActions{
    public static function onRemoval(Contrat $contrat){
        $label = "suppression du contrat n°" . $contrat->getNumero() . " pour le client " . $contrat->getClient()->getNom() . ".";
        $objects = array(
            "contrat" => clone $contrat
        );
        return new Action($label, $objects);
    }

    public static function onUpdate(Contrat $beforeUpdate, Contrat $afterUpdate){
        $label = "modification du contrat n°" . $afterUpdate->getNumero() . " pour le client " . $afterUpdate->getClient()->getNom() . ".";
        $objects = array(
            "before_update" => $beforeUpdate,
            "after_update" => $afterUpdate
        );

        return new Action($label, $objects);
    }
}