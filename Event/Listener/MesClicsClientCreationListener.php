<?php

namespace MesClics\EspaceClientBundle\Event\Listener;

use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UtilsBundle\ApisManager\ApisManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use MesClics\EspaceClientBundle\ClientNumerator\MesClicsClientNumerator;

class MesClicsClientCreationListener{
    private $clientNumerator;
    private $apis_manager;
    public $event_dispatcher;
    private $postUpdated = false;
    private $prospectHasChanged = false;
    private $nameStartHasChanged = false;

    public function __construct(MesClicsClientNumerator $client_numerator, ApisManager $apis_manager, EventDispatcher $event_dispatcher){
        $this->clientNumerator = $client_numerator;
        $this->apis_manager = $apis_manager;
        $this->event_dispatcher = $event_dispatcher;
    }

    private function setPostUpdated($updated){
        $this->postUpdated= $updated;
    }

    public function preUpdate(LifecycleEventArgs $args){
        if($args->hasChangedField('prospect')){
            $prospectHasChanged = true;
        }

        if($args->hasChangedField('nom')){
            if(strtoupper(substr($args->getOldValue('nom'), 0, 3)) != strtoupper(substr($args->getNewValue('nom'), 0, 3))){
                $this->nameStartHasChanged = true;
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args){
        $entity = $args->getObject();
        if(!$entity instanceof Client){
            return;
        }

        $em = $args->getObjectManager();
        //SET NUMERO AUTO
        if(!$entity->hasNumero()){
            $numero = $this->clientNumerator->numeroAuto($entity, $em);
            $entity->setNumero($numero);
        }
        // execute trello actions
        $this->executeTrelloActions("postPersist", array("client" => $entity, "entity_manager" => $em));

        //add an action to the Navigator
        $action = new Action("creation du client " . $entity->getNom(), array("client" => $entity));
        $this->navigator->getChronology()->addAction($action);
    }

    public function postUpdate(LifecycleEventArgs $args){
        if($this->postUpdated){
            return;
        }
        //si l'entité n'est pas une instance de client, ou si le changement ne concerne pas le statut prospect/client, on sort
        $entity = $args->getObject();
        if(!$entity instanceof Client && !$this->prospectHasChanged){
            return;
        }        
        $em = $args->getObjectManager();
        
        $this->setPostUpdated(true);
        $em->flush();
    }

    //TRELLO_ACTIONS
    public function executeTrelloActions(string $event, Array $args){
        $method = 'trello'.ucfirst($event);
        $this->$method($args);
    }

    public function trelloPostPersist(Array $args){
        $client = $args["client"];
        $em = $args["entity_manager"];
        
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
        $em->flush();
    }
}