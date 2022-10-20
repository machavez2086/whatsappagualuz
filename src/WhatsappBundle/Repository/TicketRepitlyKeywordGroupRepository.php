<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class TicketRepitlyKeywordGroupRepository extends EntityRepository {
   
    public function findByConfigurationByDateRange($configuration, $ini, $fin){
        $em = $this->getEntityManager();
        $utc = new \DateTimeZone("UTC");
        $ini->setTimezone($utc);
        $fin->setTimezone($utc);
        $dql = 'SELECT r.keyword, COUNT(s) AS total FROM WhatsappBundle:TicketRepitlyKeywordGroup s join s.repitlyKeyword r where s.configuration = :configuration and s.startDate >=:ini and s.startDate <= :fin and r.enabled = true group By r.keyword order by total DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setMaxResults(30);
        return $consulta->getResult();
    }
   
    public function findByWhatsappGroupByDateRange($whatsappGroup, $ini, $fin){
        $em = $this->getEntityManager();
        $utc = new \DateTimeZone("UTC");
        $ini->setTimezone($utc);
        $fin->setTimezone($utc);
        $dql = 'SELECT r.keyword, COUNT(s) AS total FROM WhatsappBundle:TicketRepitlyKeywordGroup s join s.repitlyKeyword r where s.whatsappGroup = :whatsappGroup and s.startDate >=:ini and s.startDate <= :fin and r.enabled = true group By r.keyword order by total DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('whatsappGroup', $whatsappGroup);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setMaxResults(15);
        return $consulta->getResult();
    }
}
