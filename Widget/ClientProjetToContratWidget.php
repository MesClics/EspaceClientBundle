<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Projet;
use MesClics\EspaceClientBundle\Entity\Contrat;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\AdminBundle\Widget\Handler\BasicWidgetHandler;


class ClientProjetToContratWidget extends Widget{
    protected $contrat;
    protected $unattached_projets;

    public function __construct(Contrat $contrat = null, BasicWidgetHandler $handler){
        $this->handler = $handler;
        if($contrat){
            $this->contrat = $contrat;
        }

        
        // // check if there are some unattached projects for this client
        $this->unattached_projets = $this->handler->entity_manager->getRepository(Projet::class)->getProjetsWithNoContrat($contrat->getClient());
    }

    public function getContrat(){
        return $this->contrat;
    }

    public function setContrat(Contrat $contrat){
        $this->contrat = $contrat;
        return $this;
    }

    public function getUnattachedProjets(){
        return $this->unattached_projets;
    }

    public function getName(){
        return 'client_projet_to_contrat';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widgets:client-contrat-projets.html.twig';
    }

    public function getVariables(){
    }
}