<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request)
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $userNameInSession = $request->getSession()->get(Security::LAST_USERNAME);

      return $this->render('main/main.html.twig', array('sessionUserName' => $userNameInSession));
    }
}
