<?php
// src/Controller/userController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Param;
use App\Entity\Pret;
use App\Entity\Trousseau;
use App\Entity\Stay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Security\Core\Security;

use \Datetime;

use Dompdf\Dompdf;
use Dompdf\Options;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */

    public function index()
    {
      $paramDepartment = $this->getDoctrine()->getRepository(Param::class)->getDepartment();
      $paramPositions  = $this->getDoctrine()->getRepository(Param::class)->getPositions();
      $nationalities   = Param::getNationality();
      return $this->render('main/addUser.html.twig', array('departments' => $paramDepartment, 'positions' => $paramPositions, "nationalities" => $nationalities));
    }

    public function addUserAjax(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
      $array = $request->request->all();
      $nationality = "FR";
      $host = $array["host"];
      $arrival = $array["dateStart"];
      $departure = $array["dateEnd"];
      $password  = $this->randomPassword(17);
      $user = $this->saveUserInDb(NULL, $array["roles"], $array["origine"], $array["name"], $array["firstname"], $array["email"], NULL, $array["financement"], $array["equipe"], $password, $array["position"], $nationality, $host, $arrival, $departure, true, $passwordEncoder);
      
			return new JSonResponse(json_encode($user));
    }

    public function saveUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
      $entityManager = $this->getDoctrine()->getManager();
      $array = $request->request->all();
      
      $arrival   = in_array("arrival", $array)?$array["arrival"]:null;
      $departure = in_array("departure", $array)?$array["departure"]:null;
      $newUser   = $request->request->get('userId') == NULL;
      //dump($newUser);die();
      $this->saveUserInDb($request->request->get('userId'), $array["roles"], $array["origine"], $array["name"], $array["firstname"], $array["email"], $array["username"], $array["financement"], $array["equipe"], $array["password"], $array["position"], $array["nationality"], $array["host"], $arrival, $departure, $newUser, $passwordEncoder);

      return $this->listUser($request);
    }

    private function randomPassword($size) {
      $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890&é"\'(-è_çà)=}]@^`|[{#~';
      $pass = array(); //remember to declare $pass as an array
      $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
      for ($i = 0; $i < $size; $i++) {
          $n = rand(0, $alphaLength);
          $pass[] = $alphabet[$n];
      }
      return implode($pass); //turn the array into a string
  }

    private function saveUserInDb($userId, $role, $origine, $name, $firstname, $email, $username, $financement, $equipe, $password, $position, $nationality, $host, $arrival, $departure, $newUser, UserPasswordEncoderInterface $passwordEncoder)
    {
      $entityManager = $this->getDoctrine()->getManager();
      $User = NULL;

      if ($newUser) {
        $User = new User();
      }
      else
      {
        $User = $entityManager->getRepository(User::class)->find($userId);
      }
      if ($host == NULL || $host == "") {
        $host = NULL;
      }
      
      if($host != null) {
        $host = $entityManager->getRepository(User::class)->find($host);
      }

      if ($arrival != NULL && !empty($arrival)) {
        $arrival = DateTime::createFromFormat('d/m/Y', $arrival);
      }
      else {
        $arrival = NULL;
      }

      if ($departure != NULL && !empty($departure)) {
        $departure = DateTime::createFromFormat('d/m/Y', $departure);
      }
      else {
        $departure = NULL;
      }

      if ($username == NULL || empty($username)) {
        $username = strtolower($firstname).".".strtolower($name);
      }
      if ($position == NULL || empty($position)) {
        $position = NULL;
      }

      $stay = NULL;
      if ($departure != NULL && $arrival != NULL) {
        $stay = new Stay();
        $stay->setArrival($arrival);
        $stay->setDeparture($departure);
      }

      $User->setRoles(array($role));
      $User->setOrigine($origine);
      $User->setName($name);
      $User->setFirstName($firstname);
      $User->setEmail($email);
      $User->setUsername($username);
      $User->setFinancement($financement);
      $User->setEquipe($equipe);
      $User->setNationality($nationality);
      $User->setHost($host);
      $User->setPosition($position);
      
      $User->setArrival($arrival);
      $User->setDeparture($departure);
  
      if ($password != NULL && !empty($password)) {
        $User->setPassword($passwordEncoder->encodePassword($User, $password));
      }
  
      // tell Doctrine you want to (eventually) save the Product (no queries yet)
      if ($newUser) {
        $entityManager->persist($User);
      }
      if ($stay != NULL) {
        $User->addStay($stay);
        $entityManager->persist($stay);
        $entityManager->persist($User);
      }
  
      // actually executes the queries (i.e. the INSERT query)
      $entityManager->flush();

      return $entityManager->getRepository(User::class)->find($User->getId());
    }

    private function getListUserFilters(Request $request)
    {
      $position   = $request->query->get('position') == NULL?NULL:$request->query->get('position');
      $deptarment = $request->query->get('equipe') == NULL?NULL:$request->query->get('equipe');
      $year = $request->query->get('year') == NULL?NULL:$request->query->get('year');

      $filters = array();
      if ($position != NULL && !empty($position)) {
        $filters["position"] = $position;
      }
      if ($deptarment != NULL && !empty($deptarment)) {
        $filters["equipe"] = $deptarment;
      }
      if ($year != NULL && !empty($year)) {
        $filters["year"] = $year;
      }
      return $filters;
    }

    /**
     * @Route("/listusers", name="listusers")
     */
    public function listUser(Request $request)
    {
      $filters = $this->getListUserFilters($request);
      $users = $this->getDoctrine()->getRepository(User::class)->getUsers($filters);
      $params = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
      $paramPositions  = $this->getDoctrine()->getRepository(Param::class)->getPositions();
      $paramDepartment = $this->getDoctrine()->getRepository(Param::class)->getDepartment();
      $years = $this->getDoctrine()->getRepository(Stay::class)->getDistinctYear();
      //dump($years);die();
      return $this->render('user/listageUsers.html.twig', array('users' => $users, 'params' => $params, 'positions' => $paramPositions, 'departments' => $paramDepartment, 'filters' => $filters, 'years' => $years));
    }

    /**
     * @Route("/deleteuser", name="deleteuser")
     */

    public function deleteUser (Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $idUser = ($request->query->get('id'));
        if ($idUser == NULL) {
          return $this->listUser($request);
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
        if ($user == null) {
          return $this->listUser($request);
        }

        $keys = $this->getDoctrine()->getRepository(Trousseau::class)->listTrousseauByCreator($idUser);
        if ($keys != null || count($keys) > 0) {
          return $this->render('user/replaceCreator.html.twig', array('user' => $user, "keys" => $keys));
        }

        $stays = $this->getDoctrine()->getRepository(Stay::class)->byUser($user->getId());
        foreach ($stays as $stay) {
          $entityManager->remove($stay);
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->listUser($request);

    }

    public function replaceCreatorKey(Request $request) {
      $userId = ($request->query->get('id'));
      if ($userId == NULL) {
        return $this->listUser($request);
      }
      $entityManager = $this->getDoctrine()->getManager();
      $keys = $this->getDoctrine()->getRepository(Trousseau::class)->listTrousseauByCreator($userId);
      //dump($this->getUser());die();

      foreach( $keys as $key) {
        $key->setCreator($this->getUser());
      }
      $entityManager->flush();
      return $this->deleteUser ($request);
    }

    public function profil(Request $request)
    {
      $userNameInSession = $request->getSession()->get(Security::LAST_USERNAME);
      return $this->render("/user/profil.html.twig", array('sessionUserName' => $userNameInSession));
    }

    public function view(Request $request)
    {
      $userId = $request->query->get('id');
      if ($userId == null)
      {
        $this->listUser($request);
      }

      $user   = $this->getDoctrine()->getRepository(User::class)->find($userId);
      $prets  = $this->getDoctrine()->getRepository(Pret::class)->getPretByUser($userId);
      $params = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();

      return $this->render('user/view.html.twig', array('user' => $user, "prets" => $prets, "params" => $params));
    }


    public function viewPdf(Request $request)
    {
      $userId = $request->query->get('id');
      if ($userId == null)
      {
        $this->listUser($request);
      }

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


    public function modifyUser(Request $request)
    {
     $id = $request->query->get('id');

     $array = $request->request->all();
     $id = $this->getDoctrine()->getRepository(User::class)->find($id);
     $paramDepartment = $this->getDoctrine()->getRepository(Param::class)->getDepartment();
     $paramPositions  = $this->getDoctrine()->getRepository(Param::class)->getPositions();
     $nationalities   = Param::getNationality();


      return $this->render('user/modifyUser.html.twig', array('user' => $id, 'departments' => $paramDepartment, 'positions' => $paramPositions, "nationalities" => $nationalities)); //'name' => $name, 'firstname' => $firstname, 'username' => $username, 'email' => $email));

    }

    public function autoComplete(Request $request)
    {
      $q = $request->query->get('q');

      $users = $this->getDoctrine()->getRepository(User::class)->getUserByName($q);

      $json=array();
      foreach ($users as $user)
      {
          array_push($json, array("id" => $user->getId(), "name" => $user->getFirstName()." ".$user->getName()));
      }

      $retour = array();
      $retour["query" ] = $q;
      $retour["data"] = $json;
      return new JSonResponse(json_encode($retour));
    }

    public function listStayAjax(Request $request)
    {
      $userId = $request->get('userId');
      $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
      return new JSonResponse(json_encode($user));
    }

    public function loadStayAjax(Request $request)
    {
      $stayId = $request->get('stayId');
      $stay = $this->getDoctrine()->getRepository(Stay::class)->find($stayId);
      return new JSonResponse(json_encode($stay));
    }
    public function deleteStayAjax(Request $request)
    {
      $stayId = $request->get('stayId');
      $entityManager = $this->getDoctrine()->getManager();
      $stay = $this->getDoctrine()->getRepository(Stay::class)->find($stayId);
      $entityManager->remove($stay);
      $entityManager->flush();
      return new JSonResponse(json_encode($stay));
    }

    public function saveStayAjax(Request $request)
    {
      $userId = $request->get('userId');
      $stayId = $request->get('stayId');
      $arrival = $request->get('arrival');
      $departure = $request->get('departure');
      $entityManager = $this->getDoctrine()->getManager();

      $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
      
      $stay = null;
      if(isset($stayId) && $stayId != "") {
        $stay = $this->getDoctrine()->getRepository(Stay::class)->find($stayId);
      } else {
        $stay = new Stay();
      }
      $stay->setArrival(DateTime::createFromFormat('d/m/Y', $arrival));
      $stay->setDeparture(DateTime::createFromFormat('d/m/Y', $departure));
      $user->addStay($stay);
      

      if(!isset($stayId) || $stayId == "") {
		    $entityManager->persist($stay);
      }
      $entityManager->flush();
      $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
      return new JSonResponse(json_encode($user));
    }
	
	public function excel(Request $request)
  {
    $spreadsheet = new Spreadsheet();
        
    $entityManager = $this->getDoctrine()->getManager();
    
    $filters = $this->getListUserFilters($request);

    $users = $this->getDoctrine()->getRepository(User::class)->getUsers($filters);
    $params = $this->getDoctrine()->getRepository(Param::class)->getAssociativeArrayParam();
        
		// @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet 
		$sheet = $spreadsheet->getActiveSheet();
		$line = 2;
		$sheet->setCellValue("A1", "Nom & Prénom");
		$sheet->setCellValue("B1", "Position");
		$sheet->setCellValue("C1", "Etablissement d'origine");
		$sheet->setCellValue("D1", "Nationalité");
		$sheet->setCellValue("E1", "Responsable scientifique de l'acceuil");
		$sheet->setCellValue("F1", "Début");
		$sheet->setCellValue("G1", "Fin");
		$sheet->setCellValue("H1", "Financement");
		$sheet->setCellValue("I1", "Site");
    $sheet->setCellValue("J1", "Equipe");
    $nationalities = Param::getNationality();
		foreach ($users as $user ) {
			//dump($lend);die();
			$column = 'A';
			$sheet->setCellValue($column . strval($line), $user->getFirstName() . " " . $user->getName());
			$column++;
			$sheet->setCellValue($column . strval($line), $params[$user->getPosition()]);
			$column++;
			$sheet->setCellValue($column . strval($line), $user->getOrigine());
			$column++;
			$sheet->setCellValue($column . strval($line), $nationalities[$user->getNationality()]);
			$column++;
			$sheet->setCellValue($column . strval($line), $user->getHost());
			$column++;
			$sheet->setCellValue($column . strval($line), $user->getArrival()==NULL?"":$user->getArrival()->format('d/m/Y'));
			$column++;
			$sheet->setCellValue($column . strval($line), $user->getDeparture()==NULL?"":$user->getDeparture()->format('d/m/Y'));
			$column++;
			$sheet->setCellValue($column . strval($line), $user->getFinancement());
			$column++;
			$sheet->setCellValue($column . strval($line), "");
			$column++;
			$sheet->setCellValue($column . strval($line), $params[$user->getEquipe()]);
			$column++;
			$line++;
		}
		$sheet->setTitle("My First Worksheet");
		
		// Create your Office 2007 Excel (XLSX Format)
		$writer = new Xlsx($spreadsheet);
		
		// In this case, we want to write the file in the public directory
		//$publicDirectory =  $this->getDoctrine()->getRepository(Pret::class); //$this->get('kernel')->getProjectDir() . '/public';
		// e.g /var/www/project/public/my_first_excel_symfony4.xlsx
		$fileName = 'my_first_excel_symfony4.xlsx';
		$temp_file = tempnam(sys_get_temp_dir(), $fileName);
		$writer->save($temp_file);
		//dump($temp_file);die();
		
		// Return the excel file as an attachment
		return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
	}
}
