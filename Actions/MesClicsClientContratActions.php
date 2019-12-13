<?php

namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Contrat;

final class MesClicsClientContratActions{
    public static function onCreation(Contrat $contrat){
        $label = "ajout d'un nouveau contrat de type " . $contrat->getType() . " pour le client " . $contrat->getClient()->getNom() . "(" . $contrat->getClient()->getNumero() . ")";
        if($contrat->getDateSignature()){
            $label .= " signé le " . $contrat->getDateSignature()->format('d/M/Y');
        }
        $label .= ".";

        $objects = array(
            "contrat" => $contrat
        );

        return new Action($label, $objects);
    }

    public static function onRemoval(Contrat $contrat){
        $label = "suppression du contrat n°" . $contrat->getNumero() . " pour le client " . $contrat->getClient()->getNom() . "(" . $contrat->getClient()->getNumero() . ").";
        $objects = array(
            "contrat" => clone $contrat
        );
        return new Action($label, $objects);
    }

    public static function onUpdate(Contrat $beforeUpdate, Contrat $afterUpdate){
        $label = "modification du contrat n°" . $afterUpdate->getNumero() . " pour le client " . $afterUpdate->getClient()->getNom() . "(" . $contrat->getClient()->getNumero() . ").";
        $objects = array(
            "before_update" => $beforeUpdate,
            "after_update" => $afterUpdate
        );

        return new Action($label, $objects);
    }
}