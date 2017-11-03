<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserFixtures extends Fixture
{
    public function setContainer( ContainerInterface $container = null )
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load( ObjectManager $manager )
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEmail('admin@gmail.com');
        $user->setUsername('admin');
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->addRole('ROLE_SUPER_ADMIN');

        $userManager->updateUser($user, true);

        $user1 = $userManager->createUser();
        $user1->setEmail('user@gmail.com');
        $user1->setUsername('user');
        $user1->setPlainPassword('user');
        $user1->setEnabled(true);
        $user1->addRole('ROLE_USER');

        $userManager->updateUser($user1, true);

    }
}