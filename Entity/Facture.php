<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\EspaceClientBundle\Entity\Article;
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
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Article")
     */
    private $articles;


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

    /**
     * Add Article
     * 
     */
    public function addArticle(Article $article){
        $this->articles[] = $article;

        return $this;
    }

    /**
     * remove article
     */
    public function removeArticle(Article $article){
        $this->articles->removeElement($article);
    }

    /**
     * get Articles
     */
    public function getArticles(){
        return $this->articles;
    }

    public function __construct(){
        $this->articles = new ArrayCollection();
    }
}
