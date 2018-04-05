<?php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
	public function indexAction($page)
	{
		// On ne sait pas combien de pages il y a
		// Mais on sait qu'une page doit être supérieure ou égale à 1
		if ($page < 1){
			// On déclenche une exception NotFoundHttpException, cela va afficher
			// une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
			throw new NotFoundHttpException('Page "' . $page . '" inexitantes.');
			}



		//RECUPERATION DES DONNER DANS LA BASE
		$listAdverts = $this->getDoctrine()
			->getManager()
			->getRepository('OCPlatformBundle:Advert')
			->findAll()
		;

		// L'appel de la vue ne change pas
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listAdverts
		));
}

/*



		// Notre liste d'annonce en dur
		$listAdverts = array(
			array(
				'title'		=> 'Recherche développeur Symfony',
				'id'		=> 1,
				'author'	=> 'Alexandre',
				'content'	=> 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla...',
				'date'		=> new \Datetime()),
			array(
				'title'		=> 'Mission de webmaster',
				'id'		=> 2,
				'author'	=> 'Hugo',
				'content'	=> 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla...',
				'date'		=> new \Datetime()),
			array(
				'title'		=> 'Offre de stage webdesigner',
				'id'		=> 3,
				'author'	=> 'Mathieu',
				'content'	=> 'Nous proposons un poste pour webdesigner. Blabla…',
				'date'		=> new \Datetime()),
			array(
				'title'		=> 'Offre de stage developpeur front end',
				'id'		=> 4,
				'author'	=> 'Alexandre',
				'content'	=> 'Nous proposons un poste pour developpeur spécialiste en angular, react, javascript. Blabla…',
				'date'		=> new \Datetime()),
			array(
				'title'		=> 'Offre de stage développeur back end',
				'id'		=> 5,
				'author'	=> 'Paul',
				'content'	=> 'Recherche développeur php, sur symfony . Blabla…',
				'date'		=> new \Datetime()),
			array(
				'title'		=> 'Offre de stage UX designer',
				'id'		=> 6,
				'author'	=> 'Henri',
				'content'	=> 'Nous proposons un poste pour webdesigner, connaissance en sketch, adobe xd et photoshop. Blabla…',
				'date'		=> new \Datetime())
		);

		// Ici, on récupérera la liste des annonces, puis on la passera au template
		// Mais pour l'instant, on ne fait qu'appeler le template
		return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
			'listAdverts' => $listAdverts
		));
	}

*/

	public function viewAction($id)
	{

		//RECUPERATION DES DONNER DANS LA BASE


		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		// $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    	// ou null si l'id $id  n'existe pas, d'où ce if :
		if(null === $advert){
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
			
		}

		// On récupère la liste des candidatures de cette annonce
		$listApplications = $em
			->getRepository('OCPlatformBundle:Application')
			->findby(array('advert' => $advert))
			;

		// On récupére maintenant la liste des AdvertSkill de l'annonce
		$listAdvertSkills = $em
			->getRepository('OCPlatformBundle:AdvertSkill')
			->findby(array('advert' => $advert))
			;

		// Le render ne change pas, on passait avant un tableau, maintenant un objet
		return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
			'advert' 			=> $advert,
			'listApplications' 	=> $listApplications,
			'listAdvertSkills'	=> $listAdvertSkills
		));
	}

	public function addAction(Request $request)
	{
		// On récupère l'EntityManager
    	$em = $this->getDoctrine()->getManager();

		//AJOUT DONNER DANS LA BASE ADVERT ET IMAGE

		//création de l'entité Advert
		$advert = new Advert();
	    $advert->setTitle('Recherche développeur Symfony.');
	    $advert->setAuthor('Alexandre');
	    $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
		
		//création de l'entité Image
		$image = new Image();
		$image->setUrl('http://static1.terrafemina.com/articles/7/17/78/77/@/172027-travailler-a-letranger-10-bons-plans-pour-trouver-le-job-de-vos-reves-622x0-1.jpg');
		$image->setAlt('Un travail de rêve');

		// Création d'une première candidature
		$application1 = new Application();
		$application1->setAuthor('Marine');
		$application1->setContent('J\'ai toutes les qualités requises');

		// Création d'une deuxième candidature par exemple
		$application2 = new Application();
		$application2->setAuthor('Pierre');
		$application2->setContent('Je suis très motivé');

		// On lie les candidatures à l'annonce
		$application1->setAdvert($advert);
		$application2->setAdvert($advert);


		// On lie l'image à l'annonce

		$advert->setImage($image);




    	// On récupère toutes les compétences possibles
    	$listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();
    	// Pour chaque compétence
    	foreach ($listSkills as $skill) {
    		// On crée une nouvelle « relation entre 1 annonce et 1 compétence »
    		$advertSkill = new AdvertSkill();
    		// On la lie à l'annonce, qui est ici toujours la même
    		$advertSkill->setAdvert($advert);
    		// On la lie à la compétence, qui change ici dans la boucle foreach
    		$advertSkill->setSkill($skill);
    		// Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
    		$advertSkill->setLevel('Expert');
    		// Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
    		$em->persist($advertSkill);

    	}

    	// Étape 1 : On « persiste » l'entité
    	$em->persist($advert);
    	// Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
    	// on devrait persister à la main l'entité $image
    	// $em->persist($image);

    	// Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    	// définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    	$em->persist($application1);
    	$em->persist($application2);

    	// Étape 2 : On « flush » tout ce qui a été persisté avant
    	$em->flush();


		//SERVICE OCANTISPAM

		// On récupère le service
		$antispam = $this->container->get('oc_platform.antispam');

		// Je pars du principe que $text contient le texte d'un message quelconque
		$text = 'Accenderat super his incitatum propositum ad nocendum aliqua mulier vilis, quae ad palatium ut poposcerat intromissa insidias ei latenter obtendi prodiderat a militibus obscurissimis. quam Constantina exultans ut in tuto iam locata mariti salute muneratam vehiculoque inpositam per regiae ianuas emisit in publicum, ut his inlecebris alios quoque ad indicanda proliceret paria vel maiora.';
		if($antispam->isSpam($text)){
			throw new \Exception('Votre message a été détecté comme spam !');
		}


		// La gestion d'un formulaire est particulière, mais l'idée est la suivante :
		// Si la requête est en POST, c'est que le visiteur a soumis le formulaire
		if($request->isMethod('POST')){

			// Ici, on s'occupera de la création et de la gestion du formulaire

			$request->getSession()->getFlashbag()->add('notice', 'Annonce bien enregistrée.');

			// Puis on redirige vers la page de visualisation de cettte annonce
			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));

		}

		// Si on n'est pas en POST, alors on affiche le formulaire
		return $this->render('OCPlatformBundle:Advert:add.html.twig');
	}

	public function editAction($id, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
		}

		// La méthode findAll retourne toutes les catégories de la base de données
		$listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

		// On boucle sur les catégories pour les lier à l'annonce
		foreach ($listCategories as $category) {
			$advert->addCategory($category);
		}

		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
   		// Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

   		// Étape 2 : On déclenche l'enregistrement
		$em->flush();

		// Même mécanisme que pour l'ajout
		if($request->isMethod('POST')){
			$request->getSession->getFlashbag()->add('notice', 'Annonce bien modifiée.');

			return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
		}

		/*

		$advert = array(
			'title'  	=> 'Recherche développeur symfony',
			'id'	 	=> $id,
			'author'	=> 'Alexandre',
			'content'	=> 'Nous recherchons un développeur Symfony débutant sur Lyon Blabla...',
			'date'		=> new \Datetime()
		);

		*/

		return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
			'advert'	=> $advert
		));
	}

	public function deleteAction($id)
	{
		$em = $this->getDoctrine()->getManager();

		// On récupère l'annonce $id
		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if(null === $advert) {
			throw new NotFoundHttpException('L\'annonce d\'Id' . $id . 'n\'existe pas');
		}

		// On boucle sur les catégories de l'annonce pour les supprimer
		foreach ($advert->getCategories() as $category){
			$advert->removeCategory($category);
		}

		// Pour persister le changement dans la relation, il faut persister l'entité propriétaire
   		// Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    	// On déclenche la modification
    	$em->flush();

		// Ici, on gérera la suppression de l'annonce en question
		return $this->render('OCPlatformBundle:Advert:delete.html.twig');
	}

	public function menuAction($limit)
	{ 
		// On fixe en dur une liste ici, bien entendu par la suite
		// on la récupérera depuis la BDD !
	/*	$listAdverts = array(
			array('id' => 2, 'title' => 'Recherche développeur Symfony'),
			array('id' => 5, 'title' => 'Mission de webmaster'),
			array('id' => 9, 'title' => 'Offre de stage webdesigner')
		);  */


		//RECUPERATION DES DONNEES DANS LA BASE
		$em = $this->getDoctrine()->getManager();

		$listAdverts = $em
			->getRepository('OCPlatformBundle:Advert')
			->findBy(
				array(),						// Pas de critère
				array('date' => 'desc'),		// On trie par date décroissante
				$limit,							// On sélectionne $limit annonces
				0								// À partir du premier
			);

		return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
			// Tout l'intérêt est ici : le contrôleur passe
			// les variables nécessaires au template !
			'listAdverts' => $listAdverts
		));
	}


 

}