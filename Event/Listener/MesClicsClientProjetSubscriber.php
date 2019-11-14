<?php
namespace MesClics\EspaceClientBundle\Event\Listener;

use MesClics\NavigationBundle\Navigator\Navigator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Actions\MesClicsClientProjetActions;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetRemoveEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetCreationEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetAssociationToContractEvent;

class MesClicsClientProjetSubscriber implements EventSubscriberInterface{
    protected $navigator;
    private $session;
    
    public function __construct(Navigator $navigator, SessionInterface $session){
        $this->navigator = $navigator;
        $this->session = $session;
    }
    
    public static function getSubscribedEvents(){
        return array(
            MesClicsClientProjetEvents::CREATION => 'onCreation',
            MesClicsClientProjetEvents::UPDATE  => 'onUpdate',
            MesClicsClientProjetEvents::REMOVAL  => 'onRemoval',
            MesClicsClientProjetEvents::ASSOCIATION_TO_CONTRACT  => 'onAssociationToContract'
        );
    }

    protected function addFlash(string $label, string $message){
            $this->session->getFlashBag()->add($label, $message);
    }

    public function onCreation(MesClicsClientProjetCreationEvent $event){
        dump($event); die();
    }

    public function onUpdate(MesClicsClientProjetUpdateEvent $event){
        dump($event); die();
    }

    public function onRemoval(MesClicsClientProjetRemoveEvent $event){
        //add a flash Message
        $label = "success";
        $message = "Le projet " . $event->getProjet()->getType() . " intitulé " . $event->getProjet()->getNom() . " a bien été supprimé.";
        $this->addFlash($label, $message);
        // add action to Navigator's chronology
        $action  = MesClicsClientProjetActions::removal($event->getProjet());

        $this->navigator->getUser()->getChronology()->addAction($action);
    }

    public function onAssociationToContract(MesClicsClientProjetAssociationToContractEvent $event){
        dump($event); die();
    }
}