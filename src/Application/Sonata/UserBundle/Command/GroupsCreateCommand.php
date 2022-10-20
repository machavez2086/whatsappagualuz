<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\Group;
use Application\Sonata\UserBundle\Entity\User;
use WhatsappBundle\Entity\Configuration;

class GroupsCreateCommand extends ContainerAwareCommand {

    /**
     * {@inheritdoc}
     */
    public function configure() {
        $this->setName('sacspro:groups:create');
        $this->setDescription('Crea los grupos necesarios');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $grop1 = new Group("Administradores");
        $grop2 = new Group("Call Center");
        $grop3 = new Group("Revisores");
//        $grop4 = new Group("Nuevo");
        
        $grop1->addRole("ROLE_SUPER_ADMIN");
        $grop2->addRole("ROLE_CALL_CENTER");
        $grop3->addRole("ROLE_OBLEA");
//        $grop4->addRole("ROLE_NUEVO");

        $isCreated = $em->getRepository('ApplicationSonataUserBundle:Group')->findByName("Administradores");
        if (count($isCreated) == 0) {
            $em->persist($grop1);
            $em->flush();
        }
        
        $isCreated = $em->getRepository('ApplicationSonataUserBundle:Group')->findByName("Call Center");
        if (count($isCreated) == 0) {
            $em->persist($grop2);
            $em->flush();
        }
        
        $isCreated = $em->getRepository('ApplicationSonataUserBundle:Group')->findByName("Revisores de Obleas");
        if (count($isCreated) == 0) {
            $em->persist($grop3);
            $em->flush();
        }
        $isAdminCreated = $em->getRepository('ApplicationSonataUserBundle:User')->findByUsername("admin");
        if(count($isAdminCreated )==0){
            $admin = new User();
            $admin->setUsername("admin");
            $admin->addRole("ROLE_SUPER_ADMIN");
            $admin->setPlainPassword("admin");
            $admin->setEmail("admin@prueba.com");
            $admin->addGroup($grop1);
            $manipulator = $this->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->create("admin", "admin", "admin@prueba.cu", true, true);
        $manipulator->addRole("admin", "ROLE_ADMIN");
        }
        
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if (count($isCreated) == 0) {
            $defaultConfiguration = new Configuration();
            $defaultConfiguration->setCompany("Default");
            $em->persist($defaultConfiguration);
            $em->flush();
        }

        
    }

}
