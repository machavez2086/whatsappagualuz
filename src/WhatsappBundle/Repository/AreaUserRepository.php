<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class AreaUserRepository extends EntityRepository {
  public function findByUserByArea($user, $area){
      $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:AreaUser s where s.user = :user and s.area = :area';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('user', $user);
        $consulta->setParameter('area', $area);
        return $consulta->getResult();
    }
}
