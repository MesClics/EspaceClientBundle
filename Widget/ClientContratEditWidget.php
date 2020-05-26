<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\EspaceClientBundle\Entity\Contrat;
use MesClics\EspaceClientBundle\Form\ContratType;
use MesClics\EspaceClientBundle\Form\DTO\ContratDTO;
use MesClics\EspaceClientBundle\Widget\Handler\ClientContratEditWidgetHandler;

class ClientContratEditWidget extends Widget{
    protected $contrat;
    protected $form;

    public function __construct(Contrat $contrat, ClientContratEditWidgetHandler $handler){
        $this->contrat = $contrat;
        $this->handler = $handler;
        $dto = new ContratDTO();
        $dto->mapFrom($contrat);
        $this->form = $this->createForm(ContratType::class, $dto);
    }

    public function setContrat(Contrat $contrat){
        $this->contrat = $contrat;
        return $this;
    }

    public function getContrat(){
        return $this->contrat;
    }

    public function getForm(){
        return $this->form;
    }

    public function getName(){
        return 'client_contrat';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-contrat-edit.html.twig';
    }

    public function getVariables(){
        return null;
    }
}