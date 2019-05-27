<?php
// src/Controller/userController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Param;
use App\Entity\Pret;
use App\Entity\Trousseau;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Security\Core\Security;

use Dompdf\Dompdf;
use Dompdf\Options;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */

    public function index()
    {
        return $this->render('main/addUser.html.twig');
    }

    /**
     * @Route("/adduser", name="adduser")
     */

    public function saveUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
		$entityManager = $this->getDoctrine()->getManager();
		$array = $request->request->all();

		$newUser = $request->request->get('userId') == NULL;
		$User = NULL;

		if ($newUser) {
			$User = new User();
		}
		else
		{
			$userId = $array["userId"];
			$User = $entityManager->getRepository(User::class)->find($userId);
		}
    $User->setRoles(array($array["roles"]));
    $User->setOrigine($array["origine"]);
    $User->setName($array["name"]);
    $User->setFirstName($array["firstname"]);
    $User->setEmail($array["email"]);
    $User->setUsername($array["username"]);
    $User->setFinancement($array["financement"]);
    $User->setEquipe($array["equipe"]);

    if (!empty($array["password"])) {
      $User->setPassword($passwordEncoder->encodePassword($User, $array["password"]));
    }

              // tell Doctrine you want to (eventually) save the Product (no queries yet)
		if ($newUser) {
              $entityManager->persist($User);
		  }

              // actually executes the queries (i.e. the INSERT query)
              $entityManager->flush();

              return $this->listUser($request);
    }

    /**
     * @Route("/listusers", name="listusers")
     */
    public function listUser(Request $request)
    {
      $users = $this->getDoctrine()->getRepository(User::class)->findAll();

      return $this->render('user/listageUsers.html.twig', array('users' => $users));
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


      return $this->render('user/modifyUser.html.twig', array('user' => $id)); //'name' => $name, 'firstname' => $firstname, 'username' => $username, 'email' => $email));

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
}
