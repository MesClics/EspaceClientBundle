<?php
namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratEvents;
use MesClics\EspaceClientBundle\Widget\ClientContratCreationWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientContratCreationEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class ClientContratCreationWidgetHandler extends WidgetHandler{
    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof ClientContratCreationWidget){
            throw new InvalidArgumentException(__METHOD__ . ' first argument must be an instance of ' . ClientContratCreationWidget::class . ', instance of ' . get_class($widget) . ' given instead.');
        }

        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $dto = $widget->getForm()->getData();
                $contrat = new Contrat();
                $this->entity_manager->persist($contrat);
                $dto->mapTo($contrat);

                $event = new MesClicsClientContratCreationEvent($contrat);
                $this->event_dispatcher->dispatch(MesClicsClientContratEvents::CREATION, $event);
                $this->entity_manager->flush();

                return $contrat;
            }
        }
    }
}