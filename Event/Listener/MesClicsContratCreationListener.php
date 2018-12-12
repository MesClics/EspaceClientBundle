<?php

namespace MesClics\EspaceClientBundle\Event\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\ContratNumerator\MesClicsContratNumerator;

class MesClicsContratCreationListener{

        private $contratNumerator;

        public function __construct(MesClicsContratNumerator $contratNumerator){
            $this->contratNumerator = $contratNumerator;
        }

        public function postPersist(LifeCycleEventArgs $args){
            $entity = $args->getObject();
            //on vérifie que notre objet est bien une instance de contrat
            if(!$entity instanceof Contrat){
            return;
            }
            $em = $args->getObjectManager();

            $entity = $this->contratNumerator->numeroAuto($entity, $em);
        }
}