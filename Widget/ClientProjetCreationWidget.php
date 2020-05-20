<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Form\DTO\ProjetDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientProjetCreationWidgetHandler;


class ClientProjetCreationWidget extends Widget{
    private $client;
    private $form;

    public function __construct(Client $client, ClientProjetCreationWidgetHandler $handler){
        $this->client = $client;
        $this->handler = $handler;

        $dto = new ProjetDTO($client);
        $this->form = $this->createForm(ProjetType::class, $dto);

        $this->setTitle('Ajouter un projet');
        $this->setClass('client-projets-new');
    }

    public function getClient(){
        return $this->client;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return 'client_projet_creation';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-projets-new.html.twig';
    }

    public function getVariables(){
        return null;
    }
}