<?php
// src/Controller/VIVSController
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

use Dompdf\Dompdf;
use Dompdf\Options;

use \Datetime;

class KeyringController extends AbstractController
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

	public function listKeysAjax(Request $request) 
	{
      if ($request->isXmlHttpRequest())
	    {
		  	$entityManager = $this->getDoctrine()->getManager();
		   	$keys = $this->getDoctrine()->getRepository(Trousseau::class)->getKeys($request->get("type"), $request->get("site"), $request->get("search"));
		   	$retour = array("data" => $keys);
		   	return new JSonResponse(json_encode($keys));
		}
		return new Response("Error : not and ajax request", 400);
	}

	public function getParamsAjax(Request $request)
	{
		if ($request->isXmlHttpRequest())
		{
			$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
			return new JSonResponse(json_encode($assocArrayParams));
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

	public function listTrousseauPerSitesAjax(Request $request) {
		if ($request->isXmlHttpRequest()) 
		{
			$ret = $this->getDoctrine()->getRepository(Trousseau::class)->listTrousseauPerSite();
			$paramsSite = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam(Param::SITE);
			$a = array();
			foreach( $ret as $r) {
				$sa = array();
				$sa[] = $paramsSite[$r["site"]];
				$sa[] = intval($r["COUNT(t.site)"]);
				$a[] = $sa;
			}
			//dump($a);die;
			return new JSonResponse(json_encode($a));
		}
		return new Response("Error : not and ajax request", 400);
	}


	public function listTrousseauPerStateAjax(Request $request) {
		//if ($request->isXmlHttpRequest()) 
		{
			$ret = $this->getDoctrine()->getRepository(Trousseau::class)->listStatePerSite();
			$paramsState = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam(Param::STATE);
			//dump($ret);die();
			$a = array();
			foreach( $ret as $r) {
				$sa = array();
				$sa[] = $paramsState[$r["state"]];
				$sa[] = intval($r["COUNT(t.state)"]);
				$a[] = $sa;
			}
			//dump($a);die;
			return new JSonResponse(json_encode($a));
		}
		return new Response("Error : not and ajax request", 400);
	}

    public function listKey(Request $request){

		$entityManager = $this->getDoctrine()->getManager();
		$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
		$keys = $this->getDoctrine()->getRepository(Trousseau::class)->getListKeys();

        return $this->render('key/list.html.twig', array('trousseaux' => $keys, 'params' => $assocArrayParams));
	}
    public function modifyKey(Request $request)
    {
		$keyId = $request->query->get('id');
		if ($keyId == null)
		{
		  $this->listKey($request);
		}
		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		$paramEtat  = $this->getDoctrine()->getRepository(Param::class)->getKeyState();
		$trousseau  = $this->getDoctrine()->getRepository(Trousseau::class)->find($keyId);
		
        return $this->render('key/modify.html.twig', array('key' => $trousseau,'types' => $paramTypes, 'lieux' => $paramLieux, 'etats' => $paramEtat));
	}

	public function viewKey(Request $request)
    {
		$keyId = $request->query->get('id');
		if ($keyId == null)
		{
		  $this->listKey($request);
		}
		$key    = $this->getDoctrine()->getRepository(Trousseau::class)->find($keyId);
		$prets  = $this->getDoctrine()->getRepository(Pret::class)->getPretByTrousseau($keyId);
		$params = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();

		return $this->render('key/view.html.twig', array('key' => $key, 'prets' => $prets, 'params' => $params));
	}

		public function newKey (Request $request) 
		{
			$entityManager = $this->getDoctrine()->getManager();

			$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
			$paramSites = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
			$paramEtat = $this->getDoctrine()->getRepository(Param::class)->getKeyState();

      return $this->render('key/key.html.twig', array( 'types' => $paramTypes, 'lieux' => $paramSites, 'state' => $paramEtat));
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


	public function listLend(Request $request) {

		$entityManager = $this->getDoctrine()->getManager();
		$prets = $this->getDoctrine()->getRepository(Pret::class)->findAll();
		$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();

		return $this->render('lend/list.html.twig', array('prets' => $prets, 'params' => $assocArrayParams));
	}

	public function modifyPret(Request $request)
	{
		$idPret = $request->query->get('id');
		if ($idPret == null)
		{
			$this->listLend($request);
		}
		$paramTypes = $this->getDoctrine()->getRepository(Param::class)->getKeyType();
		$paramLieux = $this->getDoctrine()->getRepository(Param::class)->getKeySite();
		$paramMails = $this->getDoctrine()->getRepository(User::class)->getUserEmail();

		$lend = $this->getDoctrine()->getRepository(Pret::class)->find($idPret);
		return $this->render('lend/modify.html.twig', array('pret' => $lend,'types' => $paramTypes, 'lieux' => $paramLieux,'mails'=>$paramMails));

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
			return $this->listLend($request);
		}

		$pret = $this->getDoctrine()->getRepository(Pret::class)->find($idPret);
		$entityManager->remove($pret);
		$entityManager->flush();
		return $this->listPret($request);

	}


	public function newLend (Request $request) {

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
	$access    = $array["access"];
	$ticketIn  = $array["ticketIn"];
	$ticketOut = $array["ticketOut"];
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
	$trousseau->setAccess($access);
	$trousseau->setTicketIn($ticketIn);
	$trousseau->setTicketOut($ticketOut);
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

	public function lendPdf(Request $request) 
	{
		$lendId = $request->query->get('id');
		if ($lendId == null)
		{
			$this->listLend($request);
		}

		$lend   = $this->getDoctrine()->getRepository(Pret::class)->find($lendId);
		$userId = $lend->getUser()->getId();
		$user   = $this->getDoctrine()->getRepository(User::class)->find($userId);
		$prets  = $this->getDoctrine()->getRepository(Pret::class)->getPretByUser($userId);
		$params = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();

		$pdfOptions = new Options();
		$pdfOptions->set('defaultFont', 'Arial');
		
		// Instantiate Dompdf with our options
		$dompdf = new Dompdf($pdfOptions);
		
		// Retrieve the HTML generated in our twig file
		$html = $this->renderView('user/viewPdf.html.twig', [
				'user' => $user, "prets" => $prets, "params" => $params
		]);

		// Load HTML to Dompdf
		$dompdf->loadHtml($html);
				
		// (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
		$dompdf->setPaper('A4', 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser (inline view)
		$dompdf->stream("mypdf.pdf", [
				"Attachment" => true
		]);
	}

	public function pdfAction(Request $request)
	{
		$pdfOptions = new Options();
		$pdfOptions->set('defaultFont', 'Arial');
		
		// Instantiate Dompdf with our options
		$dompdf = new Dompdf($pdfOptions);
		
		// Retrieve the HTML generated in our twig file
		$html = $this->renderView('basepdf.html.twig', [
				'title' => "Welcome to our PDF Test"
		]);

		// Load HTML to Dompdf
		$dompdf->loadHtml($html);
        
		// (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
		$dompdf->setPaper('A4', 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser (inline view)
		$dompdf->stream("mypdf.pdf", [
				"Attachment" => true
		]);
	}
}
