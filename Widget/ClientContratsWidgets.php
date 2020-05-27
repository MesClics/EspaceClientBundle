<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\WidgetsContainer;
use MesClics\EspaceClientBundle\Widget\ClientNavWidget;
use MesClics\EspaceClientBundle\Widget\ClientContratsStartWidget;
use MesClics\EspaceClientBundle\Widget\ClientContratCreationWidget;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratCreationWidgetHandler;

class ClientContratsWidgets extends WidgetsContainer{
    protected $contrat_creation_handler;

    public function __construct(ClientContratCreationWidgetHandler $cch){
        $this->contrat_creation_handler = $cch;
    }

    public function initialize($params = array()){
        $this
            ->addWidget(new ClientNavWidget($params['client']))
                ->getWidget('clients_nav')
                    ->addClasses(['oocss-discret', 'not-closable', 'small']);
        
        if(!$params['client']->getContrats()->isEmpty()){
            $this
                ->addWidget(new ClientContratsListWidget($params['client']))
                    ->getWidget('client_contrats')
                        ->addClasses(['large', 'client-contrats'])
                        ->setTitle('contrats');
        } else{
            // TODO: add contrats infos widget
            $this
                ->addWidget(new ClientContratsStartWidget($params['client']))
                    ->getWidget('client_contrats_info')
                        ->addClasses(['medium', 'highlight2', 'client-contrats-info'])
                        ->setTitle('par où commencer ?');
        }

        $this
            ->addWidget(new ClientContratCreationWidget($params['client'], $this->contrat_creation_handler))
                ->getWidget('client_contrat_creation')
                    ->addClasses(['small', 'highlight', 'client-contrats-new'])
                    ->setTitle('ajouter un contrat')
                    ->addVariable('isSlideshow', true);
    }
}