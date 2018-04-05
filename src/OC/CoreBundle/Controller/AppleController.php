<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;	
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

class AppleController extends Controller
{
    public function indexAction()
    {
        
        return $this->render('OCCoreBundle:Apple:index.html.twig');
    }



    public function contactAction(Request $request)

    {
  		$session = $request->getSession();
    
    	// Bien sûr, cette méthode devra réellement ajouter l'annonce
    
    	// Mais faisons comme si c'était le cas
 		 $session->getFlashBag()->add('info', 'La page de contact n\'est pas encore disponible, merci de revenir plus tard');
   

		return $this->redirectToRoute('oc_core_homepage');
    	//return $this->render('OCCoreBundle:Apple:contact.html.twig');
    }
}