<?php

namespace MesClics\EspaceClientBundle\ContratNumerator;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\EspaceClientBundle\Entity\Contrat;

class MesClicsContratNumerator{
    private $entity_manager;

    public function __construct(EntityManagerInterface $em){
        $this->entity_manager = $em;
    }

    public function numeroAuto(Contrat &$contrat){
        //le numéro contrat sera composé du numéro client sans '_' + date de création (Année, Mois, Jour) auqel on ajoute éventuellement un numero d'ordre (si plusieurs contrats créés le même jour)
        
        $numero = str_replace('_', '', $contrat->getClient()->getNumero());
        $date = date('Ymd', $contrat->getDateCreation()->getTimestamp());
        $temp_num = $numero . $date;
        $order = $this->entity_manager->getRepository('MesClicsEspaceClientBundle:Contrat')->getNumeroOrder($temp_num) + 1;
        $temp_num = $temp_num . $order;
                
        $contrat->setNumero($temp_num);
    }
}