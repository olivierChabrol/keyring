<?php
// src/Controller/VIVSController
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use App\Entity\Param;
use App\Entity\Trousseau;
use App\Entity\Pret;
use App\Entity\User;

use \Datetime;

class KeyringController extends Controller
{
	public function listKeyAjax(Request $request)
    {
        if ($request->isXmlHttpRequest())
	    {
		   $entityManager = $this->getDoctrine()->getManager();
		   $trousseaux = $this->getDoctrine()->getRepository(Trousseau::class)->getListKeyWithCondition($request->get("type"), $request->get("site"));
		   $retour = array("data" => $trousseaux);
		   return new JSonResponse(json_encode($retour));
		}
		return new Response("Error : not and ajax request", 400);
	}


	public function listFreeKeyAjax(Request $request)
    {
        if ($request->isXmlHttpRequest())
	    {
		   $entityManager = $this->getDoctrine()->getManager();
		   $trousseaux = $this->getDoctrine()->getRepository(Trousseau::class)->getListFreeKeyWithCondition($request->get("type"), $request->get("site"));
		   $retour = array("data" => $trousseaux);
		   return new JSonResponse(json_encode($retour));
		}
		return new Response("Error : not and ajax request", 400);
	}

    public function listKey(Request $request){

		$entityManager = $this->getDoctrine()->getManager();
		$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
		$keys = $this->getDoctrine()->getRepository(Trousseau::class)->getListKeys();

        return $this->render('keyring/listKeys.html.twig', array('trousseaux' => $keys, 'params' => $assocArrayParams));
	}
    public function modifyKey(Request $request)
    {

		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		$paramEtat  = $this->getDoctrine()->getRepository(Param::class)->getKeyState();
		$idKey      = ($request->query->get('id'));
		$trousseau  = $this->getDoctrine()->getRepository(Trousseau::class)->find($idKey);
		
        return $this->render('keyring/modifyCle.html.twig', array('trousseau' => $trousseau,'types' => $paramTypes, 'lieux' => $paramLieux, 'etats' => $paramEtat));
	}
    public function newKey (Request $request) {
		$entityManager = $this->getDoctrine()->getManager();

		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramSites = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		$paramEtat = $this->getDoctrine()->getRepository(Param::class)->getKeyState();

        return $this->render('keyring/trousseau.html.twig', array( 'types' => $paramTypes, 'lieux' => $paramSites, 'state' => $paramEtat));
    }

	public function listPret(Request $request) {

		$entityManager = $this->getDoctrine()->getManager();
		$prets = $this->getDoctrine()->getRepository(Pret::class)->findAll();
		$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();

        return $this->render('keyring/listagePret.html.twig', array('prets' => $prets, 'params' => $assocArrayParams));

	}

	/**
	 * @Route("/expiration", name="expiration")
	 */
	public function listExpiration(Request $request) {
		return $this->render('expiration/index.html.twig');
	}


    public function index(Request $request)
    {
        return $this->render('keyring/index.html.twig', array());
    }


	 public function modifyPret(Request $request)
    {
		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		$paramMails = $this->getDoctrine()->getRepository(User::class)->getUserEmail();
		$idPret = ($request->query->get('id'));

		$pret = $this->getDoctrine()->getRepository(Pret::class)->find($idPret);
        return $this->render('keyring/modifyPret.html.twig', array('pret' => $pret,'types' => $paramTypes, 'lieux' => $paramLieux,'mails'=>$paramMails));

	}

	public function deleteKey (Request $request)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$idKey = ($request->query->get('id'));
		if ($idKey == NULL || $this->getDoctrine()->getRepository(Trousseau::class)->find($idKey) == NULL)	{
			return $this->listKey($request);
		}

		$trousseau = $this->getDoctrine()->getRepository(Trousseau::class)->find($idKey);
		$entityManager->remove($trousseau);
		$entityManager->flush();
		return $this->listKey($request);

	}
	
	public function errormsg (Request $request){
		
		return $this->render('keyring/errormsg.html.twig',array());
		
	}

		public function deletePret (Request $request)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$idPret = ($request->query->get('id'));
		if ($idPret == NULL || $this->getDoctrine()->getRepository(Pret::class)->find($idPret) == NULL)	{
			return $this->listPret($request);
		}

		$pret = $this->getDoctrine()->getRepository(Pret::class)->find($idPret);
		$entityManager->remove($pret);
		$entityManager->flush();
		return $this->listPret($request);

	}


    	public function newPret (Request $request) {

		  $paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		  $paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		  $paramMails = $this->getDoctrine()->getRepository(User::class)->getUserEmail();

          return $this->render('keyring/pret.html.twig', array('types' => $paramTypes, 'lieux' => $paramLieux, 'mails' => $paramMails));

		}


  public function saveKey(Request $request) {
	$entityManager = $this->getDoctrine()->getManager();
    $array     = $request->request->all();
	$type      = $array["type"];
	$lieu      = $array["lieu"];
	$reference = $array["reference"];
	$modele    = $array["modele"];
	$etat      = $array["state"];
	$trousseauId = null;
	$trousseau  = null;
	$newKey = $request->request->get('trousseauId') == NULL;

	if ($newKey) {
	    $trousseau = new Trousseau();
	}
	else
	{
	    $trousseauId = $array["trousseauId"];
        $trousseau = $entityManager->getRepository(Trousseau::class)->find($trousseauId);
	}

	$trousseau->setType($type);
	$trousseau->setSite($lieu);
	$trousseau->setRef($reference);
	$trousseau->setModele($modele);
	$trousseau->setState($etat);
	$trousseau->setCreationDate(new DateTime());

	$trousseau->setCreator($this->getUser());
	if ($newKey ) {
	  $entityManager->persist($trousseau);
    }
	$entityManager->flush();


	return $this->listKey ($request);
  }



  public function addKey (Request $request) {
		$entityManager = $this->getDoctrine()->getManager();

		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeyLieu();

        return $this->render('keyring/trousseau.html.twig', array( 'type' => $paramTypes, 'sites' => $paramSites,));
    }

    public function addPret (Request $request){

		$array = $request->request->all();
		$entityManager = $this->getDoctrine()->getManager();
		$array = $request->request->all();
		$newPret = $request->request->get('pretId') == NULL;
		$pret = NULL;

		if ($newPret) {
		  $pret = new Pret();
	   }
	   else {
		   $pretId = $array["pretId"];
           $pret = $entityManager->getRepository(Pret::class)->find($pretId);
	   }

		$pret->setStart(DateTime::createFromFormat('m/d/Y', $array["dateStart"]));
		$pret->setDescription($array["description"]);

		if ($request->request->get("dateEnd") != NULL) {
		   $pret->setEnd(DateTime::createFromFormat('m/d/Y', $array["dateEnd"]));
	    }
	    else {
			$pret->setEnd(NULL);
		}

		$trousseau = $this->getDoctrine()->getRepository(Trousseau::class)->find($array["trousseauId"]);
		$user = $this->getDoctrine()->getRepository(User::class)->find($array["userId"]);

		$pret->setTrousseau($trousseau);
		$pret->setUser($user);

		if ($newPret) {
		  $entityManager->persist($pret);
	    }
		$entityManager->flush();

	    return $this->listPret($request);
	}

	private function sortArray($array)
	{
		$price = array();
		foreach ($array as $key => $row)
		{
			$price[$key] = $row['value'];
		}
		array_multisort($price, SORT_DESC, $array);
		return $array;
	}
}
