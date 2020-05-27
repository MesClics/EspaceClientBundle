<?php

namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\AdminBundle\Widget\Handler\BasicWidgetHandler;
use MesClics\EspaceClientBundle\Widget\ClientContratEditWidget;
use MesClics\EspaceClientBundle\Widget\ClientProjetToContratWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratEditWidgetHandler;

class ClientContratWidgets extends WidgetsContainer{
    protected $basic_handler;
    protected $contrat_edit_handler;

    public function __construct(ClientContratEditWidgetHandler $ceh, BasicWidgetHandler $basic_handler){
        $this->basic_handler = $basic_handler;
        $this->contrat_edit_handler = $ceh;
    }
    
    public function initialize($params = array()){
        $this->addWidget(new ClientNavWidget($params['client']));
        $this->addWidget(new ClientContratEditWidget($params['contrat'], $this->contrat_edit_handler));

        if($params['client']->getProjets()){
            $this
                ->addWidget(new ClientProjetToContratWidget($params['contrat'], $this->basic_handler))
                ->getWidget('client_projet_to_contrat')
                    ->addClasses(['small', 'client-contrat-projets'])
                    ->setTitle('projets associés');
        }

        $cew = $this->getWidget('client_contrat');
        $cew->setTitle('modifier le contrat n°' . $cew->getContrat()->getNumero())
            ->addClass('highlight medium');
    }
}