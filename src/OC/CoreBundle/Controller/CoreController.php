<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction()
    {
    /*
		$lastAdverts = array(
		  array(
		    'title'   => 'Recherche développpeur Symfony',
		    'id'      => 1,
		    'author'  => 'Alexandre',
		    'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
		    'date'    => new \Datetime()),
		  array(
		    'title'   => 'Mission de webmaster',
		    'id'      => 2,
		    'author'  => 'Hugo',
		    'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
		    'date'    => new \Datetime()),
		  array(
		    'title'   => 'Offre de stage webdesigner',
		    'id'      => 3,
		    'author'  => 'Mathieu',
		    'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
		    'date'    => new \Datetime())
		);
		*/
        return $this->render('OCCoreBundle:Core:index.html.twig');
    }
	
	public function contactAction(Request $request){
		
		$request->getSession()->getFlashBag()->add('notice', 'La page de contact n\'est pas encore disponible, merci de revenir plus tard.');
		return $this->redirectToRoute('oc_core_home');
		//return $this->render('OCCoreBundle:Core:contact.html.twig');
	}
}
