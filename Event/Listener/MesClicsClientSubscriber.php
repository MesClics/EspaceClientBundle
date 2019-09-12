<?php

namespace MesClics\EspaceClientBundle\Event\Listener;

use Doctrine\ORM\EntityManagerInterface;
use MesClics\NavigationBundle\Entity\Action;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use MesClics\NavigationBundle\Navigator\Navigator;
use MesClics\EspaceClientBundle\Event\MesClicsClientEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use MesClics\EspaceClientBundle\Event\MesClicsClientUpdateEvent;
use MesClics\EspaceClientBundle\Event\MesClicsClientCreationEvent;
use MesClics\EspaceClientBundle\ClientNumerator\MesClicsClientNumerator;

class MesClicsClientSubscriber implements EventSubscriberInterface{
    private $clientNumerator;
    private $apis_manager;
    private $navigator;
    private $entity_manager;
    private $postUpdated = false;
    private $prospectHasChanged = false;
    private $nameStartHasChanged = false;

    public function __construct(MesClicsClientNumerator $client_numerator, ApisManager $apis_manager, Navigator $navigator, EntityManagerInterface $em){
        $this->clientNumerator = $client_numerator;
        $this->apis_manager = $apis_manager;
        $this->navigator = $navigator;
        $this->entity_manager = $em;
    }

    public static function getSubscribedEvents(){
        return array(
            MesClicsClientEvents::CREATION => 'onCreation',
            MesClicsClientEvents::UPDATE => 'onUpdate',
            MesClicsClientEvents::REMOVAL => 'onRemoval'
        );
    }

    public function onCreation(MesClicsClientCreationEvent $event){
        $client = $event->getClient();
        //SET NUMERO AUTO
        if(!$client->hasNumero()){
            $this->clientNumerator->numeroAuto($client);
        }

        // execute trello actions
        $this->trelloActionsOnClientCreation($client);
        // $this->executeTrelloActions("postPersist", array("client" => $client, "entity_manager" => $this->entity_manager));

        //add a new action to Navigator's chronology
        $action = new Action("creation du client " . $client->getNom(), array("client" => $client));
        $this->navigator->getUser()->getChronology()->addAction($action);

        //flush changes in database
        $this->entity_manager->flush();
    }

    public function onUpdate(MesClicsClientUpdateEvent $event){
        // on prospect status change
        if($event->hasChanged('prospect')){
            //reset the client number
            if(!$event->getAfterUpdate()->isProspect()){
                $numero = $this->clientNumerator->prospectToClient($event->getAfterUpdate());
            } else{
                $numero = $this->clientNumerator->clientToProspect($event->getAfterUpdate());
            }            
            // $event->getAfterUpdate()->setNumero($numero);

            //TODO: update Trello card
            $this->trelloActionsOnClientProspectStatusChange($event->getBeforeUpdate(), $event->getAfterUpdate());
        }

        // on name change
        if($event->hasChanged('nom')){
            //if 3 first letters change, reset client number
            if(strtoupper(substr($event->getBeforeUpdate()->getNom(), 0, 3)) != strtoupper(substr($event->getAfterUpdate()->getNom(), 0, 3))){
                $numero = $this->clientNumerator->numeroAuto($event->getAfterUpdate());
                // $event->getAfterUpdate()->setNumero($numero);
                // $this->nameStartHasChanged = true;
            }

            // TODO: update Trello card
            $this->trelloActionsOnClientNameChange($event->getBeforeUpdate(), $event->getAfterUpdate());
        }

        $this->entity_manager->flush();
    }

    public function removal(Event $event){}

    private function trelloActionsOnClientCreation(Client $client){
        //ADD TRELLO LIST
        if(!$client->hasTrelloList()){
            $trello_api = $this->apis_manager->getApi('trello');
            //get clientsBoard id
            $board_options = array(
                'fields' => array(
                    'id',
                    'name'
                )
            );

            $board = $trello_api->getBoardByName('CLIENTS', $board_options)->id;

            //set datas to add to the trello list that will be created for this new client
            $datas = array(
                'name' => $client->getNumero() . ' : ' . strtoupper($client->getNom())
            );
            $list = $trello_api->addList($board, $datas);
        }

        // ADD INITIAL CARDS
        //CARD INFOS
        $infos_card_datas = array(
            "name" => "INFOS",
            "idList" => $list->id,
            "pos" => "top",
            "desc" => "> numero\xA\xA".$client->getNumero()."\xA\xA ---"
        );

        $info_card = $trello_api->addCard($infos_card_datas);

        // ADD LOGO AS ATTACHMENT
        if($client->getImage()){
            $attachment = array(
                "name" => "logo_".$client->getNom(),
                "mimeType" => "image/png",
                "url" => $client->getImage()->getUrl()
            );
            $trello_api->addAttachmentToCard($info_card->id, $attachment);
        }

        //CARD CHRONO
        $chrono_card_datas = array(
            "name" => "CHRONO",
            "idList" => $list->id,
            "pos" => "bottom",
            "desc" => "chronologie des événéments liés au client"
        );
        $chrono_card = $trello_api->addCard($chrono_card_datas);

        //ADD CHECKLIST EVENEMENTS CLIENT
        $evts_client_checklist_datas = array(
            "idCard" => $chrono_card->id,
            "name" => "EVENEMENTS COMPTE CLIENT",
            "pos" => "top"
        );
        $evts_client_checklist = $trello_api->addChecklist($evts_client_checklist_datas);
        
        //add creation du compte client comme item
        $creation_compte_client_item = array(
            "name" => "ouverture du compte client"
        );
        $trello_api->addItemToChecklist($evts_client_checklist->id, $creation_compte_client_item);

        //ADD CHECKLIST COMMUNICATIONS
        $comm_client_checklist_datas = array(
            "idCard" => $chrono_card->id,
            "name" => "COMMUNICATIONS",
            "pos" => "bottom"
        );
        $comm_client_checklist = $trello_api->addChecklist($comm_client_checklist_datas);

        $client->setTrelloList($list);
        // $this->entity_manager->flush();
    }

    private function trelloActionsOnClientProspectStatusChange(Client $before_update, Client $after_updatde){
        dump("execute some actions on trello because the client's prospect status has changed");
    }

    private function trelloActionsOnClientNameChange(Client $before_update, Client $after_update){
        dump("execute some actions on trello because the client's name has changed");
    }
}