<?php
namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetEvents;
use MesClics\EspaceClientBundle\Widget\ClientProjetCreationWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientProjetCreationEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class ClientProjetCreationWidgetHandler extends WidgetHandler{
    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof ClientProjetCreationWidget){
            throw new InvalidArgumentException(__METHOD__ . 'requests first argument to be an instance of ' . ClientProjetCreationWidget::class . ', instance of ' . get_class($widget) . ' given');
        }

        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

              if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $dto = $widget->getForm()->getData();
                $projet = new Projet();
                $this->entity_manager->persist($projet);
                $dto->mapTo($projet);
                $event = new MesClicsClientProjetCreationEvent($projet);
                $this->event_dispatcher->dispatch(MesClicsClientProjetEvents::CREATION, $event);
                $this->entity_manager->flush();

                return $projet;
            }
        }
    }
}