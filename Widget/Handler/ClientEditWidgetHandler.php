<?php
namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Widget\ClientEditWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientEvents;
use MesClics\EspaceClientBundle\Event\MesClicsClientUpdateEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;


class ClientEditWidgetHandler extends WidgetHandler{

    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof ClientEditWidget)        {
            throw new InvalidArgumentException(__METHOD__.' expects first argument to be an instance of ' . ClientEditWidget::class . ', intance of ' . get_class($widget) . ' given.');
        }
    
        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $before_update = clone $widget->getClient();
                $clientDTO = $widget->getForm()->getData();
                $clientDTO->mapTo($widget->getClient());

                if($widget->getClient() !== $before_update){
                    $event = new MesClicsClientUpdateEvent($before_update, $widget->getClient());
                    $this->event_dispatcher->dispatch(MesClicsClientEvents::UPDATE, $event);
                    $this->entity_manager->flush();
                }

                return $client;
            }
        }
    }
}