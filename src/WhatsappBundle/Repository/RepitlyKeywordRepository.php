<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class RepitlyKeywordRepository extends EntityRepository {
  public function findByConfigurationByKeyword($configurationId, $keyword){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:RepitlyKeyword s where s.configuration =:configuration and s.keyword=:keyword';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('keyword', $keyword);
        $consulta->setParameter('configuration', $configurationId);
        
        return $consulta->getResult();
    }
}
