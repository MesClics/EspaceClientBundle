<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Projet
 *
 * @ORM\Table(name="mesclics_projet")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=55, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=60, nullable=true)
     * @Assert\Length(min= 3, minMessage="Le nom du projet doit contenir au moins 3 caractères", max = 30, maxMessage="Le nom du projet est trop long")
     */
    private $nom;

    /**
     * @var bool
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    private $closed = false;

    /**
     * @ORM\ManyToOne(targetEntity="MesClics\EspaceClientBundle\Entity\Client", inversedBy="projets")
     * @Assert\Valid();
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="MesClics\EspaceClientBundle\Entity\Contrat", inversedBy="projets")
     */
    private $contrat;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Projet
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Projet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    
    /**
     * Set closed
     *
     * @param boolean $closed
     *
     * @return Projet
     */
    public function setIsClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return bool
     */
    public function IsClosed()
    {
        return $this->closed;
    }

    /**
     * Set client
     *
     * @param \MesClics\EspaceClientBundle\Entity\Client $client
     *
     * @return Projet
     */
    public function setClient(\MesClics\EspaceClientBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \MesClics\EspaceClientBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set contrat
     *
     * @param \MesClics\EspaceClientBundle\Entity\Contrat $contrat
     *
     * @return Projet
     */
    public function setContrat(\MesClics\EspaceClientBundle\Entity\Contrat $contrat = null)
    {
        $this->contrat = $contrat;

        return $this;
    }

    /**
     * Get contrat
     *
     * @return \MesClics\EspaceClientBundle\Entity\Contrat
     */
    public function getContrat()
    {
        return $this->contrat;
    }

    /**
     * label pour liste dans select
     */
    public function getSelectLabel(){
        return $this->nom .  ' (' . $this->type . ')';
    }
}
