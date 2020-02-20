<?php
namespace MesClics\EspaceClientBundle\Widget\Handler;

use MesClics\UtilsBundle\Widget\Widget;
use Symfony\Component\HttpFoundation\Request;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UtilsBundle\Widget\Handler\WidgetHandler;
use MesClics\EspaceClientBundle\Event\MesClicsClientEvents;
use MesClics\EspaceClientBundle\Widget\ClientCreationWidget;
use MesClics\EspaceClientBundle\Event\MesClicsClientCreationEvent;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

class ClientCreationWidgetHandler extends WidgetHandler{
    
    public function handleRequest(Widget $widget, Request $request){
        if(!$widget instanceof ClientCreationWidget){
            throw new InvalidArgumentException(__METHOD__ . ' expects first parameter to be an instance of ' . ClientCreationWidget::class . ", instance of " . get_class($widget) . " given." );
        }
        
        if($request->isMethod('POST')){
            $widget->getForm()->handleRequest($request);

            if($widget->getForm()->isSubmitted() && $widget->getForm()->isValid()){
                $dto = $widget->getForm()->getData();
                $client = new Client();
                $dto->mapTo($client);
                $this->entity_manager->persist($client);

                $event = new MesClicsClientCreationEvent($client);
                $this->event_dispatcher->dispatch(MesClicsClientEvents::CREATION, $event);
                $this->entity_manager->flush();

                return $this->redirectToRoute('mesclics_admin_client', array("client_id" => $client->getId()));
            }
        }
    }
}