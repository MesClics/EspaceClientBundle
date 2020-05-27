<?php
namespace MesClics\EspaceClientBundle\Widget;

use MesClics\UtilsBundle\Widget\Widget;
use MesClics\EspaceClientBundle\Entity\Contrat;
use Doctrine\Common\Collections\ArrayCollection;


class ClientProjetToContratWidget extends Widget{
    protected $contrat;
    protected $projets;

    public function __construct(Contrat $contrat = null){
        if($contrat){
            $this->contrat = $contrat;
        }
    }

    public function getContrat(){
        return $this->contrat;
    }

    public function setContrat(Contrat $contrat){
        $this->contrat = $contrat;
        return $this;
    }

    public function getProjets(){
        return $this->projets;
    }

    public function setProjets(ArrayCollection $projets){
        $this->projets = $projets;
    }

    public function getName(){
        return 'client_projet_to_contrat';
    }

    public function getTemplate(){
        return 'MesClicsEspaceClientBundle:Widget:client-contrat-projets.html.twig';
    }

    public function getVariables(){
        return null;
    }
}