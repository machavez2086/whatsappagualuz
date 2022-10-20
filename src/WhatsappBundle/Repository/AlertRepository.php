<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class AlertRepository extends EntityRepository {
    
    public function countTotalAlertsAnswer($configurationId){
      
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('type', "Respuesta");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    public function cantAlertTodayAnswer($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Respuesta");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    public function cantAlertThisWeekAnswer($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
//        $day = new \DateTime("last day this week 00:00:00", $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Respuesta");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function cantAlertThisMonthAnswer($configurationId, $userTimezone){
        $day = new \DateTime("first day of this month", $userTimezone);
        $day->setTime(0, 0, 0);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Respuesta");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function cantAlertOpenAnswer($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.open = True and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('type', "Respuesta");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    
    public function countTotalAlertsSolution($configurationId){
      
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('type', "Resolución");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    public function cantAlertTodaySolution($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Resolución");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    public function cantAlertThisWeekSolution($configurationId, $weekday, $hourEndWeek, $userTimezone){
//        $day = new \DateTime("last day this week 00:00:00", $userTimezone);
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Resolución");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function cantAlertThisMonthSolution($configurationId, $userTimezone){
        $day = new \DateTime("first day of this month", $userTimezone);
        $day->setTime(0, 0, 0);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.sendDate > :sendDate and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('sendDate', $day);
        $consulta->setParameter('type', "Resolución");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function cantAlertOpenSolution($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Alert s where s.open = True and s.type=:type and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('type', "Resolución");
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function findByTicketByEnabled($ticket, $type){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Alert s where s.open = True and s.type=:type and s.ticket = :ticket';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('type', $type);
        $consulta->setParameter('ticket', $ticket);
        return $consulta->getResult();
    }
    
    
    public function findByTicketIsEnabled($ticket){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Alert s where s.open = True and s.ticket = :ticket';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ticket', $ticket);
        return $consulta->getResult();
    }
    
    public function findByTicketNull(){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Alert s where s.open = True and s.ticket is null';
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }
    
    public function lastFriday($weekday, $hourEndWeek, $userTimezone) {
        $day = new \DateTime("last ".$weekday." ".$hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
        $friday =  new \DateTime($weekday." ".$hourEndWeek, $userTimezone);
//        $friday =  new \DateTime("friday 17:30:00", $userTimezone);
        $now = new \DateTime("now", $userTimezone);
        if($now > $friday){
           $day = $friday;
        }
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        return $day;
    }
    public function today($weekday, $hourEndWeek, $userTimezone) {
        $day = new \DateTime("today", $userTimezone);
        $friday =  new \DateTime($weekday." ".$hourEndWeek, $userTimezone);
        $now = new \DateTime("now", $userTimezone);
        if($now > $friday){
           $day = $friday;
        }
        return $day;
    }
}
