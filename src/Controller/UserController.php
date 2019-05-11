<?php
// src/Controller/userController.php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Param;
use App\Entity\Pret;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Security\Core\Security;

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
        $User->setPassword($passwordEncoder->encodePassword($User, $array["password"]));

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
        if ($idUser == NULL || $this->getDoctrine()->getRepository(User::class)->find($idUser) == NULL)    {
        return $this->listUser($request);
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($idUser);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->listUser($request);

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
