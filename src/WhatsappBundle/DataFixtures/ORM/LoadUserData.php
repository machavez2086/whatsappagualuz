<?php

namespace WhatsappBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Application\Sonata\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword('test');
        $userAdmin->setEmail('admin@test.com');
        $userAdmin->setTimezone('America/Havana');
        $userAdmin->addRole("ROLE_SUPER_ADMIN");
        $manager->persist($userAdmin);
        
        $user = new User();
        $user->setUsername('test');
        $user->setPassword('test');
        $user->setEmail('test@test.com');
        $userAdmin->setTimezone('America/Havana');
        $user->addRole("ROLE_USER");
        $manager->persist($user);
        $manager->flush();
    }
   
}
