<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\TicketRepository")
 * @ORM\Table(name="ticket")
 * @ORM\HasLifecycleCallbacks()
 */
class Ticket
{
    public function __construct() {
        $this->messages = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->ticketTypes = new ArrayCollection();
    }
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     * @Assert\NotBlank
     */
    private $name;
    
    /**
     * @var string $startDate
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=true)
     */
    private $startDate;
    
    
    /**
     * @var string $startTime
     *
     * @ORM\Column(name="starttime", type="time", nullable=true)
     */
    private $startTime;
    
    function getStartTime() {
        
        return $this->startTime;
//        $timezone = new \DateTimeZone($this->configuration->getTimezone());
//        $dateAux = $this->startTime;
//        $dateAux->setTimezone($timezone);
//        
//        return $dateAux->format("H:i:s");
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\TicketType", inversedBy="tickets") 
    * @ORM\JoinColumn(name="tickettype_id", referencedColumnName="id")
    * tipo de peticion
    */
    private $ticketType;
    
     /** 
    * @ORM\ManyToMany(targetEntity="WhatsappBundle\Entity\TicketType", mappedBy="ticket2s") 
    * tipo de peticion
    */
    private $ticketTypes;
    
    
    /**
     * @var string $solution
     * Como se resolvio
     * @ORM\Column(name="solution", type="text", nullable=true)
     */
    private $solution;

    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\WhatsappGroup", inversedBy="tickets") 
    * @ORM\JoinColumn(name="whatsappgroup_id", referencedColumnName="id", onDelete="SET NULL")
    */
    protected $whatsappGroup;

    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="ticket", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $messages;
    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="ticket", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $alerts;

    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="sendalert", type="boolean", nullable=true)
     */
    private $sendalert;

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
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentvaderallmessages", type="float", nullable=true)
     * 
     */
    private $sentimentVaderAllMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentvadersupportmessages", type="float", nullable=true)
     * 
     */
    private $sentimentVaderSupportMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentvaderclientmessages", type="float", nullable=true)
     * 
     */
    private $sentimentVaderClientMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentspahishallmessages", type="float", nullable=true)
     * 
     */
    private $sentimentSpahishAllMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentspahishsupportmessages", type="float", nullable=true)
     * 
     */
    private $sentimentSpahishSupportMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentspahishclientmessages", type="float", nullable=true)
     * 
     */
    private $sentimentSpahishClientMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimenttextbloballmessages", type="float", nullable=true)
     * 
     */
    private $sentimentTextblobAllMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimenttextblobsupportmessages", type="float", nullable=true)
     * 
     */
    private $sentimentTextblobSupportMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimenttextblobclientmessages", type="float", nullable=true)
     * 
     */
    private $sentimentTextblobClientMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentasureallmessages", type="float", nullable=true)
     * 
     */
    private $sentimentAsureAllMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentasuresupportmessages", type="float", nullable=true)
     * 
     */
    private $sentimentasureSupportMessages;
    
    /**
     * @var long $sentimentVaderAllMessages
     *
     * @ORM\Column(name="sentimentasureclientmessages", type="float", nullable=true)
     * 
     */
    private $sentimentAsureClientMessages;
    
    
    
    /**
     * @var long $evaluating
     *
     * @ORM\Column(name="evaluating", type="boolean", nullable=true)
     * 
     */
    private $evaluating;
    
    function getMessages() {
        return $this->messages;
    }
    
    function setMessages($messages) {
        $this->messages = new ArrayCollection();
        if (count($messages) > 0) {
            foreach ($messages as $i) {
                $this->addMessages($i);
            }
        }
        return $this;
    }
    
