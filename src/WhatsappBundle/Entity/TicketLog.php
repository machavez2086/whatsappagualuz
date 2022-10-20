<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\TicketLogRepository")
 * @ORM\Table(name="ticketlog")
 * @ORM\HasLifecycleCallbacks()
 */
class TicketLog
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="oldid", type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $oldId;
    
    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="sendalert", type="boolean", nullable=true)
     */
    private $sendalert;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     * @Assert\NotBlank
     */
    private $name;
    
    
    /**
     * @var string $deletedByUsername
     *
     * @ORM\Column(name="deletedbyusername", type="string", nullable=true)
     
     */
    private $deletedByUsername;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="tickettypes", type="text", nullable=true)
     * @Assert\NotBlank
     */
    private $ticketTypes;
    
    /**
     * @var string $startDate
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    private $startDate;
    
    /**
     * @var string $startDate
     *
     * @ORM\Column(name="deleteddate", type="datetime", nullable=true)
     */
    private $deletedDate;
    
    
    /**
     * @var string $startTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=true)
     */
    private $startTime;
    
    function getStartTime() {
        
        return $this->startTime;
        return $dateAux->format("H:i:s");
    }
    function getStartTimeText() {
        if($this->startTime == null)
             return null;
        if($this->configuration == null)
             return null;
//        return $this->startTime->format("H:i:s");
        $timezone = new \DateTimeZone($this->configuration->getTimezone());
        $dateAux = clone $this->startTime;
        $dateAux->setTimezone($timezone);
        return $dateAux->format("H:i:s");
    }

    function setStartTime($startTime) {
        $this->startTime = $startTime;
    }
    
    /**
     * @var string $endDate
     *
     * @ORM\Column(name="enddate", type="datetime", nullable=true)
     */
    private $endDate;
    
    /**
     * @var string $satisfactiondDescritpion
     *
     * @ORM\Column(name="satisfactiondescritpion", type="string", nullable=true)
     */
    private $satisfactiondDescritpion;
    
    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="satisfaction", type="float", nullable=true)
     */
    private $satisfaction;
    
    /**
     * @var string $minutesSolutionTime
     *
     * @ORM\Column(name="minutessolutiontime", type="float", nullable=true)
     */
    private $minutesSolutionTime;
    
    /**
     * @var string $minutesAnswerTime
     *
     * @ORM\Column(name="minutesanswertime", type="float", nullable=true)
     */
    private $minutesAnswerTime;
    
    /**
     * @var string $solution
     * Como se resolvio
     * @ORM\Column(name="solution", type="text", nullable=true)
     */
    private $solution;
    
    /**
     * @var string $solution
     * Como se resolvio
     * @ORM\Column(name="whatsappgroupname", type="string", nullable=true)
     */
    protected $whatsappGroupName;

    /**
     * @var string $solution
     * Como se resolvio
     * @ORM\Column(name="solvedbysupportmembername", type="string", nullable=true)
     */
    protected $solvedBySupportMemberName;
   
    /**
     * cuando ha pasado mas de una hora y no se ha cerrado genera una alerta de resolucion
     * @var string $satisfaction
     *
     * @ORM\Column(name="sendalertsolution", type="boolean", nullable=true)
     */
    private $sendalertSolution;

    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="firstanswer", type="boolean", nullable=true)
     */
    private $firstanswer;

    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="ticketended", type="boolean", nullable=true)
     */
    private $ticketended;

    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="nofollow", type="boolean", nullable=true)
     */
    private $nofollow;
    
    /**
     * @var string $isValidated
     *
     * @ORM\Column(name="isvalidated", type="boolean", nullable=true)
     */
    private $isValidated;
    
    /**
     * @var float $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentasureallmessages", type="float", nullable=true)
     * 
     */
    private $sentimentAsureAllMessages;
    
    /**
     * @var string $solution
     * Como se resolvio
     * @ORM\Column(name="solutiontypename", type="string", nullable=true)
     */
    private $solutionTypeName;    
    
    
    function getId() {
        return $this->id;
    }

  
    function setId($id) {
        $this->id = $id;
    }

    function getName() {
        return $this->name;
    }

    function setName($name) {
        $this->name = $name;
    }
    
    function getMinutesSolutionTime() {
        return round($this->minutesSolutionTime,2);
        
    }

    function setMinutesSolutionTime($minutesSolutionTime) {
        $this->minutesSolutionTime = $minutesSolutionTime;
    }

    function getStartDate() {
        if($this->configuration == null)
             return null;
//        $timezone = new \DateTimeZone($this->configuration->getTimezone());
//        $this->startDate->setTimezone($timezone);
        return $this->startDate;
    }
    
    function getStartDateText() {
        if($this->startDate == null)
             return null;
        if($this->configuration == null)
             return null;
        $timezone = new \DateTimeZone($this->configuration->getTimezone());
        $dateAux = clone $this->startDate;
        $dateAux->setTimezone($timezone);
        return $dateAux;
    }

    function getEndDate() {
        return $this->endDate;
    }

    function getEndDateText() {
        if($this->endDate == null)
             return null;
        if($this->configuration == null)
             return null;
        $timezone = new \DateTimeZone($this->configuration->getTimezone());
        $dateAux = clone $this->endDate;
        $dateAux->setTimezone($timezone);
        return $dateAux;
    }

    function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    function getSolutionTypeName() {
        return $this->solutionTypeName;
    }

    function getSolution() {
        return $this->solution;
    }

    function getSolvedBySupportMemberName() {
        return $this->solvedBySupportMemberName;
    }

    function setTickets($tickets) {
        $this->tickets = $tickets;
    }


    function setSolutionTypeName($solutionTypeName) {
        $this->solutionTypeName = $solutionTypeName;
    }

    function setSolution($solution) {
        $this->solution = $solution;
    }

    function setSolvedBySupportMemberName($solvedBySupportMember) {
        $this->solvedBySupportMemberName = $solvedBySupportMember;
    }
    
    function getSatisfactiondDescritpion() {
        return $this->satisfactiondDescritpion;
    }

    function getSatisfaction() {
        return $this->satisfaction;
    }

    function setSatisfactiondDescritpion($satisfactiondDescritpion) {
        $this->satisfactiondDescritpion = $satisfactiondDescritpion;
    }

    function setSatisfaction($satisfaction) {
        $this->satisfaction = $satisfaction;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->name;
    }
    
    function getSendalert() {
        return $this->sendalert;
    }

    function setSendalert($sendalert) {
        $this->sendalert = $sendalert;
    }
    function getFirstanswer() {
        return $this->firstanswer;
    }

    function setFirstanswer($firstanswer) {
        $this->firstanswer = $firstanswer;
    }

    function getTicketended() {
        return $this->ticketended;
    }

    function setTicketended($ticketended) {
        $this->ticketended = $ticketended;
    }
    function getSendalertSolution() {
        return $this->sendalertSolution;
    }

    function setSendalertSolution($sendalertSolution) {
        $this->sendalertSolution = $sendalertSolution;
    }

    function getAlerts() {
        return $this->alerts;
    }

    function setAlerts($alerts) {
        $this->alerts = $alerts;
    }
    
    function getMinutesAnswerTime() {
        return round($this->minutesAnswerTime,2);
    }

    function setMinutesAnswerTime($minutesAnswerTime) {
        $this->minutesAnswerTime = $minutesAnswerTime;
    }
    
    function getNofollow() {
        return $this->nofollow;
    }

    function setNofollow($nofollow) {
        $this->nofollow = $nofollow;
    }

    
    /**
     * cuando ha pasado mas de una hora y no se ha cerrado genera una alerta de resolucion
     * @var string $satisfaction
     *
     * @ORM\Column(name="weekday", type="string", nullable=true)
     */
    private $weekday;
    
    function getWeekday() {
        return $this->weekday;
        return $this->SpanishDate($this->getDayOfWeek($this->startDate));
    }

    function setWeekday($weekday) {
        $this->weekday = $weekday;
    }

    public function getDayOfWeek($date)
    {
        if($date){
            $date = clone $date;
            //Poner la zona horaria correcta
            $timezone = new \DateTimeZone($this->configuration->getTimezone());
            $date->setTimezone($timezone);
            return date('l', strtotime( $date->format('Y-m-d')));
        }
        return null; 
    }
    function SpanishDate($weekDay)
    {
       $diassemanaN= array("Sunday"=>"Domingo","Monday" => "Lunes","Tuesday" => "Martes", "Wednesday" => "Miércoles",
                      "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado");
       return $diassemanaN[$weekDay];
    } 
    
    
     private $resolutionDate;
     function getResolutionDate() {
         $resolutionDate = $this->getStartDate();
         $minutes_to_add = intval($this->minutesAnswerTime*60);
         if($minutes_to_add < 0){
             $minutes_to_add = -1*$minutes_to_add;
         }
         if($minutes_to_add && $resolutionDate)
         $resolutionDate->add(new \DateInterval('PT' . $minutes_to_add . 'S'));
         return $resolutionDate;
     }
     function getResolutionDateText() {
         if($this->getStartDate() == null)
             return null;
         if($this->configuration == null)
             return null;
         $resolutionDate = clone $this->getStartDate();
         $minutes_to_add = intval($this->minutesAnswerTime*60);
         if($minutes_to_add < 0){
             $minutes_to_add = -1*$minutes_to_add;
         }
         if($minutes_to_add && $resolutionDate)
         $resolutionDate->add(new \DateInterval('PT' . $minutes_to_add . 'S'));
         $timezone = new \DateTimeZone($this->configuration->getTimezone());
            $resolutionDate->setTimezone($timezone);
         return $resolutionDate;
     }

     function setResolutionDate($resolutionDate) {
         $this->resolutionDate = $resolutionDate;
     }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="tickets")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id", onDelete="SET NULL") 
    * @Assert\NotBlank
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    
    /**
     * @var string $validationCount
     *
     * @ORM\Column(name="validationcount", type="integer", nullable=true)
     */
    private $validationCount;
    
    
    /**
     * @var string $minutesDevTime
     *
     * @ORM\Column(name="minutesdevtime", type="float", nullable=true)
     */
    private $minutesDevTime;
    
    /**
     * @var string $minutesValidationWaitTime
     *
     * @ORM\Column(name="minutesvalidationwaittime", type="float", nullable=true)
     */
    private $minutesValidationWaitTime;
    
    function getValidationCount() {
        return $this->validationCount;
    }

    function getMinutesDevTime() {
        return round($this->minutesDevTime,2);
    }

    function getMinutesValidationWaitTime() {
        return round($this->minutesValidationWaitTime,2);
    }

    function setValidationCount($validationCount) {
        $this->validationCount = $validationCount;
    }

    function setMinutesDevTime($minutesDevTime) {
        $this->minutesDevTime = $minutesDevTime;
    }

    function setMinutesValidationWaitTime($minutesValidationWaitTime) {
        $this->minutesValidationWaitTime = $minutesValidationWaitTime;
    }

    function getIsValidated() {
        return $this->isValidated;
    }

    function setIsValidated($isValidated) {
        $this->isValidated = $isValidated;
    }
    
    function getSentimentAsureAllMessages() {
        return $this->sentimentAsureAllMessages;
    }

    function getTicketTypes() {
        return $this->ticketTypes;
    }
    
    function setTicketTypes($ticketTypes) {
        $this->ticketTypes = $ticketTypes;
    }
        
    function getEvaluating() {
        return $this->evaluating;
    }

    function setEvaluating($evaluating) {
        $this->evaluating = $evaluating;
    }
    
    function getWhatsappGroupName() {
        return $this->whatsappGroupName;
    }

    function setWhatsappGroupName($whatsappGroupName) {
        $this->whatsappGroupName = $whatsappGroupName;
    }
    function getOldId() {
        return $this->oldId;
    }

    function setOldId($oldId) {
        $this->oldId = $oldId;
    }
    
    function setSentimentAsureAllMessages($sentimentAsureAllMessages) {
        $this->sentimentAsureAllMessages = $sentimentAsureAllMessages;
    }
    function setDeletedDate($deletedDate) {
        $this->deletedDate = $deletedDate;
    }

    
    function getDeletedDate() {
        return $this->deletedDate;
    }

    function getDeletedByUsername() {
        return $this->deletedByUsername;
    }

    function setDeletedByUsername($deletedByUsername) {
        $this->deletedByUsername = $deletedByUsername;
    }

}
