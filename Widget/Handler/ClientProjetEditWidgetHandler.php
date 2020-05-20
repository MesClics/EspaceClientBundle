<?php

namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Widget\ClientProjetEditWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetUpdateEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class ClientProjetEditWidgetHandler extends WidgetHandler{
    public function handleRequest(Widget $widget, Request $request){
        if(! $widget instanceof ClientProjetEditWidget){
            throw new InvalidArgumentException(__METHOD__ . 'requests first argument to be an instance of ' . ClientProjetEditWidget::class . ', instance of ' . get_class($widget) . ' given instead');
        }
        
        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $before_update = clone $widget->getProjet();
                $dto = $widget->getForm()->getData();
                $dto->mapTo($widget->getProjet());

                if($widget->getProjet() !== $before_update){
                    $event = new MesClicsClientProjetUpdateEvent($before_update, $widget->getProjet());
                    $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::UPDATE, $event);
                    $this->entity_manager->flush();
                    return $this->redirectToRoute('mesclics_admin_client_projet', array('client_id' => $widget->getProjet()->getClient()->getId(), 'projet_id' => $widget->getProjet()->getId()));
                }
            }
        }
    }
}