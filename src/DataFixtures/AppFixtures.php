<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
{
		$users = $manager->createQuery("SELECT u FROM App:User u")->getResult();
        
        for ($i = 0 ; $i < count($users) ; $i++)
        {
			$users[$i]->setPassword($this->passwordEncoder->encodePassword($users[$i], $users[$i]->getPassword()));
		}
        $manager->flush();
	}
}
