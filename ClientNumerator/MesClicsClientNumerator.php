<?php

namespace MesClics\EspaceClientBundle\ClientNumerator;

use Doctrine\ORM\EntityManager;
use MesClics\EspaceClientBundle\Entity\Client;

class MesClicsClientNumerator{

    // public function numeroAuto(Client $client, EntityManager $em, $nameStartHasChanged = false, $prospectHasChanged = false){
    //     //s'il ne s'agit pas d'un nouveau client et que son statut prospect a changé
    //     if($client->hasNumero() && $prospectHasChanged){
    //         //si le nom du client commence tjs par les mêmes lettres
    //         if(!$nameStartHasChanged){
    //             //si le client est transformé en prospect
    //             if($client->isProspect()){
    //                 return $this->clientToProspect($client);
    //             }
    //             //si le propect est transformé en client
    //             return $this->prospectToClient($client);
    //         }
    //     }

    //     //on récupère le dernier numéro de client commençant par les 4 premiers caractères ('_'inclus)
    //     //on extraie les initiales du client
    //     $initiales = strtoupper(substr($client->getAlias(), 0, 3));
    //     //on recherche le dernier client ayant les même initiales
    //     $lastClientNumero = $em->getRepository("MesClicsEspaceClientBundle:Client")->lastClientNumeroBeginningWith($initiales);
    //     var_dump($initiales, $lastClientNumero); die();

    //     if(!$lastClientNumero){
    //         $numeroDispo = '001';
    //     } else{
    //         //on incrémente le numéro si le début du nom a changé
    //         if($nameStartHasChanged){
    //             $lastNumero = intval(substr($lastClientNumero->getNumero(), 4));
    //             $numeroDispo = str_pad(($lastNumero + 1), 3, '000', STR_PAD_LEFT);
    //         } else{
    //             $numeroDispo = str_pad(intval(substr($client->getNumero(), 4)), 3, '000', STR_PAD_LEFT);
    //         }
    //     }
        
    //     if($client->isProspect()){
    //         return '_' . $initiales . $numeroDispo;
    //     } else{
    //         return $initiales . '_' . $numeroDispo;
    //     }
            
    // }

    public function numeroAuto($client, $em){     
        //s'il ne s'agit pas d'un nouveau client et que son statut prospect a changé
        // if($client->hasNumero() && $prospectHasChanged){
        //     //si le nom du client commence tjs par les mêmes lettres
        //     if(!$nameStartHasChanged){
        //         //si le client est transformé en prospect
        //         if($client->isProspect()){
        //             return $this->clientToProspect($client);
        //         }
        //         //si le propect est transformé en client
        //         return $this->prospectToClient($client);
        //     }
        // }

        //on définit les initiales à rechercher dans la bdd
        $repo = $em->getRepository("MesClicsEspaceClientBundle:Client");
        $initials = substr($client->getAlias(), 0, 3);
        $last_numero_full = $repo->lastClientBeginningWith($initials)->getNumero();

        $next_numero = '001';
        // $last_numero_full = $repo->lastClientNumeroBeginningWith($initial)->getNumero();
        if(isset($last_numero_full)){
            $last_numero = intval(substr($last_numero_full, 4));
        }
        if(isset($last_numero) && $last_numero >= $next_numero){
            $next_numero = $last_numero + 1;
        }

        $next_numero = str_pad($next_numero, 3, '000', STR_PAD_LEFT);
        if($client->isProspect()){
            $next_numero_full = '_'.strtoupper($initials).$next_numero;
        } else{
            $next_numero_full = strtoupper($initials).'_'.$next_numero;
        }
        
        return $next_numero_full;
    }

    public function prospectToClient(Client $client){
        //si le client est tjs un prospect on ne change pas son numéro
        if($client->isProspect()){
            return;
        }

        //on récupère le numéro du prospect
        $chiffres  = str_pad(substr($client->getNumero(), -1, 3), 3, '000', STR_PAD_LEFT);
        $initiales = substr($client->getNumero(), 1, 3);
        $numero = $initiales . '_' . $chiffres;
        
        return $numero;
    }

    public function clientToProspect(Client $client){
        //si le client est tjs un client non prospect, on ne change pas son numéro
        if(!$client->isProspect()){
            return;
        }

        //on récupère le numéro du client
        $chiffres  = str_pad(substr($client->getNumero(), -1, 3), 3, '000', STR_PAD_LEFT);
        $initiales = substr($client->getNumero(), 0, 3);
        $numero = '_' . $initiales . $chiffres;

        return $numero;
    }
}