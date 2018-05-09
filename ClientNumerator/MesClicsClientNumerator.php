<?php
    namespace MesClics\EspaceClientBundle\ClientNumerator;

    use MesClics\EspaceClientBundle\Entity\Client;

    class MesClicsClientNumerator{
        public function numeroAuto(Client $client, $em, $nameStartHasChanged = false, $prospectHasChanged = false){
            //s'il ne s'agit pas d'un nouveau client et que son statut prospect a changé
            if($client->hasNumero() && $prospectHasChanged){
                //si le nom du client commence tjs par les mêmes lettres
                if(!$nameStartHasChanged){
                    //si le client est transformé en prospect
                    if($client->getProspect()){
                        return $this->clientToProspect($client);
                    }
                    //si le propect est transformé en client
                    return $this->prospectToClient($client);
                }
            }

            //on récupère le dernier numéro de client commençant par les 4 premiers caractères ('_'inclus)
            //on extraie les initiales du client
            $initiales = strtoupper(substr($client->getAlias(), 0, 3));
            //on recherche le dernier client ayant les même initiales
            $lastClientNumero = $em->getRepository("MesClicsEspaceClientBundle:Client")->lastClientNumeroBeginningWith($initiales);

            if(!$lastClientNumero){
                $numeroDispo = '001';
            } else{
                //on incrémente le numéro si le début du nom a changé
                if($nameStartHasChanged){
                    $lastNumero = intval(substr($lastClientNumero->getNumero(), 4));
                    $numeroDispo = str_pad(($lastNumero + 1), 3, '000', STR_PAD_LEFT);
                } else{
                    $numeroDispo = str_pad(intval(substr($client->getNumero(), 4)), 3, '000', STR_PAD_LEFT);
                }
            }
            
            if($client->getProspect()){
                return $numero = '_' . $initiales . $numeroDispo;
            } else{
                return $numero = $initiales . '_' . $numeroDispo;
            }
                
        }

        public function prospectToClient(Client $client){
            //si le client est tjs un prospect on ne change pas son numéro
            if($client->getProspect()){
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
            if(!$client->getProspect()){
                return;
            }

            //on récupère le numéro du client
            $chiffres  = str_pad(substr($client->getNumero(), -1, 3), 3, '000', STR_PAD_LEFT);
            $initiales = substr($client->getNumero(), 0, 3);
            $numero = '_' . $initiales . $chiffres;

            return $numero;
        }
    }