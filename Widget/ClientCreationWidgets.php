<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientCreationWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientCreationWidgetHandler;

class ClientCreationWidgets extends WidgetsContainer{
    private $client_creation_handler;
    
    public function __construct(ClientCreationWidgetHandler $ccwh){
        parent::__construct();
        $this->client_creation_handler = $ccwh;
    }

    public function initialize($params = array()){
        $this
            ->addWidget(new ClientCreationWidget($this->client_creation_handler));
    }
}