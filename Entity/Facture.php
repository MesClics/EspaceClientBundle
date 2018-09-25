<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Facture
 *
 * @ORM\Table(name="mesclics_espaceclient_facture")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\FactureRepository")
 */
class Facture
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
     * @ORM\Column(name="ref", type="string", length=255, unique=true)
     */
    private $ref;

    /**
     * @var bool
     *
     * @ORM\Column(name="franchise_tva", type="boolean")
     */
    private $franchiseTva;


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
     * @param string $ref
     *
     * @return Facture
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref.
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set franchiseTva.
     *
     * @param bool $franchiseTva
     *
     * @return Facture
     */
    public function setFranchiseTva($franchiseTva)
    {
        $this->franchiseTva = $franchiseTva;

        return $this;
    }

    /**
     * Get franchiseTva.
     *
     * @return bool
     */
    public function isFranchiseTva()
    {
        return $this->franchiseTva;
    }
}
