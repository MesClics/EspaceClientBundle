<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientsListWidget;
use MesClics\EspaceClientBundle\Widget\ClientCreationWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientsListWidgetHandler;
use MesClics\EspaceClientBundle\Widget\Handler\ClientCreationWidgetHandler;

class ClientsHomeWidgets extends WidgetsContainer{
    private $client_creation_handler;
    private $clients_list_handler;

    public function __construct(ClientsListWidgetHandler $clwh, ClientCreationWidgetHandler $ccwh){
        parent::__construct();
        $this->clients_list_handler = $clwh;
        $this->client_creation_handler = $ccwh;
    }

    public function initialize($params = array()){
        $this->addWidget(new ClientsListWidget($this->clients_list_handler));
        $this->addWidget(new ClientCreationWidget($this->client_creation_handler));
    }
    
    public function getClients(){
        return $this->clients_list_handler->getClients();
    }
}