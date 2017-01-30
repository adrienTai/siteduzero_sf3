<?php
namespace OC\PlatformBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class isFloodingService
{

	public $manager;
    private $request_stack;
    
	public function __construct(EntityManager $manager, RequestStack $request_stack){
		$this->manager = $manager;
		$this->request_stack = $request_stack;
	}

	public function isFlooding($seconds)
	{

		$ip = $this->request_stack->getCurrentRequest()->getClientIp();
		$a= $this->manager->getRepository('OCPlatformBundle:Advert')->isFlood($ip, $seconds);
		$b= $this->manager->getRepository('OCPlatformBundle:Application')->isFlood($ip, $seconds);
		/*
			echo "<pre>";
			var_dump($a);
			var_dump($b);
			die();
		*/
		if(empty($a) || empty($b)){
			return true;
		}		
		
		
		return false;
	}
	
}

