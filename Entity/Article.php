<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Table(name="mesclics_espaceclient_article")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\ArticleRepository")
 */
class Article
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
     * @var string|null
     *
     * @ORM\Column(name="ref", type="string", length=255, nullable=true, unique=true)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="TVA", type="decimal")
     */
    private $tva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="prixHT", type="decimal", nullable=true)
     */
    private $prixHT;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ref.
     *
     * @param string|null $ref
     *
     * @return Article
     */
    public function setRef($ref = null)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref.
     *
     * @return string|null
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Article
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Article
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set tva.
     *
     * @return Article
     */
    public function setTVA($tva)
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva.
     */
    public function getTVA()
    {
        return $this->tva;
    }

    /**
     * Set prixHT.
     *
     * @return Article
     */
    public function setPrixHT($prixHT = null)
    {
        $this->prixHT = $prixHT;

        return $this;
    }

    /**
     * Get prixHT.
     */
    public function getPrixHT()
    {
        return $this->prixHT;
    }
}
