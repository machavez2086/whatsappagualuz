<?php

namespace WhatsappBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
class TicketRepository extends EntityRepository {
  
    public function findPreThisId($id, $whatsappGroup){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.id < :id and s.whatsappGroup = :whatsappGroup order by s.id DESC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('id', $id);
        $consulta->setParameter('whatsappGroup', $whatsappGroup);
        return $consulta->getResult();
    }
    
    public function findPosThisId($id, $whatsappGroup){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.id > :id and s.whatsappGroup = :whatsappGroup order by s.id ASC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('id', $id);
        $consulta->setParameter('whatsappGroup', $whatsappGroup);
        return $consulta->getResult();
    }
    
    public function findTicketsThisWeek($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
//        $utc = new \DateTimeZone("UTC");
//        $day->setTimezone($utc);
//        dump($day);
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :startDate and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
    
    public function countTicketsThisWeek($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
//        $utc = new \DateTimeZone("UTC");
//        $day->setTimezone($utc);
//        dump($day);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function ticketsCountByGroupsLastMonths($group, $userTimezone){
        $day = new \DateTime("-1 month", $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('group', $group);
        return $consulta->getSingleScalarResult();
    }
    
    public function ticketsCountByGroupsLastWeek($group, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
//        $utc = new \DateTimeZone("UTC");
//        $day->setTimezone($utc);
//        dump($day);die;
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('group', $group);
        return $consulta->getSingleScalarResult();
    }
    
    public function ticketsCountByGroupsToday($group, $weekday, $hourEndWeek, $userTimezone){
        $day = new \DateTime("today", $userTimezone);
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('group', $group);
        return $consulta->getSingleScalarResult();
    }
    
    public function findTicketsThisDay($configurationId, $weekday, $hourEndWeek, $userTimezone){
//        $day = strtotime("this week");
//        $day = strtotime("-1 week monday 00:00:00");
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
//        dump($day);
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :startDate  and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
    
    public function countTicketsThisDay($configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate  and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function findTicketsThisDayByGroup($group, $configurationId, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->today($weekday, $hourEndWeek, $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
//        $timestamp = strtotime("today");
        
//        $day->setTimestamp($timestamp);
//        dump($day);die;
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :startDate and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('group', $group);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
    public function findTicketsThisWeekByGroup($group, $weekday, $hourEndWeek, $userTimezone){
        $day = $this->lastFriday($weekday, $hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
//        $utc = new \DateTimeZone("UTC");
//        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :startDate and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('group', $group);
        return $consulta->getResult();
    }
    
    public function cantTicketsThisMonth($configurationId, $userTimezone){
        $day = new \DateTime("first day of this month", $userTimezone);
        $day->setTime(0, 0, 0);
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate > :startDate  and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function findLastTicket($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration order by s.startDate DESC ';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
    
      public function cantTicketsOpeneds($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where (s.ticketended is null or  s.ticketended = false) and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
      public function ticketsOpeneds($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where (s.ticketended is null or  s.ticketended = false) and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration order by s.startDate';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
      public function findUnclosed(){
        $day = new \DateTime();
        $timestamp = strtotime("-1 hours");
        $day->setTimestamp($timestamp);
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where (s.ticketended is null or  s.ticketended = false) and s.endDate <:endDate';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('endDate', $day);
        return $consulta->getResult();
    }
    
    
      public function findNotSolvedBySupportMember(){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.solvedBySupportMember is null';
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }
    
      public function findSolvedBySupportMemberAndNotClientSatisfaction(){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.solvedBySupportMember is not null and s.satisfactiondDescritpion is null and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        return $consulta->getResult();
    }
    
    public function ticketsCountByDates($ini, $fin, $configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT count(s) FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate <= :fin and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function ticketsByDates($ini, $fin, $configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate < :fin and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getResult();
    }
    
    public function ticketsByDatesByGroup($ini, $fin, $group){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate < :fin and s.whatsappGroup =:group  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('group', $group);
        return $consulta->getResult();
    }
    
    public function ticketsByDatesBySupportMember($ini, $fin, $supportMember){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate < :fin and s.solvedBySupportMember =:supportMember  and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('supportMember', $supportMember);
        return $consulta->getResult();
    }
    
    public function ticketsByDatesByTicketType($ini, $fin, $ticketType){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s LEFT JOIN s.ticketTypes t where s.startDate >= :ini and s.startDate < :fin and t.id =:ticketType and (s.nofollow is null or s.nofollow = false)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('ticketType', $ticketType);
        return $consulta->getResult();
    }
//    
//    public function ticketsByDatesByTicketType($ini, $fin, $ticketType){
//        $em = $this->getEntityManager();
//        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate < :fin and s.ticketType =:ticketType and (s.nofollow is null or s.nofollow = false)';
//        $consulta = $em->createQuery($dql);
//        $consulta->setParameter('ini', $ini);
//        $consulta->setParameter('fin', $fin);
//        $consulta->setParameter('ticketType', $ticketType);
//        return $consulta->getResult();
//    }
    
    public function ticketsByNotFirstAnswerNotEnded(){
        $em = $this->getEntityManager();
        $time = strtotime("-5 hours");
        $fechalimite = new \DateTime();
        $fechalimite->setTimestamp($time);
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where (s.firstanswer is null or s.firstanswer = false) and (s.ticketended is null or s.ticketended = false) and s.startDate < :fechalimite';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechalimite', $fechalimite);
        return $consulta->getResult();
    }
    
    public function ticketAnteriorToThis($ticket, $group){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.id < :ticket and s.whatsappGroup = :group and (s.nofollow is null or s.nofollow = false) order by s.id desc';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        $consulta->setParameter('ticket', $ticket);
        $consulta->setParameter('group', $group);
        return $consulta->getResult();
    }
    
    
    public function findNoRegisterTickets5HoursAgo(){
        $em = $this->getEntityManager();
        $time = strtotime("-5 hours");
        $fechalimite = new \DateTime();
        $fechalimite->setTimestamp($time);
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.nofollow = true and s.startDate < :fechalimite and s.ticketended = true';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechalimite', $fechalimite);
        return $consulta->getResult();
    }
    
    
    public function sumTicketsAnswerTime($ini, $fin, $configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT sum(s.minutesAnswerTime) FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate <= :fin and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function sumTicketsSolutionTime($ini, $fin, $configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT sum(s.minutesSolutionTime) FROM WhatsappBundle:Ticket s where s.startDate >= :ini and s.startDate <= :fin and (s.nofollow is null or s.nofollow = false) and s.configuration =:configuration';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('ini', $ini);
        $consulta->setParameter('fin', $fin);
        $consulta->setParameter('configuration', $configurationId);
        return $consulta->getSingleScalarResult();
    }
    
    public function getFirtTicket($configurationId){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where (s.nofollow is null or s.nofollow = false) order by s.startDate ASC';
        $consulta = $em->createQuery($dql)->setMaxResults(1);
        return $consulta->getResult();
    }
    
    public function findNoRegisterTicketsOpen30MinsAgo(){
        $em = $this->getEntityManager();
        $time = strtotime("-30 minutes");
        $fechalimite = new \DateTime();
        $fechalimite->setTimestamp($time);
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.nofollow = true and s.startDate < :fechalimite and (s.ticketended = false or s.ticketended is null)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('fechalimite', $fechalimite);
        return $consulta->getResult();
    }
    
     public function findByConfigurations($configurations){
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.configuration in (:configurations)';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('configurations', $configurations);
        return $consulta->getResult();
    }
    
    
    public function findTicketsweeks(){
        //Retorna todos los ticket de hace una semana para analizarlos y corregirlos. Antes se analizaban todos.
        $day = new \DateTime("-1 week");
        $em = $this->getEntityManager();
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :startDate';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter('startDate', $day);
        return $consulta->getResult();
    }
    
    public function ticketsFiltered($page, $limit, $sortBy, $sortOrder, $outOfRange, $configuration, $filters){
        $firstResult = ($page -1)*$limit;
        $qb = $this->getEntityManager()->createQueryBuilder("s");
        $qb->select('s')->from('WhatsappBundle:Ticket' ,'s')   ;
        
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
           
            if($outOfRange != null){
                $outOfRange  = $this->getConvertedValue("outOfRange",$outOfRange);
                $alias = "s";
                if($outOfRange){
                    $qb->andWhere($alias . '.minutesAnswerTime > :minutesAnswerAlert or ' . $alias . '.minutesSolutionTime > :minutesResolutionAlert');
                    $qb->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                    $qb->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                    
                }
                else{
                    $qb->andWhere($alias . '.minutesAnswerTime <= :minutesAnswerAlert and ' . $alias . '.minutesSolutionTime <= :minutesResolutionAlert');
                    $qb->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                    $qb->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                }
            }
//            dump($sortBy);
//            dump($sortOrder);
//            die;
            
        $qb->andWhere('s.configuration = :configuration');
        $qb->setParameter("configuration", $configuration);
        $qb->orderBy("s.".$sortBy, $sortOrder);
        $qb->setFirstResult($firstResult);
        $qb->setMaxResults($limit);
        $query = $qb->getQuery();
//        dump($query);die;
        return $query->getResult();
    }
    
    public function ticketsFilteredCount($page, $limit, $sortBy, $sortOrder, $outOfRange, $configuration, $filters){
        $firstResult = ($page -1)*$limit;
        $qb = $this->getEntityManager()->createQueryBuilder("s");
        $qb->select('count(s)')->from('WhatsappBundle:Ticket' ,'s')   ;
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
        if($outOfRange != null){
                $outOfRange  = $this->getConvertedValue("outOfRange",$outOfRange);
                $alias = "s";
                if($outOfRange){
                    $qb->andWhere($alias . '.minutesAnswerTime > :minutesAnswerAlert or ' . $alias . '.minutesSolutionTime > :minutesResolutionAlert');
                    $qb->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                    $qb->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                    
                }
                else{
                    $qb->andWhere($alias . '.minutesAnswerTime <= :minutesAnswerAlert and ' . $alias . '.minutesSolutionTime <= :minutesResolutionAlert');
                    $qb->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                    $qb->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                }
            }
        $qb->andWhere('s.configuration = :configuration');
        $qb->setParameter("configuration", $configuration);
        $qb->orderBy("s.".$sortBy, $sortOrder);
        $query = $qb->getQuery();
        
//        dump($query);die;
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
        if($key == "startDate" || $key == "endDate" )
            return new \DateTime($value);
        if($key == "outOfRange"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "firstanswer"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "ticketended"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "sendalert"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "sendalertSolution"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "nofollow"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "isValidated"){     
            if($value == "true")
                return true;
            return false;
        }
        if($key == "id"){                 
            return intval($value);
        }
        if($key == "id"){                 
            return doubleval($value);
        }
        if($key == "whatsappGroup"){                 
            return doubleval($value);
        }
        if($key == "solvedBySupportMember"){                 
            return doubleval($value);
        }
        if($key == "ticketType"){                 
            return doubleval($value);
        }
        if($key == "solutionType"){                 
            return doubleval($value);
        }
        if($key == "minutesAnswerTime"){                 
            return floatval($value);
        }
        if($key == "minutesSolutionTime"){                 
            return floatval($value);
        }
        if($key == "minutesDevTime"){                 
            return floatval($value);
        }
        if($key == "minutesValidationWaitTime"){                 
            return floatval($value);
        }
        if($key == "minutesAnswerTime"){                 
            return floatval($value);
        }
        return $value;
    }
    
    public function lastFriday($weekday, $hourEndWeek, $userTimezone) {
        $day = new \DateTime("last ".$weekday." ".$hourEndWeek, $userTimezone);
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
        $friday =  new \DateTime($weekday." ".$hourEndWeek, $userTimezone);
//        $friday =  new \DateTime("friday 17:30:00", $userTimezone);
        $now = new \DateTime("now", $userTimezone);
        if($now > $friday){
           $day = $friday;
        }
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        return $day;
    }
    public function today($weekday, $hourEndWeek, $userTimezone) {
        $day = new \DateTime("today", $userTimezone);
        $friday =  new \DateTime($weekday." ".$hourEndWeek, $userTimezone);
        $now = new \DateTime("now", $userTimezone);
        if($now > $friday){
           $day = $friday;
        }
        return $day;
    }
    
    public function findAllSortDate($limit){
        $em = $this->getEntityManager();
//        dump($dtmessage);
//        dump($ticket);
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s order by s.startDate DESC';
        $consulta = $em->createQuery($dql);
        if($limit != null)
            $consulta->setMaxResults ($limit);
        return $consulta->getResult();
    }
    
    public function findNoSentimetToday(){
        $em = $this->getEntityManager();
        $day = new \DateTime("today");
        $day = new \DateTime("yesterday");
//        dump($day);
        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.sentimentAsureClientMessages is null and s.sentimentasureSupportMessages is null and s.sentimentAsureAllMessages is null and s.sentimentTextblobClientMessages is null and s.sentimentTextblobSupportMessages is null and s.sentimentTextblobAllMessages is null and s.sentimentSpahishClientMessages is null and s.sentimentSpahishSupportMessages is null and s.sentimentSpahishAllMessages is null and s.sentimentVaderClientMessages is null and s.sentimentVaderSupportMessages is null and s.sentimentVaderAllMessages is null and s.startDate > :date';
//        $dql = 'SELECT s FROM WhatsappBundle:Ticket s where s.startDate > :date';
        $consulta = $em->createQuery($dql);
        $consulta->setParameter("date", $day);
        return $consulta->getResult();
    }
    
}
