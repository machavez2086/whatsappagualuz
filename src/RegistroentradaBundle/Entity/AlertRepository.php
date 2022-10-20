<?php

namespace Deepweb\ClasificadosBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class AlertRepository extends EntityRepository {
  
    public function findBySubcategoria($category, $provincias = null){

    $em = $this->getEntityManager();
    if($provincias != null)
        $dql = 'SELECT s FROM DeepwebClasificadosBundle:Alert s JOIN s.provincia p where s.subcategoria = :category and p.enabled = :provincia ORDER BY s.priority DESC, s.postDate DESC';    
    else
        $dql = 'SELECT s FROM DeepwebClasificadosBundle:Alert s where s.subcategoria = :category and s.provincia is null ORDER BY s.priority DESC, s.postDate DESC';    
    $consulta = $em->createQuery($dql);
    if($provincias != null){
        $consulta->setParameter('provincia', $provincias);
//        $consulta->setParameter('nulo', null);
    }
    $consulta->setParameter('category', $category);
    $consulta->setResultCacheLifetime(3600);
    $val = $consulta->getResult(); 
    return $val;
  }
    
   
  
 
  
  
  
}
