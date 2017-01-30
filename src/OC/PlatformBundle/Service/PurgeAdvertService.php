<?php
namespace OC\PlatformBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;

class PurgeAdvertService
{

	public $manager;
    
	public function __construct(EntityManager $manager){
		$this->manager = $manager;
		
	}

	public function purge($days)
	{
		//je ne récupère qu'une liste d'ID qui dépassent $days jours		
		$listAdverts = $this->manager->getRepository('OCPlatformBundle:Advert')->getAdvertsFromDays($days);
	
		$advertDeleted = array();
		foreach($listAdverts as $advert){
			// pour chacun je lance la méthode de suppression , ramenée ici dans le service
			$advertDeleted[$advert->getId()]= $this->deleteOne($advert, true); 
		}
		// on renvoit un tableau des id supprimés, indiquant true si supprimé
		return $advertDeleted;
	}
	
	public function deleteOne(Advert $advert, $purge =false)
	{
		if($advert->getNbApplications() == 0 ){
			//je supprime ses relations avec category
			foreach($advert->getCategories() as $category){
				$advert->removeCategory($category);
			}
			//je supprime ses entitées-relations avec skills
			$listAdvertSkills = $this->manager->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert'=>$advert) );
			foreach($listAdvertSkills as $as){
				$this->manager->remove($as);
			}
			//éventuelle suite : forcer la suppression même si elle contient des applications (avec un param 'force' de notre méthode)
			
			$this->manager->remove($advert);
			try{
				$this->manager->flush();
			}
			catch(Exception $e){
				throw new NotFoundHttpException("Erreur lors de la suppression des adverts : ".$e);
			}
			
			return true;
		}else {
		    
			return false;
		}
		
		
	}
}

