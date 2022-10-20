<?php

namespace WhatsappBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use WhatsappBundle\Entity\Message;
use WhatsappBundle\Entity\Ticket;
use WhatsappBundle\Entity\WhatsappGroup;
use WhatsappBundle\Entity\SupportMember;
use WhatsappBundle\Entity\Configuration;

class LoadTicketsData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
//        insert ticket ahora
//        $now = \DateTime("now");
        $group = new WhatsappGroup();
        $group->setName("Test");
        $manager->persist($group);
        $group1 = new WhatsappGroup();
        $group1->setName("Test 1");
        $manager->persist($group1);
        
        $supportMember = new SupportMember();
        $supportMember->setName("Prueba pepe");
        $supportMember->setPhoneNumber("+5353578485");
        $supportMember->setWhatsappNick("Pepe");
        $manager->persist($supportMember);
                
        $ticket = new Ticket();
        $ticket->setWhatsappGroup($group);
        for ($i = 20; $i >= 0; $i--) {
            $day = new \DateTime();
            $timestamp = strtotime("-".$i." min");                
            $day->setTimestamp($timestamp);
//        dump($day);
            $message = new Message();
            $message->setWhatsappGroup($group);
            $message->setStrmenssagetext("mensaje de prueaba ".$i);
            $message->setEnabled(true);
            if($i == 13){
                $message->setStrmenssagetext("mensaje de prueaba #inicio".$i);
                $message->setSupportFirstAnswer(true);
            }
            if($i == 6){
                $message->setStrmenssagetext("mensaje de prueaba #validacion".$i);
                $message->setIsValidationKeyword(true);
            }
            if($i == 0){
                $message->setStrmenssagetext("mensaje de prueaba #cierre".$i);
            }
            $message->setDtmmessage($day);
            $message->setTicket($ticket);
            $message->setSupportMember($supportMember);
            $manager->persist($message);
            $ticket->addMessages($message);
            
        }
        $configuraion = new Configuration();
        $manager->persist($configuraion);
        $configuraion->setTimeZone("America/Havana");
        $configId = $configuraion->getId();
//        $timezone = $manager->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
        $ticket->setConfiguration($configuraion);
        $ticket->recalculateResolutionTates("America/Havana");
        $ticket->recalculateStartDate();
        $ticket->recalculeValidation();
        
        $manager->persist($ticket);
//        dump($ticket);die;
//        die;
        $manager->flush();
    }
   
}
