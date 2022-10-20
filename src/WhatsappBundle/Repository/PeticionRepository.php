<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class PeticionRepository extends EntityRepository {
      public function findLastTicketToday($configuration){
        $em = $this->getEntityManager();
        $today = new \DateTime("today");
        $dql = 'SELECT s FROM WhatsappBundle:Peticion s where s.configuration =:configuration and s.createdAt >:today order by s.id DESC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('today', $today);
        return $consulta->getResult();
    }
    
    public function findLastTicket($configuration){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Peticion s where s.configuration =:configuration order by s.createdAt DESC ';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
     public function countTicketsThisDay($configuration){
        $day = new \DateTime("today");
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Peticion s where s.createdAt > :createdAt and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
     public function countTicketsThisWeek($configuration){
        $day = new \DateTime();
        $timestamp = strtotime("this week");
        $day->setTimestamp($timestamp);
        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Peticion s where s.createdAt > :createdAt and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('createdAt', $day);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
     public function countTicketsThisMonth($configuration, $ini, $finalDay){
//        $day = new \DateTime();
//        $timestamp = strtotime("first day of last month");
////        $timestamp = strtotime("first day of this month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Peticion s where s.createdAt >= :ini and s.createdAt <= :finalDay and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
     public function countTicketsOpened($configuration){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Peticion s where (s.isFininshed is null or s.isFininshed = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getSingleScalarResult();
    }
    
     public function ticketsOpeneds($configuration){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Peticion s where (s.isFininshed is null or s.isFininshed = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findTicketsThisMonth($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Peticion s where s.createdAt >= :ini and s.createdAt <= :finalDay and s.configuration =:configuration  order by s.createdAt DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findGroupTicketsThisMonthByPeticionType($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Peticion s join s.peticionType r where s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :finalDay group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findGroupTicketsThisMonthByCategory($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Peticion s join s.category r where s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :finalDay group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findGroupTicketsThisMonthByClientActitud($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Peticion s join s.clientActitud r where s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :finalDay group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findGroupTicketsThisMonthByProduct($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Peticion s join s.product r where s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :finalDay group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function findGroupTicketsThisMonthByMotive($configuration, $ini, $finalDay){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Peticion s join s.motive r where s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :finalDay group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('finalDay', $finalDay);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
    public function countGroupTicketsByDatesByPeticionType($configuration, $ini, $fin, $name){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Peticion s join s.peticionType p where p.name = :name and s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :fin';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('name', $name);
        return $consulta->getSingleScalarResult();
    }
    
    public function countGroupTicketsByDatesByCategory($configuration, $ini, $fin, $name){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Peticion s join s.category p where p.name = :name and s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :fin';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('name', $name);
        return $consulta->getSingleScalarResult();
    }
    
    public function countGroupTicketsByDatesByClientActitud($configuration, $ini, $fin, $name){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Peticion s join s.clientActitud p where p.name = :name and s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :fin';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('name', $name);
        return $consulta->getSingleScalarResult();
    }
    
    public function countGroupTicketsByDatesByProduct($configuration, $ini, $fin, $name){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Peticion s join s.product p where p.name = :name and s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :fin';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('name', $name);
        return $consulta->getSingleScalarResult();
    }
    
    public function countGroupTicketsByDatesByMotive($configuration, $ini, $fin, $name){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Peticion s join s.motive p where p.name = :name and s.configuration = :configuration and s.createdAt >= :ini and s.createdAt <= :fin';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('name', $name);
        return $consulta->getSingleScalarResult();
    }
    
    public function countTicketsDistintWhatsappGroup($configuration, $ini, $fin){
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name, r.phoneNumber, r.email FROM WhatsappBundle:Peticion s join s.whatsappGroup r where s.configuration = :configuration and s.createdAt > :ini and s.createdAt <= :fin  group By r.id order by total desc';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
}
