<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class TicketSendedAreaRepository extends EntityRepository {
  public function countTicketSendedAreaThisDay($configuration){
        $day = new \DateTime("today");
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt > :createdAt and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
    public function countTicketSendedAreaThisWeek($configuration){
        $day = new \DateTime();
        $timestamp = strtotime("this week");
        $day->setTimestamp($timestamp);
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt > :createdAt and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
    public function countTicketSendedAreaThisMonth($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt >= :ini and s.createdAt <= :finalDay and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
  public function countTicketSendedAreaAnsweredThisDay($configuration){
        $day = new \DateTime("today");
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt > :createdAt and s.configuration =:configuration and s.answer is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
    public function countTicketSendedAreaAnsweredThisWeek($configuration){
        $day = new \DateTime();
        $timestamp = strtotime("this week");
        $day->setTimestamp($timestamp);
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt > :createdAt and s.configuration =:configuration and s.answer is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
    public function countTicketSendedAreaAnsweredThisMonth($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:TicketSendedArea s where s.createdAt >= :ini and s.createdAt <= :finalDay and s.configuration =:configuration and s.answer is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
}
