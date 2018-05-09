<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Typo
 *
 * @ORM\Table(name="mesclics_typo")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\TypoRepository")
 */
class Typo
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="fontWeight", type="string", length=255, nullable=true)
     */
    private $fontWeight;

    /**
     * @var string
     *
     * @ORM\Column(name="fontSize", type="string", length=255, nullable=true)
     */
    private $fontSize;

    /**
     * @var string
     * 
     * @ORM\ManyToOne(targetEntity = "MesClics\EspaceClientBundle\Entity\CharteGraphique", cascade={"persist", "remove"}, inversedBy="typos")
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
     * @return Typo
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
     * Set url
     *
     * @param string $url
     *
     * @return Typo
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set fontWeight
     *
     * @param string $fontWeight
     *
     * @return Typo
     */
    public function setFontWeight($fontWeight)
    {
        $this->fontWeight = $fontWeight;

        return $this;
    }

    /**
     * Get fontWeight
     *
     * @return string
     */
    public function getFontWeight()
    {
        return $this->fontWeight;
    }

    /**
     * Set fontSize
     *
     * @param string $fontSize
     *
     * @return Typo
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;

        return $this;
    }

    /**
     * Get fontSize
     *
     * @return string
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }
}

