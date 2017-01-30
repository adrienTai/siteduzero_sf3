<?php
namespace OC\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Category;
use OC\PlatformBundle\Service\PurgeadvertService;

use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;

use OC\PlatformBundle\Service\isFloodingService;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdvertController extends Controller
{
	
	public function indexAction($page){
		if($page <1 ){
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		$nbPerPage = 3;
		$em = $this->getDoctrine()->getManager();
		$listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdverts($page, $nbPerPage);
		$nbPages = ceil(count($listAdverts) / $nbPerPage);
		
		if($page > $nbPages){
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'nom'=>'GreenBatou',
		  'listAdverts' => $listAdverts,
		  'nbPages'     => $nbPages,
		  'page'        => $page,
		));
	}


	public function viewAction(Advert $advert, Request $request){
		//$tag = $request->query->get('tag');

		//$repository = $this->get('doctrine')->getManager()->getRepository('OCPlatformBundle:Advert');
		$em = $this->getDoctrine()->getManager();

		//$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		
		//if(null === $advert) {
		//	throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		//}
		
		$listApplications = $em->getRepository('OCPlatformBundle:Application')
								->findBy(array('advert' => $advert));
		
		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
								->findBy(array('advert' => $advert));

		
		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
		  'advert' => $advert,
		  'listApplications' => $listApplications,
		  'listAdvertSkills' => $listAdvertSkills,
		  'listApplications' => $advert->getApplications()
		));
	}
	
	/**
	* @ParamConverter("date", options={"format": "Y-m-d"})
	*/
	public function viewListAction(\Datetime $date){
		//var_dump($date);
		//die();
		
	}

