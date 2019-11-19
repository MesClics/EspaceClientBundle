<?php
namespace MesClics\EspaceClientBundle\Event\Listener;

use MesClics\NavigationBundle\Navigator\Navigator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Actions\MesClicsClientContratActions;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratRemoveEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratCreationEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratSignatureEvent;

class MesClicsClientContratSubscriber implements EventSubscriberInterface{

    private $session;
    private $navigator;

    public function __construct(SessionInterface $session, Navigator $navigator){
        $this->session = $session;
        $this->navigator = $navigator;
    }

    public static function getSubscribedEvents(){
        return array(
            MesClicsClientContratEvents::CREATION => "onCreation",
            MesClicsClientContratEvents::UPDATE => "onUpdate",
            MesClicsClientContratEvents::REMOVAL => "onRemoval",
            MesClicsClientContratEvents::SIGNATURE => "onSignature"
        );
    }

    public function addFlash(string $label, string $message){
        $this->session->getFlashBag()->add($label, $message);
    }

    public function onCreation(MesClicsClientContratCreationEvent $event){
        dump($event); die();
    }

    public function onUpdate(MesClicsClientContratUpdateEvent $event){
        // TODO : add a flash message
        dump($event); die();
    }

    public function onRemoval(MesClicsClientContratRemoveEvent $event){
        //ADD A FLASH MESSAGE
        $label = "success";
        $message = "Le contrat n°" . $event->getContrat()->getNumero() . " a bien été supprimé.";
        $this->addFlash($label, $message);

        //ADD ACTION TO NAVIGATOR
        $action = MesClicsClientContratActions::onRemoval($event->getContrat());
        $this->navigator->getUser()->getChronology()->addAction($action);
    }

    public function onSignature(MesClicsClientContratSignatureEvent $event){
        dump($event); die();
    }
}