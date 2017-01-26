<?php
// src/OC/PlatformBundle/DataFixtures/ORM/LoadApplication.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Application;

class LoadApplication implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétences à ajouter
	$advertA = $manager->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Alexandro']); 
	$advertB = $manager->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Alberto']); 
	$advertC = $manager->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Roberto']); 
	$advertD = $manager->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Django']); 
	$advertE = $manager->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Pedro']); 
		
	$appA= new Application();
	$appA->setContent("Je connais SF2, SF3 et même SF4! Embauchez moi!");
	$appA->setAuthor("Billy");
	$appA->setAdvert($advertA);
	$appA->setIp('127.0.0.1');
	$manager->persist($appA);	
	
	$appB= new Application();
	$appB->setContent("Bonjour votre annonce m'intéresse,.. je m'appel Jhonny,....");
	$appB->setAuthor("Jhonny");
	$appB->setAdvert($advertA);
	$appB->setIp('127.0.0.1');
	$manager->persist($appB);
	
	$appC= new Application();
	$da =	new \DateTime();
	$da->sub( new \DateInterval('P18D'));
	$appC->setDate( $da);
	$appC->setContent("Je suis un pro de l'inté et de SF2");
	$appC->setAuthor("Rocky");
	$appC->setAdvert($advertC);
	$appC->setIp('127.0.0.1');
	$manager->persist($appC);
	
	$appD= new Application();
	$db =	new \DateTime();
	$db->sub( new \DateInterval('P14D'));
	$appD->setDate( $db);
	$appD->setContent("Je débute en SF3 j'aimerais en savoir plus, je suis une brute sur PS et sur l'intégration html5");
	$appD->setAuthor("Boby");
	$appD->setAdvert($advertC);
	$appD->setIp('127.0.0.1');
	$manager->persist($appD);
	
	$appE= new Application();
	$dc =	new \DateTime();
	$dc->sub( new \DateInterval('P6D'));
	$appE->setDate( $dc);
	$appE->setContent("Bonjour, votre annonce me correspond, je suis polyvalent blablabla....");
	$appE->setAuthor("Willy");
	$appE->setAdvert($advertC);
	$appE->setIp('127.0.0.1');
	$manager->persist($appE);
	
	$appF= new Application();
	$dd =	new \DateTime();
	$dd->sub( new \DateInterval('P10D'));
	$appF->setDate( $dd);
	$appF->setContent("Like a baby miss...");
	$appF->setAuthor("Jimmy");
	$appF->setAdvert($advertE);
	$appF->setIp('127.0.0.1');
	$manager->persist($appF);
	
	$appG= new Application();
	$de =	new \DateTime();
	$de->sub( new \DateInterval('P2D'));
	$appG->setDate($de);
	$appG->setContent("Bonjour, je suis tellement un dev Backend que je n'utilise pas d'écran chez moi. Non je déconne, je pourrais pas jouer à Borderlands sinon. Pardon, je suis dev BE, je joue à WoW et Euclidea.");
	$appG->setAuthor("Jacky");
	$appG->setAdvert($advertB);
	$appG->setIp('127.0.0.1');
	$manager->persist($appG);
	


    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
