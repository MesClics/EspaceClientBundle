<?php

namespace MesClics\EspaceClientBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contrat
 *
 * @ORM\Table(name="mesclics_contrat")
 * @ORM\Entity(repositoryClass="MesClics\EspaceClientBundle\Repository\ContratRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contrat
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
     * @ORM\Column(name="type", type="string", length=55)
     * 
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     * @Assert\DateTime()
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_signature", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateSignature;

    /**
     * @var \DateTime
     * 
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate;

    /**
     * @ORM\Column(name="numero", type="string", nullable=true)
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity="MesClics\EspaceClientBundle\Entity\Projet", mappedBy="contrat", cascade={"persist"})
     */
    private $projets;

    /**
     * @ORM\ManyToOne(targetEntity="MesClics\EspaceClientBundle\Entity\Client", inversedBy="contrats", cascade={"persist"})
     */
    private $client;


    /**
     * Get id
     *
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Contrat
     */
    public function setType($type){
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Contrat
     */
    public function setDateCreation($dateCreation){
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation(){
        return $this->dateCreation;
    }

    /**
     * Set dateSignature
     *
     * @param \DateTime $dateSignature
     *
     * @return Contrat
     */
    public function setDateSignature($dateSignature){
        $this->dateSignature = $dateSignature;

        return $this;
    }

    /**
     * Get dateSignature
     *
     * @return \DateTime
     */
    public function getDateSignature(){
        return $this->dateSignature;
    }

    /**
     * Set lastUpdate
     * 
     * @return Contrat
     */
    public function setLastUpdate(){
        $this->lastUpdate = new \DateTime();

        return $this;
    }

    /**
     * Get lastUpdate
     * 
     * @return \DateTime
     */
    public function getLastUpdate(){
        return $this->lastUpdate;
    }

    /**
     * Add projet
     *
     * @param \MesClics\EspaceClientBundle\Entity\Projet $projet
     *
     * @return Contrat
     */
    public function addProjet(\MesClics\EspaceClientBundle\Entity\Projet $projet)
    {
        $this->projets[] = $projet;
        $projet->setContrat($this);
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
        $projet->setContrat(null);
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Contrat
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
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
     * Set client
     *
     * @param \MesClics\EspaceClientBundle\Entity\Client $client
     *
     * @return Contrat
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
     * @ORM\PrePersist
     */
    public function updateDates(){
        $this->setDateCreation(new \DateTime());
        $this->updateLastUpdate();
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateLastUpdate(){
        $this->setLastUpdate(new \Datetime());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projets = new ArrayCollection();
    }

    // formattage du label lorsque l'objet est une option d'un select
    public function getSelectLabel(){
        return $this->getNumero() . '(' . $this->getType() . ') créé le ' . $this->getDateCreation()->format('d/m/Y');
    }

    //validation
    /**
     * @Assert\IsTrue(message="Le type de contrat n'est pas valide")
     */
    public function isType(){
        $types = array(
            'test',
            'web',
            'graphisme',
            'web et graphisme'
        );

        //on prépare chaque type pour la regexp (début de chaîne / fin de chaîne)
        foreach($types as $key => $type){
            $new_type = '^'.$type.'$';
            $types[$key] = $new_type;
        }
        $pattern =  '/'.implode("|", $types).'/';

        return preg_match($pattern, $this->type);
    }
}
