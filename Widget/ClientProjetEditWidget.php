<?php

namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Form\ProjetType;
use MesClics\EspaceClientBundle\Form\DTO\ProjetDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientProjetEditWidgetHandler;

class ClientProjetEditWidget extends Widget{
    protected $projet;
    protected $form;

    public function __construct(Projet $projet, ClientProjetEditWidgetHandler $handler){
        $this->projet = $projet;
        $this->handler = $handler;

        $dto = new ProjetDTO();
        $dto->mapFrom($projet);
        $this->form = $this->createForm(ProjetType::class, $dto);
    }

    public function getProjet(){
        return $this->projet;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return 'client_projet';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-projet-edit.html.twig';
    }

    public function getVariables(){
        return null;
    }
}