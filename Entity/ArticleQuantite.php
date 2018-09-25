<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ArticleQuantite
 *
 * @ORM\Table(name="article_quantite")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\ArticleQuantiteRepository")
 */
class ArticleQuantite
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
     * @var \MesClics\EspaceClientBundle\Entity\Article
     * 
     * @ORM\ManyToOne(targetEntity="\MesClics\EspaceClientBundle\Entity\Article")
     */
    private $article;


    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;


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
     * Set article
     * 
     * @param \MesClics\EspaceClientBundle\Entity\Article $article
     * 
     * @return ArticleQuantite
     */
    public function setArticle(\MesClics\EspaceClientBundle\Entity\Article $article){
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     * 
     * @return \MesClics\EspaceClientBundle\Entity\Article
     */
    public function getArticle(){
        return $this->article;
    }

    /**
     * Set quantite.
     *
     * @param int $quantite
     *
     * @return ArticleQuantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite.
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }
}
