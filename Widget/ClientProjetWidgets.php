<?php

namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\EspaceClientBundle\Widget\ClientProjetEditWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientProjetEditWidgetHandler;

class ClientProjetWidgets extends WidgetsContainer{
    protected $projet_widget_handler;

    public function __construct(ClientProjetEditWidgetHandler $pwh){
        parent::__construct();
        $this->projet_widget_handler = $pwh;
    }

    public function initialize($params = array()){
        $this->addWidget(new ClientNavWidget($params['client']));
        $this->addWidget(new ClientProjetEditWidget($params['projet'], $this->projet_widget_handler));

        $this->getWidget('client_projet')->addClasses(['highlight', 'client-projet-edit']);
        $this->getWidget('client_projet')->setTitle('Modifier le projet');
    }
}