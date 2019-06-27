<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Client
 *
 * @ORM\Table(name="mesclics_client")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\ClientRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Client
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
     * @ORM\Column(name="numero", type="string", length=7, unique=true, nullable=true)
     */
    private $numero;

    /**
     * @var string
     * @Gedmo\Slug(fields={"nom"})
     * @ORM\Column(name="alias", type="string", length=50, unique=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100)
     * @Assert\Length(min=3, minMessage="Le nom du client doit contenir au moins 3 caractères, ajoutez éventuellement 000 pour compléter un nom trop court", max=100, maxMessage="Le nom est trop long. Il doit contenir moins de 100 caractères")
     */
    private $nom;
    
    /** 
     * @ORM\Column(name="prospect", type="boolean")
     */
    private $prospect = true;

    /**
     * @ORM\OneToOne(targetEntity= "MesClics\EspaceClientBundle\Entity\CharteGraphique", inversedBy="client", cascade={"persist"})
     */
    private $charte;

    //FIXME: TEST: suppression de l'attribut
    /**
     * @ORM\OneToOne(targetEntity="MesClics\EspaceClientBundle\Entity\Image", cascade={"persist"})
     */
    private $image; 
   
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Projet", mappedBy="client", cascade={"persist"})
     * @Assert\Valid()
     */
    private $projets;

    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Contrat", mappedBy="client", cascade={"persist"})
     * @Assert\Valid()
     */
    private $contrats;

    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Facture", mappedBy="client", cascade={"persist"})
     */
    private $factures;

    /**
     * @ORM\Column(name="trello", type="json_array", nullable=true)
     */
    private $trello_list;

    // /**
    //  * @ORM\OneToMany(targetEntity="MesClics\UserBundle\Entity\MesclicsUser", mappedBy="client", cascade={"persist"})
    //  */
    // private $users;

    /**
     * @ORM\Column(name="website", type="text", length=255, nullable=true)
     */
    private $website;
        
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Client
     */
    public function setNumero($numero)
    { 
        $this->numero = $numero;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Has numero
     * 
     * @return bool
     */
    public function hasNumero(){
        return $this->getNumero() ? true : false;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Client
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Client
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
     * Set prospect
     *
     * @param boolean $prospect
     *
     * @return Client
     */
    public function setProspect($prospect)
    {
        $this->prospect = $prospect;

        return $this;
    }

    /**
     * Is prospect
     *
     * @return boolean
     */
    public function isProspect()
    {
        return $this->prospect;
    }

    public function getImage(){
        return $this->image;
    }

    public function setImage(Image $image = null){
        $this->image = $image;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Client
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate(){
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set charte
     *
     * @param \MesClics\EspaceClientBundle\Entity\CharteGraphique $charte
     *
     * @return Client
     */
    public function setCharte(\MesClics\EspaceClientBundle\Entity\CharteGraphique $charte = null)
    {
        $this->charte = $charte;
        $charte->setClient($this);
        return $this;
    }

    /**
     * Get charte
     *
     * @return \MesClics\EspaceClientBundle\Entity\CharteGraphique
     */
    public function getCharte()
    {
        return $this->charte;
    }

    /**
     * Add projet
     *
     * @param \MesClics\EspaceClientBundle\Entity\Projet $projet
     *
     * @return Client
     */
    public function addProjet(\MesClics\EspaceClientBundle\Entity\Projet $projet)
    {
        $this->projets[] = $projet;
        $projet->setClient($this);

        return $this;
    }

    /**
     * Remove projet
     *
     * @param \MesClics\EspaceClientBundle\Entity\Projet $projet
     */
    public function removeProjet(\MesClics\EspaceClientBundle\Entity\Projet $projet)
    {
        $this->projets->removeElement($projet);
        $projet->setClient(null);
    }

    /**
     * Get projets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjets()
    {
        return $this->projets;
    }

    /**
     * Add contrat
     *
     * @param \MesClics\EspaceClientBundle\Entity\Contrat $contrat
     *
     * @return Client
     */
    public function addContrat(\MesClics\EspaceClientBundle\Entity\Contrat $contrat)
    {
        $this->contrats[] = $contrat;
        $contrat->setClient($this);

        return $this;
    }

    /**
     * Remove contrat
     *
     * @param \MesClics\EspaceClientBundle\Entity\Contrat $contrat
     */
    public function removeContrat(\MesClics\EspaceClientBundle\Entity\Contrat $contrat)
    {
        $this->contrats->removeElement($contrat);
        $contrat->setClient(null);
    }

    /**
     * Get contrats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContrats()
    {
        return $this->contrats;
    }

    /**
     * Add facture
     * @param \MesClics\EspaceClientBundle\Entity\Facture
     * 
     * @return Client
     */
    public function addFacture(\MesClics\EspaceClientBundle\Entity\Facture $fatcure){
        $this->factures[] = $facture;
        $fdacture->setClient($this);

        return $this;
    }

    /**
     * remove facture
     * @param \MesClics\EspaceClientBundle\Entity\Facture
     * 
     */
    public function removeFacture(\MesClics\EspaceClientBundle\Entity\Facture $facture){
        $this->factures->removeElement($facture);
        $facture->setClient(null);
    }

    /**
     * get Factures
     */
    public function getFactures(){
        return $this->factures;
    }

    /**
     * set trello_list
     */
    public function setTrelloList($trello_list){
        $this->trello_list = $trello_list;
        return $this;
    }

    /**
     * get trello list
     */
    public function getTrelloList(){
        return $this->trello_list;
    }

    public function hasTrelloList(){
        return $this->getTrelloList() ? $this->trello_list : false;
    }

    // /**
    //  * add User
    //  */
    // public function addUser(MesclicsUser $user){
    //     $this->users[] = $user;
    // }

    // /**
    //  * get Users
    //  */
    // public function getUsers(){
    //     return $this->users;
    // }

    public function getWebsite(){
        return $this->website;
    }

    public function setWebsite(String $website_url){
        $this->website = $website_url;

        return $this;
    }
    

    /**
     * constructor
     */
    public function __construct(){
        $this->contrats = new ArrayCollection();
        $this->projets = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }
}
