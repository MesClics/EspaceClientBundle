<?php
namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Widget\ClientsListWidget;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class ClientsListWidgetHandler extends WidgetHandler{
    protected $clients;

    public function handleRequest(Widget $widget, Request $request){
        if(! $widget instanceof ClientsListWidget){
            throw new InvalidArgumentException(__METHOD__." expects first argument to be an instance of " . ClientsListWidget::class . ", instance of " . get_class($widget) . " given.");
        }
        // TODO: do whatever we need
        return;
    }

    public function getClients(bool $force = false){
        // at first call or if clients is null
        if(!$this->clients || $force){
            $this->clients = $this->entity_manager->getRepository('MesClicsEspaceClientBundle:Client')->getClientsList();
        }

        return $this->clients;
    }
}