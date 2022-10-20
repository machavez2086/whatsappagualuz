<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\TicketTypeRepository")
 * @ORM\Table(name="tickettype")
 */
class TicketType
{
     public function __construct() {
        $this->tickets = new ArrayCollection();
        $this->ticket2s = new ArrayCollection();
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
     */
    private $name;
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="ticketType", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $tickets;
    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\ManyToMany(targetEntity="Ticket", inversedBy="ticketTypes")
     */
    private $ticket2s;
    
    function getTicket2s() {
        return $this->ticket2s;
    }
    
    function setTicket2s($ticket2s) {
        $this->ticket2s = new ArrayCollection();
        if (count($ticket2s) > 0) {
            foreach ($ticket2s as $i) {
                $this->addTicket2($i);
            }
        }
        return $this;
    }
    
    public function addTicket2(Ticket $ticket)
    {
//        $ticket->addTicketType($this);

        $this->ticket2s->add($ticket);
    }

    public function removeTicket2(Ticket $ticket)
    {
        $this->ticket2s->removeElement($ticket);
    }
    
    function getTickets() {
        return $this->tickets;
    }

    function setTickets($tickets) {
        $this->tickets = $tickets;
    }
        
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="ticketTypes")
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
        return $this->name;
    }

}
