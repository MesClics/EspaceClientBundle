<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientEditWidget;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientEditWidgetHandler;

class ClientEditWidgets extends WidgetsContainer{
    protected $client_edit_handler;
    
    public function __construct(ClientEditWidgetHandler $cewh){
        parent::__construct();        
        $this->client_edit_handler = $cewh;
    }

    public function initialize($params = array()){
        $this
            ->addWidget(new ClientNavWidget($params['client']))
                ->getWidget('clients_nav')
                    ->addClasses(['oocss-discret', 'not-closable', 'small']);

        $this
            ->addWidget(new ClientEditWidget($params['client'], $this->client_edit_handler))
            ->getWidget('client_edit')->addClass("client-edit")
                ->addClasses(['medium', 'highlight', 'client-edit']);
    }
}