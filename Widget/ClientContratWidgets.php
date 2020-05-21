<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\EspaceClientBundle\Widget\ClientContratCreationWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratCreationWidgetHandler;

class ClientContratWidgets extends WidgetsContainer{
    protected $contrat_creation_handler;

    public function __construct(ClientContratCreationWidgetHandler $cch){
        $this->contrat_creation_handler = $cch;
    }

    public function initialize($params = array()){
        $this
            ->addWidget(new ClientNavWidget($params['client']))
            ->addWidget(new ClientContratCreationWidget($params['client'], $this->contrat_creation_handler));

        $this
            ->getWidget('client_contrat_creation')
                ->addClass('small')
                ->addVariable('isSlideshow', true);
    }
}