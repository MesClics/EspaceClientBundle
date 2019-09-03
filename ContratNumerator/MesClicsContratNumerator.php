<?php

namespace MesClics\EspaceClientBundle\ContratNumerator;

use MesClics\EspaceClientBundle\Entity\Contrat;

class MesClicsContratNumerator{
    public function numeroAuto(Contrat $contrat, $em){
        //le numéro contrat sera composé du numéro client sans '_' + date de création (Année, Mois, Jour) auqel on ajoute éventuellement un numero d'ordre (si plusieurs contrats créés le même jour)
        
        $numero = str_replace('_', '', $contrat->getClient()->getNumero());
        $date = date('Ymd', $contrat->getDateCreation()->getTimestamp());
        $temp_num = $numero . $date;
        $order = $em->getRepository('MesClicsEspaceClientBundle:Contrat')->getNumeroOrder($temp_num) + 1;
        $temp_num = $temp_num . $order;
                
        $contrat->setNumero($temp_num);
        
        $em->flush();
        
        return $contrat;
    }
}