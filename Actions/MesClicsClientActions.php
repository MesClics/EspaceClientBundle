<?php
namespace MesClics\EspaceClientBundle\Actions;

use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Client;

class MesClicsClientActions{
    public static function creation(Client $client){
        $label = "creation du client " . $client->getNom();
        $objects = array (
            "client" => $client
        );
        
        return new Action($label, $objects);
    }

    public static function nameChange(Client $before_update, Client $after_update){
        $label = "changement du nom du client " . $before_update->getNom() . " en " . $after_update->getNom();
        $objects = array(
            "clientBeforeUpdate" => $before_update,
            "clientAfterUpdate" => $after_update
        );

        return new Action($label, $objects);
    }


    public static function prospectToClient(Client $before_update, Client $after_update){
        $label = "changement du statut \"prospect\" pour " . $before_update->getNom() . " en statut \"client\"";
        $objects = array(
            "client" => $after_update
        );

        return new Action($label, $objects);
    }

    public static function clientToProspect(Client $before_update, Client $after_update){
        $label = "changement du statut \"client\" pour " . $before_update->getNom() . " en statut \"prospect\"";
        $objects = array(
            "client" => $after_update
        );

        return new Action($label, $objects);
    }

    public static function removal(Client $client){
        if($client->isProspect()){
            $statut = "prospect";
        } else{
            $statut = "client";
        }
        $label = "suppression du " . $statut . " " . $client->getNom();
        $objects = array(
            "client" => $client
        );

        return new Action($label, $objects);
    }
}
