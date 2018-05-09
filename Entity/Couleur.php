<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Couleur
 *
 * @ORM\Table(name="mesclics_couleur")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\CouleurRepository")
 */
class Couleur
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
     * @ORM\Column(name="nom", type="string", length=55, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="hex", type="string", length=7, nullable=true)
     */
    private $hex;

    /**
     * @var string
     *
     * @ORM\Column(name="rgb", type="string", length=20, nullable=true)
     */
    private $rgb;

    /**
     * @var string //vaut true si la couleur est une couleur secondaire. par default vaut false
     * @ORM\Column(name="isSecondaire", type="boolean", nullable=false)
     */
    private $isSecondaire = false;

    /**
     * @var string
     * 
     * @ORM\ManyToOne(targetEntity = "MesClics\EspaceClientBundle\Entity\CharteGraphique", cascade={"persist", "remove"}, inversedBy="couleurs")
     */
    private $charte;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Couleur
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
     * Set hex
     *
     * @param string $hex
     *
     * @return Couleur
     */
    public function setHex($hex)
    {
        $this->hex = $hex;

        return $this;
    }

    /**
     * Get hex
     *
     * @return string
     */
    public function getHex()
    {
        return $this->hex;
    }

    /**
     * Set rgb
     *
     * @param string $rgb
     *
     * @return Couleur
     */
    public function setRgb($rgb)
    {
        $this->rgb = $rgb;

        return $this;
    }

    /**
     * Get rgb
     *
     * @return string
     */
    public function getRgb()
    {
        return $this->rgb;
    }
}

