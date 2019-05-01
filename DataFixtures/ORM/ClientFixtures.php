<?php
namespace MesClics\EspaceClientBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use MesClics\EspaceClientBundle\Entity\Image;
use Doctrine\Common\Persistence\ObjectManager;
use MesClics\EspaceClientBundle\Entity\Client;
use MesClics\UserBundle\DataFixtures\ORM\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ClientFixtures extends Fixture{

    public function load(ObjectManager $manager){

        //PROSPECT
        $prospect = new Client();
        $google_logo = new Image();
        $google_logo
        ->setUrl("https://www.usine-digitale.fr/mediatheque/5/0/0/000305005_homePageUne/logo-google-g.jpg")
        ->setAlt("logo Google");
        $prospect
        ->setProspect(true)
        ->setNom("Google")
        ->setImage($google_logo);

        $manager->persist($prospect);
        
        //CLIENT
        $client = new Client();
        $ams_logo = new Image();
        $ams_logo
        ->setUrl("https://www.amsnettoyage.com/view/images/logo.png")
        ->setAlt("logo AMS");
        $client
        ->setProspect(false)
        ->setNom("AMS AXE MULTI SERVICE")
        ->setImage($ams_logo);

        $manager->persist($client);

        $manager->flush();
    }
}