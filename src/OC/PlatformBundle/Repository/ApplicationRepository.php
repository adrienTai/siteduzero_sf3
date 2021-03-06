<?php

namespace OC\PlatformBundle\Repository;

/**
 * ApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApplicationRepository extends \Doctrine\ORM\EntityRepository
{

	public function getApplicationsWithAdvert($limit){
		$qb = $this->createQueryBuilder('app')
			->innerjoin('app.advert', 'adv')
			->addSelect('adv')
			->orderBy('app.date', 'DESC')
			->setMaxResults($limit)
		;
		
		return $qb->getQuery()
				->getResult();
	}
	
	public function isFlood($ip, $seconds){
		
		$now = new \DateTime();
		$echeance = $now->sub( new \DateInterval('PT'.$seconds.'S'));
		$qb = $this->createQueryBuilder('app')
				->select('app.content, app.date')
				->where('app.date >:date_now')
				->andWhere('app.ip =:actual_ip')
				->setParameters(array('date_now' => $echeance, 'actual_ip' => $ip ))
				->orderBy('app.date', 'DESC')
				->setMaxResults('1');
		return $qb->getQuery()
					->getResult();
	}
}
