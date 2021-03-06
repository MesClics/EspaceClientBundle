<?php

namespace MesClics\EspaceClientBundle\Repository;

use MesClics\EspaceClientBundle\Entity\Client;

/**
 * ProjetRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjetRepository extends \Doctrine\ORM\EntityRepository
{
    public function getProjetWithContrat(){
        $qb = $this
        ->createQueryBuilder('projet')
        ->leftJoin('projet.contrat', 'contrat')
        ->addSelect('contrat')
        ;

        return $qb->getQuery()->getResult();
    }

    //QB methods for types
    public function getProjetsWithNoContratQB(Client $client){
        $qb = $this
        ->createQueryBuilder('projet')
        ->andWhere('projet.client = :client')
            ->setParameter('client', $client)
        ->andWhere('projet.contrat IS NULL')
        ;

        return $qb;
    }

    public function getProjetsWithNoContrat(Client $client){
        $qb = $this->getProjetsWithNoContratQB($client);

        return $qb->getQuery()->getResult();
    }
}
