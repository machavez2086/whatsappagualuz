<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class ClientMemberRepository extends EntityRepository {
  
    public function findByConfigurations($configurations){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:ClientMember s where s.configuration in (:configurations)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configurations', $configurations);
        return $consulta->getResult();
    }
}
