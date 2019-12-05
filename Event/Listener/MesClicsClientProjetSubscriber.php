<?php
namespace MesClics\EspaceClientBundle\Event\Listener;

use MesClics\NavigationBundle\Navigator\Navigator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Actions\MesClicsClientProjetActions;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetAttachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetDetachEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetRemoveEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetCreationEvent;

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
            MesClicsClientProjetEvents::ATTACHMENT  => 'onAttachment',
            MesClicsClientProjetEvents::DETACHMENT  => 'onDetachment'
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

       $this->navigator->addAction($action);
    }

    public function onAttachment(MesClicsClientProjetAttachEvent $event){
        // add a flash Message
        $label = "success";
        $message = "Le projet " . $event->getProjet()->getType() . " intitulé " . $event->getProjet()->getNom() . " a bien été associé au contrat " . $event->getContrat()->getNumero();
        $this->addFlash($label, $message);
        // add action to Navigator's chronology
        $action = MesClicsClientProjetActions::attachment($event->getProjet(), $event->getContrat());

       $this->navigator->addAction($action);
    }

    public function onDetachment(MesClicsClientProjetDetachEvent $event){
        // add a flash message
        $label = "success";
        $message = "Le projet " . $event->getProjet()->getType() . " intitulé " . $event->getProjet()->getNom() . " a bien été dissocié du contrat " . $event->getContrat()->getNumero();
        $this->addFlash($label, $message);
        // add an action to Navigaotr's chronology
        $action = MesClicsClientProjetActions::detachment($event->getProjet(), $event->getContrat());
        $this->navigator->addAction($action);
    }
}