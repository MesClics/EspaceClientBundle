<?php

namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Widget\ClientContratEditWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratUpdateEvent;

class ClientContratEditWidgetHandler extends WidgetHandler{
    public function handleRequest(Widget $widget, Request $request){
        if(! $widget instanceof ClientContratEditWidget){
            throw new InvalidArgumentException(__METHOD__ .' first argument must be an instance of '. ClientContratEditWidget::class .  ', instance of '. get_class($widget) . ' given instead');
        }

        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $before_update = clone $widget->getContrat();
                $dto = $widget->getForm()->getData();
                
                if($before_update !== $dto){
                    $dto->mapTo($widget->getContrat());

                    $event = new MesClicsClientContratUpdateEvent($before_update, $widget->getContrat());
                    $this->event_dispatcher->dispatch(MesClicsClientContratEvents::UPDATE, $event);
                    $this->entity_manager->flush();

                    return $widget;
                }
            }
        }
    }
}