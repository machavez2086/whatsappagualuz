<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class UserCompanyRepository extends EntityRepository {
    
    
    public function findByConfigurationByUser($configuration, $user){
      
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:UserCompany s where s.configuration=:configuration and s.user=:user';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configuration);
        $consulta->setParameter('user', $user);
        return $consulta->getResult();
    }
}
