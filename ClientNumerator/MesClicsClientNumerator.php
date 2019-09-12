<?php

namespace MesClics\EspaceClientBundle\ClientNumerator;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\EspaceClientBundle\Entity\Client;

class MesClicsClientNumerator{
    private $entity_manager;

    public function __construct(EntityManagerInterface $em){
        $this->entity_manager = $em;
    }

    public function numeroAuto($client){

        //on définit les initiales à rechercher dans la bdd
        $repo = $this->entity_manager->getRepository("MesClicsEspaceClientBundle:Client");
        $initials = substr($client->getAlias(), 0, 3);
        $last_numero_client = $repo->lastClientBeginningWith($initials);
        if($last_numero_client){
            $last_numero_full = $last_numero_client->getNumero();
        }

        $next_numero = '001';
        
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
        
        $client->setNumero($next_numero_full);
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
        
        $client->setNumero($numero);
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

        $client->setNumero($numero);
        return $numero;
    }
}