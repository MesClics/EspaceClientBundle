<?php
namespace MesClics\EspaceClientBundle\Form\DTO;

use MesClics\UtilsBundle\Links\Link;
use MesClics\EspaceClientBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayIterableItem;

class ClientDTO extends DataTransportObjectToEntity{
    private $name;
    private $prospect;
    private $image;
    private $website;
    private $socials;

    public function __construct(){
        parent::__construct();
        $this->socials = new ArrayCollection();
    }

    public function getMappingArray(){
        return array(
            "name" => new MappingArrayItem("name", array("getName", "setName"), array("getNom", "setNom")),
            "prospect" => new MappingArrayItem("prospect", array("isProspect", "setProspect")),
            "image" => new MappingArrayItem("image", array("getImage", "setImage")),
            "website" => new MappingArrayItem("website", array("getWebsite", "setWebsite")),
            // "socials" => new MappingArrayIterableItem("socials", array("getSocials", "addSocial", "removeSocial"))
        );
    }

    public function getName(){
        return $this->name;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function isProspect(){
        return $this->prospect;
    }

    public function setProspect($prospect){
        $this->prospect = $prospect;
    }

    public function getImage(){
        return $this->image;
    }

    public function setImage(Image $image){
        $this->image = $image;
    }

    public function getWebsite(){
        return $this->website;
    }

    public function setWebsite(string $url){
        $this->website = $url;
    }

    public function getSocials(){
        return $this->socials;
    }

    public function addSocial(Link $link){
        $this->socials->add($link);
    }

    public function removeSocial(Link $link){
        $this->socials->remove($link);
    }
}