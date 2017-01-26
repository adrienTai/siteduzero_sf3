<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadAdvert.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Category;

class LoadAdvert implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétences à ajouter
	
	$advertA= new Advert();
	$advertA->setTitle("Recherche développeur full stack Symfony3.");
	$advertA->setAuthor("Alexandro");
	$advertA->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
	$advertA->setEmail("doubidou@gmail.com");
	$manager->persist($advertA);
	
	$advertB= new Advert();
	$d =	new \DateTime();
	$d->sub( new \DateInterval('P5D'));
	$advertB->setDate( $d);
	$advertB->setTitle("Recherche développeur backend Symfony2 et Java.");
	$advertB->setAuthor("Alberto");
	$advertB->setContent("Nous recherchons un développeur SF2 avec connaissance en Java pour migration, sur Saint-Fons...");
	$advertB->setEmail("bleurps@gmail.com");
	$manager->persist($advertB);
	
	$advertC= new Advert();
	$da =	new \DateTime();
	$da->sub( new \DateInterval('P20D'));
	$advertC->setDate( $da);
	$advertC->setTitle("Recherche développeur full front.");
	$advertC->setAuthor("Roberto");
	$advertC->setContent("Nous recherchons un développeur web, avec qui maîtrise un framework SF ou Zend. Bliblibli…");
	$advertC->setEmail("burp@gmail.com");
	$manager->persist($advertC);
    
    $advertD= new Advert();
    $dat =	new \DateTime();
	$dat->sub( new \DateInterval('P8D'));
	$advertD->setDate( $dat);
	$advertD->setTitle("Recherche développeur full stack.");
	$advertD->setAuthor("Primo");
	$advertD->setContent("Nous recherchons un développeur Symfony senor sur Lyon. Blabla…");
	$advertD->setEmail("doubidou15@gmail.com");
	$manager->persist($advertD);
	
	$advertE= new Advert();
	$date =	new \DateTime();
	$date->sub( new \DateInterval('P7D'));
	$advertE->setDate( $date);
	$advertE->setTitle("Recherche développeur backend.");
	$advertE->setAuthor("Django");
	$advertE->setContent("Nous recherchons un développeur Backend. Blabla…");
	$advertE->setEmail("imissu@gmail.com");
	$manager->persist($advertE);
	
	$advertF= new Advert();
	$date_ =	new \DateTime();
	$date_->sub( new \DateInterval('P15D'));
	$advertF->setDate( $date_);
	$advertF->setTitle("Recherche développeur qui développe.");
	$advertF->setAuthor("Pedro");
	$advertF->setContent("Nous recherchons un développeur qui développe, et oui, pas d'inspi. Blabla…");
	$advertF->setEmail("cadeveloppe@gmail.com");
	$manager->persist($advertF);
	

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
