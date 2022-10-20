<?php

namespace WhatsappBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use WhatsappBundle\Entity\Configuration;

class LoadConfigurationData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $entity = new Configuration();
        $entity->setId(1);
        $entity->setMinutesAnswerAlert(5);
        $entity->setMinutesResolutionAlert(30);
//        $entity->setStatusPhoneConected(true);
//        $entity->setSynchronizationEmailStatus(true);
        $manager->persist($entity);
        $manager->flush();
    }
   
}
