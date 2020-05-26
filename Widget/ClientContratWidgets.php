<?php

namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\EspaceClientBundle\Widget\ClientContratEditWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratEditWidgetHandler;

class ClientContratWidgets extends WidgetsContainer{
    protected $contrat_edit_handler;

    public function __construct(ClientContratEditWidgetHandler $ceh){
        $this->contrat_edit_handler = $ceh;
    }
    
    public function initialize($params = array()){
        $this->addWidget(new ClientNavWidget($params['client']));
        $this->addWidget(new ClientContratEditWidget($params['contrat'], $this->contrat_edit_handler));

        $cew = $this->getWidget('client_contrat');
        $cew->setTitle('modifier le contrat n°' . $cew->getContrat()->getNumero())
            ->addClass('highlight medium');
    }
}