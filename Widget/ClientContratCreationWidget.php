<?php

namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\EspaceClientBundle\Form\DTO\ContratDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratCreationWidgetHandler;

class ClientContratCreationWidget extends Widget{
    protected $client;
    protected $form;

    public function __construct(Client $client, ClientContratCreationWidgetHandler $handler){
        $this->handler = $handler;
        $this->client = $client;
        $dto = new ContratDTO($client);
        $this->form = $this->createForm(ContratType::class, $dto);

        $this->addClass('client-contrats-new');
        $this->setTitle('ajouter un contrat');
    }

    public function getClient(){
        return $this->client;
    }

    public function setClient(Client $client){
        $this->client = $client;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return 'client_contrat_creation';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-contrats-new.html.twig';
    }

    public function getVariables(){
    }
}