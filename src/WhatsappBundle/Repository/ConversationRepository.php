<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class ConversationRepository extends EntityRepository {
    
    public function findGroupTicketsThisMonthByConversationType($configuration, $ini, $fin){
//        $day = new \DateTime();
////        $timestamp = strtotime("first day of this month");
//        $timestamp = strtotime("first day of last month");
//        $day->setTimestamp($timestamp);
//        $day->setTime(0, 0, 0);
        $em = $this->getEntityManager();
//        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Conversation s join s.conversationType r where s.configuration = :configuration and s.day > :createdAt group By r.id';
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Conversation s join s.conversationType r where s.day >= :ini and s.day <= :fin group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
//        $consulta->setParameter('configuration', $configuration);
        return $consulta->getResult();
    }
    
  public function countBetwenDates($dateIni, $dateEnd){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT COUNT(s) FROM WhatsappBundle:Conversation s where s.day >= :dateIni and s.day <= :dateEnd';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dateIni', $dateIni);
        $consulta->setParameter('dateEnd', $dateEnd);
        return $consulta->getSingleScalarResult();
    }
    
  public function countGroupByType($dateIni, $dateEnd){
        
        $em = $this->getEntityManager();
        $dql = 'SELECT r.id, COUNT(s) AS total, r.name FROM WhatsappBundle:Conversation s join s.conversationType r where s.day >= :dateIni and s.day <= :dateEnd group By r.id';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dateIni', $dateIni);
        $consulta->setParameter('dateEnd', $dateEnd);
        return $consulta->getResult();
    }
    
}
