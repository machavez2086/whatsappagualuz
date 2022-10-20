<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\AlertRepository")
 * @ORM\Table(name="alert")
 */
class Alert
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
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Ticket", inversedBy="alerts")
    * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="SET NULL") 
    */
    protected $ticket;
    
    /**
     * @var string $sendDate
     *
     * @ORM\Column(name="senddate", type="datetime", nullable=true)
     */
    private $sendDate;
    
    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;
    
    /**
     * @var string $open
     *
     * @ORM\Column(name="open", type="boolean", nullable=true)
     */
    private $open;

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }
    
    function getMessage() {
        return $this->message;
    }

   
    function getSendDate() {
        return $this->sendDate;
    }
    function getSendDateText() {
        $sendDate = clone $this->sendDate;
        if($this->configuration){
            $timezone = new \DateTimeZone($this->configuration->getTimezone());
            $sendDate->setTimezone($timezone);
        }
        return $sendDate;
    }

    function getType() {
        return $this->type;
    }

    function getTicket() {
        return $this->ticket;
    }

    function setMessage($message) {
        $this->message = $message;
    }

   

    function setSendDate($sendDate) {
        $this->sendDate = $sendDate;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setTicket($ticket) {
        $this->ticket = $ticket;
    }
    
    function getOpen() {
        return $this->open;
    }

    function setOpen($open) {
        $this->open = $open;
    }
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="alerts")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->message;
    }
    function getOpenText() {
        if($this->open)
            return "sí";
        return "no";
    }

}