<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class MessageRepository extends EntityRepository {
     public function findPosThisIdDate($dtmessage, $ticket){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.dtmmessage > :dtmessage and s.ticket = :ticket order by s.dtmmessage ASC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dtmessage', $dtmessage);
        $consulta->setParameter('ticket', $ticket->getId());
        return $consulta->getResult();
    }
     public function findByTicketOrderDt($ticket){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.ticket = :ticket order by s.dtmmessage ASC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ticket', $ticket->getId());
        return $consulta->getResult();
    }
     public function findByTicketFirstAnswere($ticket){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.ticket = :ticket and s.supportFirstAnswer = true order by s.dtmmessage ASC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('ticket', $ticket->getId());
        return $consulta->getResult();
    }
    
     public function lastMessageFromGroup($group){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.whatsappGroup = :group order by s.dtmmessage DESC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('group', $group);
        return $consulta->getResult();
    } 
    
     public function messagesFiltered($page, $limit, $sortBy, $sortOrder, $configuration, $filters){
        $firstResult = ($page -1)*$limit;
        $qb = $this->getEntityManager()->createQueryBuilder("s");
        $qb->select('s')->from('WhatsappBundle:Message' ,'s')   ;
        
        $count = 0;
        foreach ($filters as $key => $filter) {
            $filterIdType = $filter["type"];
            $filterIdValue = $filter["value"];
            
            //filtros de id
            $filterIdTypePass = false;
            if($filterIdType){
                if($this->getBetweenOrNotBetweenType($filterIdType)){
                    
                    if($filterIdType == "between"){
                        if(trim($filterIdValue["start"]) != ""){
                            $qb->andWhere("s.".$key." >="." :filterIdValue".$count."start");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                        }
                        if(trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("s.".$key." <="." :filterIdValue".$count."end");
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                    }
                    else{
                        if(trim($filterIdValue["start"]) != "" && trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("(s.".$key." <"." :filterIdValue".$count."start "." or s.".$key." >"." :filterIdValue".$count."end)");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                        elseif(trim($filterIdValue["start"]) != "" && trim($filterIdValue["end"]) == ""){
                            $qb->andWhere("s.".$key." <"." :filterIdValue".$count."start ");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                        }
                        elseif(trim($filterIdValue["start"]) == "" && trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("s.".$key." >"." :filterIdValue".$count."end ");
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                    }
                    $filterIdTypePass = true;
                }
                elseif($this->getLikeOrNotLikeType($filterIdType) && trim($filterIdValue) != ""){
                    
                    $qb->andWhere("s.".$key." ".$this->getLikeOrNotLikeType($filterIdType)." :filterIdValue".$count);

                    $filterLikeIdValue = $filterIdValue;
                    if (strpos($filterLikeIdValue,"%") !== true){
                        $filterLikeIdValue = "%".$filterLikeIdValue."%";
                    }
                    $qb->setParameter("filterIdValue".$count, $filterLikeIdValue);
                    $filterIdTypePass = true;
                    
                }
                elseif($this->getNullOrNotNullType($filterIdType)){
                    
                    $qb->andWhere("s.".$key." ".$this->getNullOrNotNullType($filterIdType));
                    $filterIdTypePass = true;
                }
                elseif($this->getEmptyOrNotEmptyType($filterIdType)){
                    
                    $filterIdTypePass = true;
                    $qb->andWhere("s.".$key." ".$this->getEmptyOrNotEmptyType($filterIdType)." :filterIdValue".$count);
                    $qb->setParameter("filterIdValue".$count, "");
                }
            }
            
            if($filterIdValue != null && !$filterIdTypePass){
                
                $qb->andWhere("s.".$key." ".$this->getElementByType($filterIdType)." :filterIdValue".$count);
                $qb->setParameter("filterIdValue".$count, $this->getConvertedValue($key, $filterIdValue));
            }
            
            $count = $count+1;
        }
        
        $qb->andWhere('s.configuration = :configuration');
        $qb->setParameter("configuration", $configuration);
        $qb->orderBy("s.".$sortBy, $sortOrder);
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function messagesFilteredCount($page, $limit, $sortBy, $sortOrder, $configuration, $filters){
        $firstResult = ($page -1)*$limit;
        $qb = $this->getEntityManager()->createQueryBuilder("s");
        $qb->select('count(s)')->from('WhatsappBundle:Message' ,'s')   ;
        $count = 0;
        foreach ($filters as $key => $filter) {
            $filterIdType = $filter["type"];
            $filterIdValue = $filter["value"];
            //filtros de id
            $filterIdTypePass = false;
            if($filterIdType){
                if($this->getBetweenOrNotBetweenType($filterIdType)){
                    if($filterIdType == "between"){
                        if(trim($filterIdValue["start"]) != ""){
                            $qb->andWhere("s.".$key." >="." :filterIdValue".$count."start");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                        }
                        if(trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("s.".$key." <="." :filterIdValue".$count."end");
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                    }
                    else{
                        if(trim($filterIdValue["start"]) != "" && trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("(s.".$key." <"." :filterIdValue".$count."start "." or s.".$key." >"." :filterIdValue".$count."end)");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                        elseif(trim($filterIdValue["start"]) != "" && trim($filterIdValue["end"]) == ""){
                            $qb->andWhere("s.".$key." <"." :filterIdValue".$count."start ");
                            $qb->setParameter("filterIdValue".$count."start", $this->getConvertedValue($key, $filterIdValue["start"]));
                        }
                        elseif(trim($filterIdValue["start"]) == "" && trim($filterIdValue["end"]) != ""){
                            $qb->andWhere("s.".$key." >"." :filterIdValue".$count."end ");
                            $qb->setParameter("filterIdValue".$count."end", $this->getConvertedValue($key, $filterIdValue["end"]));
                        }
                    }
                    $filterIdTypePass = true;
                }
                elseif($this->getLikeOrNotLikeType($filterIdType) && trim($filterIdValue) != ""){
                    $qb->andWhere("s.".$key." ".$this->getLikeOrNotLikeType($filterIdType)." :filterIdValue".$count);

                    $filterLikeIdValue = $filterIdValue;
                    if (strpos($filterLikeIdValue,"%") !== true){
                        $filterLikeIdValue = "%".$filterLikeIdValue."%";
                    }
                    $qb->setParameter("filterIdValue".$count, $filterLikeIdValue);
                    $filterIdTypePass = true;
                    
                }
                elseif($this->getNullOrNotNullType($filterIdType)){
                    $qb->andWhere("s.".$key." ".$this->getNullOrNotNullType($filterIdType));
                    $filterIdTypePass = true;
                }
                elseif($this->getEmptyOrNotEmptyType($filterIdType)){
                    $filterIdTypePass = true;
                    $qb->andWhere("s.".$key." ".$this->getEmptyOrNotEmptyType($filterIdType)." :filterIdValue".$count);
                    $qb->setParameter("filterIdValue".$count, "");
                }
            }
            if($filterIdValue != null  && !$filterIdTypePass){
                $qb->andWhere("s.".$key." ".$this->getElementByType($filterIdType)." :filterIdValue".$count);
                $qb->setParameter("filterIdValue".$count, $this->getConvertedValue($key, $filterIdValue));
            }
            
            $count = $count+1;
        }
        
        $qb->andWhere('s.configuration = :configuration');
        $qb->setParameter("configuration", $configuration);
        $qb->orderBy("s.".$sortBy, $sortOrder);
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }
    
    private function getElementByType($type)
    {
        if($type == "eq")
            return "=";
        if($type == "ne")
            return "<>";
        if($type == "lt")
            return "<";
        if($type == "lte")
            return "<=";
        if($type == "gt")
            return ">";
        if($type == "gte")
            return ">=";
       return null;
    }
    private function getNullOrNotNullType($type)
    {
        if($type == "isnull")
            return "is null";
        if($type == "isnotnull")
            return "is not null";
        return null;
    }
    private function getEmptyOrNotEmptyType($type)
    {
        if($type == "isempty")
            return "=";
        if($type == "isnotempty")
            return "<>";
        return null;
    }
    
    private function getLikeOrNotLikeType($type)
    {
        if($type == "like")
            return "like";
        if($type == "notlike")
            return "not like";
        return null;
    }
    
    private function getBetweenOrNotBetweenType($type)
    {
        if($type == "between" || $type == "notbetween")
            return true;
        return false;
    }
    
    private function getConvertedValue($key, $value)
    {
        if($key == "dtmmessage")
            return new \DateTime($value);
        
        if($key == "supportFirstAnswer"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "isValidationKeyword"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "id"){                 
            return doubleval($value);
        }
        if($key == "ticket"){                 
            return doubleval($value);
        }
        if($key == "whatsappGroup"){                 
            return doubleval($value);
        }
        if($key == "supportMember"){                 
            return doubleval($value);
        }
        if($key == "clientMember"){                 
            return doubleval($value);
        }
        return $value;
    }
    
     public function findLast15ByConversation($conversation){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.conversation = :conversation order by s.dtmmessage DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('conversation', $conversation);
        $consulta->setMaxResults(15);
        return $consulta->getResult();
    }
    
      public function findLast15ByWhatsappGroup($whatsappGroup){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Message s where s.whatsappGroup = :whatsappGroup order by s.dtmmessage DESC';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('whatsappGroup', $whatsappGroup);
        $consulta->setMaxResults(500);
        return $consulta->getResult();
    }
    
    public function countMessagesFromMe($dateIni, $dateEnd){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Message s where s.dtmmessage >= :dateIni and s.dtmmessage <= :dateEnd and s.fromMe = true and s.messageNumber is not null and s.conversation is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dateIni', $dateIni);
        $consulta->setParameter('dateEnd', $dateEnd);
        return $consulta->getSingleScalarResult();
    }
    
    public function countMessagesNotFromMe($dateIni, $dateEnd){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Message s where s.dtmmessage >= :dateIni and s.dtmmessage <= :dateEnd and (s.fromMe = false or s.fromMe is null) and s.messageNumber is not null and s.conversation is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dateIni', $dateIni);
        $consulta->setParameter('dateEnd', $dateEnd);
        return $consulta->getSingleScalarResult();
    }
    
    public function countMessages($dateIni, $dateEnd){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Message s where s.dtmmessage > :dateIni and s.dtmmessage <= :dateEnd and s.messageNumber is not null and s.conversation is not null';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('dateIni', $dateIni);
        $consulta->setParameter('dateEnd', $dateEnd);
        return $consulta->getSingleScalarResult();
    }
    
}
