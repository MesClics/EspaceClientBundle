<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CharteGraphique
 *
 * @ORM\Table(name="mesclics_charte_graphique")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\CharteGraphiqueRepository")
 */
class CharteGraphique
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
     * @ORM\OneToOne(targetEntity="MesClics\EspaceClientBundle\Entity\Client", mappedBy="charte")
     */
    private $client;

    /**
     * @var string
     * 
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Couleur", mappedBy="charte")
     */
    private $couleurs;
   
    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Typo", mappedBy="charte")
     */
    private $typos;

    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\ElementGraphique", mappedBy="charte")
     */
    private $elementsGraphiques;
    
    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Support", mappedBy="charte")
     */
    private $supports;

    public function __construct(){
        $this->couleurs = new ArrayCollection();
        $this->typos = new ArrayCollection();
        $this->elementsGraphiques = new ArrayCollection();
        $this->supports = new ArrayCollection();
    }

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
     * Set client
     *
     * @param \MesClics\EspaceClientBundle\Entity\Client $client
     *
     * @return CharteGraphique
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
     * Add couleur
     *
     * @param \MesClics\EspaceClientBundle\Entity\Couleur $couleur
     *
     * @return CharteGraphique
     */
    public function addCouleur(\MesClics\EspaceClientBundle\Entity\Couleur $couleur)
    {
        $this->couleurs[] = $couleur;
        $couleur->setCharte($this);

        return $this;
    }

    /**
     * Remove couleur
     *
     * @param \MesClics\EspaceClientBundle\Entity\Couleur $couleur
     */
    public function removeCouleur(\MesClics\EspaceClientBundle\Entity\Couleur $couleur)
    {
        $this->couleurs->removeElement($couleur);
        $couleur->setCharte(null);
    }

    /**
     * Get couleurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCouleurs()
    {
        return $this->couleurs;
    }

    /**
     * Add typo
     *
     * @param \MesClics\EspaceClientBundle\Entity\Typo $typo
     *
     * @return CharteGraphique
     */
    public function addTypo(\MesClics\EspaceClientBundle\Entity\Typo $typo)
    {
        $this->typos[] = $typo;
        $typo->setCharte($this);

        return $this;
    }

    /**
     * Remove typo
     *
     * @param \MesClics\EspaceClientBundle\Entity\Typo $typo
     */
    public function removeTypo(\MesClics\EspaceClientBundle\Entity\Typo $typo)
    {
        $this->typos->removeElement($typo);
        $typo->setCharte(null);
    }

    /**
     * Get typos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTypos()
    {
        return $this->typos;
    }

    /**
     * Add elementsGraphique
     *
     * @param \MesClics\EspaceClientBundle\Entity\ElementGraphique $elementsGraphique
     *
     * @return CharteGraphique
     */
    public function addElementsGraphique(\MesClics\EspaceClientBundle\Entity\ElementGraphique $elementsGraphique)
    {
        $this->elementsGraphiques[] = $elementsGraphique;
        $elementGraphique->setCharte($this);

        return $this;
    }

    /**
     * Remove elementsGraphique
     *
     * @param \MesClics\EspaceClientBundle\Entity\ElementGraphique $elementsGraphique
     */
    public function removeElementsGraphique(\MesClics\EspaceClientBundle\Entity\ElementGraphique $elementsGraphique)
    {
        $this->elementsGraphiques->removeElement($elementsGraphique);
        $elementGraphique->setCharte(null);
    }

    /**
     * Get elementsGraphiques
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElementsGraphiques()
    {
        return $this->elementsGraphiques;
    }

    /**
     * Add support
     *
     * @param \MesClics\EspaceClientBundle\Entity\Support $support
     *
     * @return CharteGraphique
     */
    public function addSupport(\MesClics\EspaceClientBundle\Entity\Support $support)
    {
        $this->supports[] = $support;
        $support->setCharte($this);

        return $this;
    }

    /**
     * Remove support
     *
     * @param \MesClics\EspaceClientBundle\Entity\Support $support
     */
    public function removeSupport(\MesClics\EspaceClientBundle\Entity\Support $support)
    {
        $this->supports->removeElement($support);
        $support->setCharte(null);
    }

    /**
     * Get supports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupports()
    {
        return $this->supports;
    }
}