/*
	/**
	* @ParamConverter("json")
	* /
	public function ParamConverterAction($json)
	{
		return new Response(print_r($json, true));
	}
*/

	/*
	* @Security("has_role('ROLE_AUTEUR')")
	*/
	public function addAction(Request $request){
		/*
		controle d'accès par l'apppel du service, autre manière que les annotaions
		
		if(!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')){
			throw new AccessDeniedException('Accès limité aux auteurs.');
		}
		*/
		$advert = new Advert();
		//$form = $this->get('form.factory')->create(AdvertType::class, $advert);
		$form = $this->createForm(AdvertType::class, $advert);
		
		if ($request->isMethod('POST') &&  $form->handleRequest($request)->isValid()) {
		    $em = $this->getDoctrine()->getManager();
		    $advert->setIp($request->getClientIp());
		    
		    ////begin-event
			// On crée l'évènement avec ses 2 arguments
			//$event = new MessagePostEvent($advert->getContent(), $advert->getUser());
			$event = new MessagePostEvent($advert->getContent(), $this->getUser());

			// On déclenche l'évènement
			$this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

			// On récupère ce qui a été modifié par le ou les listeners, ici le message
			$advert->setContent($event->getMessage());
		    ////end-event
		    
		    $em->persist($advert);
		    $em->flush();

		    $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

		    return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));

		}

		return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
		  'form' => $form->createView(),
		));
		
	}

	public function editAction($id, Request $request){
		
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
		if( null === $advert){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}
		
		$form = $this->get('form.factory')->create(AdvertEditType::class, $advert);
	
		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

		    //$em->persist($advert); persister est facultatif ici, Doctrine connait déjà l' advert
		    $em->flush();

			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');			
			return $this->redirectToRoute('oc_platform_view', ['id'=>$advert->getId()]);

		  
		}

		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
		  'advert' => $advert,
		  'form' => $form->createView()
		));
	}
	
	public function deleteAction($id, Request $request){
	/*
		$purger = $this->container->get('oc_platform.purger.advert');
		$purger->deleteOne($id, false);
		return $this->redirectToRoute('oc_platform_home', ['id'=> 1]);
	*/
		$em = $this->getDoctrine()->getManager();
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if (null === $advert) {
		  throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		// On crée un formulaire vide, qui ne contiendra que le champ CSRF
		// Cela permet de protéger la suppression d'annonce contre cette faille
		$form = $this->get('form.factory')->create();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')
									->findBy(array('advert'=>$advert) );
			foreach($listAdvertSkills as $as){
				$em->remove($as);
			}
			
			$em->remove($advert);
			$em->flush();

			$request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

			return $this->redirectToRoute('oc_platform_home');
		}
		
		return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
		  'advert' => $advert,
		  'form'   => $form->createView(),
		));
	}
	
	public function menuAction($limit)
	{
		$em = $this->getDoctrine()->getManager();

		$listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
		  array(),                 // Pas de critère
		  array('date' => 'desc'), // On trie par date décroissante
		  $limit,                  // On sélectionne $limit annonces
		  0                        // À partir du premier
		);

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
		  // Tout l'intérêt est ici : le contrôleur passe
		  // les variables nécessaires au template !

		  'listAdverts' => $listAdverts
		));
	}
	
	public function purgeAction($days, Request $request){
		
//		$purge = new PurgeadvertService($this->getDoctrine(), $request);
		$purger = $this->container->get('oc_platform.purge.advert');
		
		//on récupère un tableau qui précise les id supprimés/conservés
		$res= $purger->purge($days);
		if($res !== false && !EMPTY($res) ){
			foreach($res as $key=>$bool){
				if($bool === true){
					$request->getSession()->getFlashBag()->add('notice', "Annonce numéro $key a bien été supprimée");
				}else{
					$request->getSession()->getFlashBag()->add('notice', "Annonce numéro $key contient des candidatures, nous ne pouvons la supprimer.");	
				}
			}
			
			$request->getSession()->getFlashBag()->add('notice', "Fin de l'opération de purge. Les annonces de plus de $days jours ont bien étées supprimées si elles ne comportent pas de candidatures.");			
		}		
		return $this->redirectToRoute('oc_platform_home', ['id'=> 1]);
	}

	
	public function endFixturesAction(Request $request){
		$em = $this->getDoctrine()->getManager();
		//omg c'est trop long et répétitif >< l'autre méthode de fixture avec .yml est mieux.
		
		$advertA = $em->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Alexandro']); 
		$advertB = $em->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Alberto']); 
		$advertC = $em->getRepository('OCPlatformBundle:Advert')->findOneBy(['author' => 'Roberto']); 
		$catDev = $em->getRepository('OCPlatformBundle:Category')->findOneBy(['name' => 'Développement web']);
		$catDevM = $em->getRepository('OCPlatformBundle:Category')->findOneBy(['name' => 'Développement mobile']);
		$catInte = $em->getRepository('OCPlatformBundle:Category')->findOneBy(['name'=>  'Intégration']);
		$catGra = $em->getRepository('OCPlatformBundle:Category')->findOneBy(['name' => 'Graphisme']);
		$catRes = $em->getRepository('OCPlatformBundle:Category')->findOneBy(['name' => 'Réseau']);
		$skillPHP = $em->getRepository('OCPlatformBundle:Skill')->findOneBy(['name' => 'PHP']);
		$skillSF = $em->getRepository('OCPlatformBundle:Skill')->findOneBy(['name' => 'Symfony']);
		$skillC = $em->getRepository('OCPlatformBundle:Skill')->findOneBy(['name' => 'C']);
		$skillJa = $em->getRepository('OCPlatformBundle:Skill')->findOneBy(['name' => 'Java']);
		$skillPS = $em->getRepository('OCPlatformBundle:Skill')->findOneBy(['name' => 'Photoshop']);
		
		$advertA->addCategory($catDev);
		$advertA->addCategory($catDevM);
		$ASAA = new AdvertSkill();
		$ASAA->setAdvert($advertA);	$ASAA->setSkill($skillPHP);	$ASAA->setLevel('Bon');
		$ASAB = new AdvertSkill();		
		$ASAB->setAdvert($advertA);	$ASAB->setSkill($skillSF); 	$ASAB->setLevel('Débutant');
		
		$advertB->addCategory($catDev);
		$advertB->addCategory($catDevM);
		$advertB->addCategory($catRes);
		$ASBA = new AdvertSkill();
		$ASBA->setAdvert($advertB);	$ASBA->setSkill($skillPHP);	$ASBA->setLevel('Maîtrise');
		$ASBB = new AdvertSkill();
		$ASBB->setAdvert($advertB);	$ASBB->setSkill($skillSF);	$ASBB->setLevel('Bon');
		$ASBC = new AdvertSkill();
		$ASBC->setAdvert($advertB);	$ASBC->setSkill($skillJa);	$ASBC->setLevel('Moyen');
		$ASBD = new AdvertSkill();
		$ASBD->setAdvert($advertB);	$ASBD->setSkill($skillC);	$ASBD->setLevel('Moyen');

		
		$advertC->addCategory($catDev);
		$advertC->addCategory($catInte);
		$advertC->addCategory($catGra);
		$ASCA = new AdvertSkill();
		$ASCA->setAdvert($advertC);	$ASCA->setSkill($skillPHP);	$ASCA->setLevel('Bon');
		$ASCB = new AdvertSkill();
		$ASCB->setAdvert($advertC);	$ASCB->setSkill($skillSF);	$ASCB->setLevel('Moyen');
		$ASCC = new AdvertSkill();
		$ASCC->setAdvert($advertC);	$ASCC->setSkill($skillPS);	$ASCC->setLevel('Maîtrise');
		
		$em->persist($advertA);		$em->persist($advertB);		$em->persist($advertC);
		$em->persist($ASAA);		$em->persist($ASAB);		
		$em->persist($ASBA);		$em->persist($ASBB);		$em->persist($ASBC);	$em->persist($ASBD);
		$em->persist($ASCA);		$em->persist($ASCB);		$em->persist($ASCC);	
		$em->flush();
		
		$request->getSession()->getFlashBag()->add('notice', 'fixtures bien corrigées');	
		
		return $this->redirectToRoute('oc_platform_home', ['id'=> 1]);
	}

/*
public function testAction(Request $request)
  {
    $advert = new Advert;
        
    $advert->setDate(new \Datetime());  // Champ « date » OK
    $advert->setTitle('abc');           // Champ « title » incorrect : moins de 10 caractères
    //$advert->setContent('blabla');    // Champ « content » incorrect : on ne le définit pas
    $advert->setAuthor('A');            // Champ « author » incorrect : moins de 2 caractères
        
    // On récupère le service validator
    $validator = $this->get('validator');
        
    // On déclenche la validation sur notre object
    $listErrors = $validator->validate($advert);

    // Si $listErrors n'est pas vide, on affiche les erreurs
    if(count($listErrors) > 0) {
      // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
      return new Response((string) $listErrors);
    } else {
      return new Response("L'annonce est valide !");
    }
  }
  */
  
  	public function isFloodingAction($seconds)
  	{
		
  		$result = $this->get('oc_platform.client.is_flooding')->isFlooding($seconds);
  		return new Response((string) $result);
  	}
  	
/*	
	public function translationAction($name)
	{
		return $this->render('OCPlatformBundle:Advert:translation.html.twig', array(
		'name' => $name
		));
	}
*/
}

