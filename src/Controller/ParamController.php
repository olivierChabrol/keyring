<?php
// src/Controller/VIVSController
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Controller keyring.
 *
 * @author Hugo BLACHERE <hugo.blachere@etu.univ-amu.fr>
 * @author Olivier CHABROL <olivier.chabrol@univ-amu.fr>
 * @author Maximilien GUERRERO <maximilien.guerrero@etu.univ-amu.fr>
 */

class ParamController extends AbstractController
{
	public function getParamsAjax(Request $request)
	{
		if ($request->isXmlHttpRequest())
		{
			$assocArrayParams = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
			return new JSonResponse(json_encode($assocArrayParams));
		}
		return new Response("Error : not and ajax request", 400);
	}
    
	public function list(Request $request)
	{
        $params = $this->getDoctrine()->getRepository(Param::class)->findAll();
        $paramTypes = Param::getTypes();
        return $this->render('param/list.html.twig', array('params' => $params, 'paramTypes' => $paramTypes));
    }

    public function modify(Request $request)
    {
		$paramId = $request->query->get('id');
		if ($paramId == null)
		{
		  $this->list($request);
        }
        $param = $this->getDoctrine()->getRepository(Param::class)->find($paramId);
        $paramTypes = Param::getTypes();
        //dump($paramTypes);die();
        return $this->render('param/modify.html.twig', array('param' => $param, 'paramTypes' => $paramTypes));
    }

    public function delete(Request $request)
    {
		$paramId = $request->query->get('id');
		if ($paramId == null)
		{
		  $this->list($request);
        }
	    $entityManager = $this->getDoctrine()->getManager();
		$param = $this->getDoctrine()->getRepository(Param::class)->find($paramId);
		$entityManager->remove($param);
		$entityManager->flush();
		return $this->list($request);

    }

    public function new(Request $request)
    {
        $paramTypes = Param::getTypes();
        return $this->render('param/new.html.twig', array('paramTypes' => $paramTypes));
    }

    public function save(Request $request)
    {
        $array   = $request->request->all();
        $type    = $array["type"];
        $value    = $array["value"];
        $paramId = null;
        $param   = null;
        $newKey = $request->request->get('paramId') == NULL;
    
	    $entityManager = $this->getDoctrine()->getManager();
        if ($newKey) {
            $param = new Param();
        }
        else
        {
            $paramId = $array["paramId"];
            $param = $entityManager->getRepository(Param::class)->find($paramId);
        }
        $param->setType($type);
        $param->setValue($value);

        if ($newKey ) {
            $entityManager->persist($param);
        }
        $entityManager->flush();
    
        return $this->list($request);
    }
}