<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientProjetsListWidget;
use MesClics\EspaceClientBundle\Widget\ClientProjetsStartWidget;
use MesClics\EspaceClientBundle\Widget\ClientProjetCreationWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientProjetCreationWidgetHandler;

class ClientProjetsWidgets extends WidgetsContainer{
    
    protected $projet_creation_handler;

    public function __construct(ClientProjetCreationWidgetHandler $pch){
        parent::__construct();
        $this->projet_creation_handler = $pch;
    }

    public function initialize($params = array()){
        $this->addWidget(new ClientNavWidget($params['client']));
        if($params['client']->getProjets()->isEmpty()){
            $this
                ->addWidget(new ClientProjetsStartWidget($params['client']))
                ->getWidget('client_projets_start')
                    ->addClasses(['client-projets-info', 'highlight2', 'medium'])
                    ->setTitle('par où commencer ?');
        } else{
            $this->addWidget(new ClientProjetsListWidget($params['client']));
        }
        $this->addWidget(new ClientProjetCreationWidget($params['client'], $this->projet_creation_handler));
        $this->getWidget('client_projet_creation')->addVariable('isSlideshow', true);
        $this->getWidget('client_projet_creation')->addClass('small');
    }
}