<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class ConfigurationRepository extends EntityRepository {
  public function getTimezoneFromConfiguration($id){
        $em = $this->getEntityManager();
        $dql = 'SELECT s.timeZone FROM WhatsappBundle:Configuration s where s.id = :id';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('id', $id);
        return $consulta->getResult();
    }
}