    public function addMessages(Message $message)
    {
        $message->setTicket($this);

        $this->messages->add($message);
    }

    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }
    
    function getId() {
        return $this->id;
    }

    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
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
    
    function getTickets() {
        return $this->tickets;
    }

    function getTicketType() {
        return $this->ticketType;
    }

    function getSolution() {
        return $this->solution;
    }


    function setTickets($tickets) {
        $this->tickets = $tickets;
    }

    function setTicketType($ticketType) {
        $this->ticketType = $ticketType;
    }

    function setSolution($solution) {
        $this->solution = $solution;
    }

    
    function recalculateStartDate(){
        
        if(count($this->messages)> 0){
            $minDate = $this->messages->first()->getDtmmessage();
            $maxDate = $this->messages->first()->getDtmmessage();
            foreach ($this->messages as $value) {
                if($value->getDtmmessage() < $minDate and $value->getEnabled())
                    $minDate = $value->getDtmmessage();
                if($value->getDtmmessage() > $maxDate and $value->getEnabled())
                    $maxDate = $value->getDtmmessage();
            }
            
            $this->setStartDate($minDate);
            $this->setStartTime($minDate);
            $this->setEndDate($maxDate);
//            $maxDate = $this->endDate;
            $since_start = $maxDate->diff($minDate);
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s/60;
            $this->setMinutesSolutionTime($minutes);
            $this->setWeekday($this->SpanishDate($this->getDayOfWeek($this->getStartDate())));
             
            $whatsappGroupName = "";
            if($this->whatsappGroup){
                $whatsappGroupName = $this->whatsappGroup->getName();
            }
            $startDate = "";
            if($this->startDate){
                $startDate = clone $this->startDate;
                $timezone = new \DateTimeZone($this->configuration->getTimezone());
                $startDate->setTimezone($timezone);
                $startDate = $startDate->format('Y-m-d H:i:s');
            }
            $this->name = $whatsappGroupName." > ".strval($startDate);
        }
        if($this->nofollow == null){
            $this->nofollow = false;
        }
       
    }
    
    function recalculateResolutionTates($timezone){
        
        if(count($this->messages)> 0){
            $minDate = $this->messages->first()->getDtmmessage();
            $maxDate = $this->messages->last()->getDtmmessage();
            foreach ($this->messages as $value) {
                if($value->getDtmmessage() < $minDate and $value->getEnabled())
                    $minDate = $value->getDtmmessage();
                if($value->getDtmmessage() > $maxDate and $value->getEnabled())
                    $maxDate = $value->getDtmmessage();
            }
            $this->setStartDate($minDate);
            $this->setEndDate($maxDate);
            $since_start = $maxDate->diff($minDate);
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s/60;
            $this->setMinutesSolutionTime($minutes);
            $this->setWeekday($this->SpanishDate($this->getDayOfWeek($this->getStartDate())));
        }
        else{
            $this->setStartDate(null);
            $this->setEndDate(null);
            $this->setMinutesSolutionTime(0);
            $this->setWeekday("");
        }
        $whatsappGroupName = "";
        if($this->whatsappGroup){
            $whatsappGroupName = $this->whatsappGroup->getName();
        }
         $startDate = "";
            if($this->startDate){
                $startDate = clone  $this->startDate;
                
                //Buscar un mecanismo para obtener el timezone de configuracion de otra forma mas rapida
//                $timezone = new \DateTimeZone($this->configuration->getTimezone());
                $timezone = new \DateTimeZone($timezone);
                $startDate->setTimezone($timezone);
                $startDate = $startDate->format('Y-m-d H:i:s');
            }
               
            
            $this->name = $whatsappGroupName." > ".strval($startDate);
        
    }
    
    function recalculateResolutionByEndDate($maxDate){
        
        if(count($this->messages)> 0){
            $minDate = $this->messages->first()->getDtmmessage();
            
            foreach ($this->messages as $value) {
                if($value->getDtmmessage() < $minDate and $value->getEnabled())
                    $minDate = $value->getDtmmessage();
            }
            $this->setStartDate($minDate);
            $this->setEndDate($maxDate);
            $since_start = $maxDate->diff($minDate);
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s/60;
            $diferencia = floatval($this->getMinutesAnswerTime()) - floatval($this->getMinutesSolutionTime());
            if($diferencia < 0)
                $diferencia = $diferencia*-1;
            if($diferencia < 1){
                $this->setMinutesAnswerTime($minutes);
            }
            $this->setMinutesSolutionTime($minutes);
            
        }
        else{
            $this->setStartDate(null);
            $this->setMinutesSolutionTime(0);
        }
    }
    
    function findAndRecalculeFirstAnswer(){
        foreach ($this->messages as $value) {
            if($value->getSupportFirstAnswer()){
                $this->recalculeFirstAnswer($value->getId());
                break;
            }
        }
    }
    
    function recalculeFirstAnswer($messageId){
         
        if(count($this->messages)> 0){
            
            $minDate = $this->messages->first()->getDtmmessage();
            $maxDate = $this->messages->last()->getDtmmessage();
            foreach ($this->messages as $value) {
                if($value->getDtmmessage() < $minDate and $value->getEnabled())
                    $minDate = $value->getDtmmessage();
                if($value->getId() == $messageId){
                    $maxDate = $value->getDtmmessage();
                    
                }
            }
            
//            $this->setStartDate($minDate);
//            $this->setEndDate($maxDate);
            $since_start = $maxDate->diff($minDate);
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s/60;
            
            $this->setMinutesAnswerTime($minutes);
            $this->setFirstanswer(true);
        }
//        else{
//            $this->setStartDate(null);
//            $this->setEndDate(null);
//            $this->setMinutesSolutionTime(strval(0));
//        }
    }
    
    function recalculeValidation(){
        if(count($this->messages)> 0){
            $minDate = $this->messages->first()->getDtmmessage();
            $maxDate = $minDate;
            $cantValidations = 0;
            foreach ($this->messages as $value) {
                if($value->getDtmmessage() < $minDate and $value->getEnabled())
                    $minDate = $value->getDtmmessage();
                if($value->getIsValidationKeyword() && $maxDate < $value->getDtmmessage()){
                    $maxDate = $value->getDtmmessage();
                }
                if($value->getIsValidationKeyword()){
                    $cantValidations = $cantValidations+1;
                }
            }
            if($cantValidations > 0){
                $since_start = $maxDate->diff($minDate);
                $minutes = $since_start->days * 24 * 60;
                $minutes += $since_start->h * 60;
                $minutes += $since_start->i;
                $minutes += $since_start->s/60;

                $this->setMinutesDevTime($minutes);
                $since_start = $this->endDate->diff($maxDate);
                $minutes = $since_start->days * 24 * 60;
                $minutes += $since_start->h * 60;
                $minutes += $since_start->i;
                $minutes += $since_start->s/60;
                $this->setMinutesValidationWaitTime($minutes);            
                $this->setValidationCount($cantValidations);
            }
            if($cantValidations > 0){
                $this->setFirstanswer(true);
                $this->setIsValidated(true);
            }
            else{
                $this->setIsValidated(false);
            }
        }
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
    
    function getSentimentVaderAllMessages() {
        return $this->sentimentVaderAllMessages;
    }

    function getSentimentVaderSupportMessages() {
        return $this->sentimentVaderSupportMessages;
    }

    function getSentimentVaderClientMessages() {
        return $this->sentimentVaderClientMessages;
    }

    function getSentimentSpahishAllMessages() {
        return $this->sentimentSpahishAllMessages;
    }

    function getSentimentSpahishSupportMessages() {
        return $this->sentimentSpahishSupportMessages;
    }

    function getSentimentSpahishClientMessages() {
        return $this->sentimentSpahishClientMessages;
    }

    function getSentimentTextblobAllMessages() {
        return $this->sentimentTextblobAllMessages;
    }

    function getSentimentTextblobSupportMessages() {
        return $this->sentimentTextblobSupportMessages;
    }

    function getSentimentTextblobClientMessages() {
        return $this->sentimentTextblobClientMessages;
    }

    function getSentimentAsureAllMessages() {
        return $this->sentimentAsureAllMessages;
    }

    function getSentimentasureSupportMessages() {
        return $this->sentimentasureSupportMessages;
    }

    function getSentimentAsureClientMessages() {
        return $this->sentimentAsureClientMessages;
    }

    function setSentimentVaderAllMessages($sentimentVaderAllMessages) {
        $this->sentimentVaderAllMessages = $sentimentVaderAllMessages;
    }

    function setSentimentVaderSupportMessages($sentimentVaderSupportMessages) {
        $this->sentimentVaderSupportMessages = $sentimentVaderSupportMessages;
    }

    function setSentimentVaderClientMessages($sentimentVaderClientMessages) {
        $this->sentimentVaderClientMessages = $sentimentVaderClientMessages;
    }

    function setSentimentSpahishAllMessages($sentimentSpahishAllMessages) {
        $this->sentimentSpahishAllMessages = $sentimentSpahishAllMessages;
    }

    function setSentimentSpahishSupportMessages($sentimentSpahishSupportMessages) {
        $this->sentimentSpahishSupportMessages = $sentimentSpahishSupportMessages;
    }

    function setSentimentSpahishClientMessages($sentimentSpahishClientMessages) {
        $this->sentimentSpahishClientMessages = $sentimentSpahishClientMessages;
    }

    function setSentimentTextblobAllMessages($sentimentTextblobAllMessages) {
        $this->sentimentTextblobAllMessages = $sentimentTextblobAllMessages;
    }

    function setSentimentTextblobSupportMessages($sentimentTextblobSupportMessages) {
        $this->sentimentTextblobSupportMessages = $sentimentTextblobSupportMessages;
    }

    function setSentimentTextblobClientMessages($sentimentTextblobClientMessages) {
        $this->sentimentTextblobClientMessages = $sentimentTextblobClientMessages;
    }

    function setSentimentAsureAllMessages($sentimentAsureAllMessages) {
        $this->sentimentAsureAllMessages = $sentimentAsureAllMessages;
    }

    function setSentimentasureSupportMessages($sentimentasureSupportMessages) {
        $this->sentimentasureSupportMessages = $sentimentasureSupportMessages;
    }

    function setSentimentAsureClientMessages($sentimentAsureClientMessages) {
        $this->sentimentAsureClientMessages = $sentimentAsureClientMessages;
    }

    function getTicketTypes() {
        return $this->ticketTypes;
    }
    
    function setTicketTypes($ticketTypes) {
        $this->ticketTypes = new ArrayCollection();
        if (count($ticketTypes) > 0) {
            foreach ($ticketTypes as $i) {
                $this->addTicketType($i);
            }
        }
        return $this;
    }
    
    public function addTicketType(TicketType $ticketType)
    {
//        dump("sdf");die;
        $ticketType->addTicket2($this);

        $this->ticketTypes->add($ticketType);
    }

    public function removeTicketType(TicketType $ticketType)
    {
        $this->ticketTypes->removeElement($ticketType);
        $ticketType->removeTicket2($this);
    }
    

    function getEvaluating() {
        return $this->evaluating;
    }

    function setEvaluating($evaluating) {
        $this->evaluating = $evaluating;
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate()
    {
    //    $this->recalculateResolutionTates();
    }
    

}
