<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\WhatsappGroupRepository")
 * @ORM\Table(name="whatsappgroup")
 * @UniqueEntity(fields = {"name"})
 */
class WhatsappGroup
{
    
     public function __construct() {
        $this->tickets = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messageSendeds = new ArrayCollection();
        $this->companyConfirmed = true;
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
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;    
    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="whatsappGroup", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $tickets;
    
    /**
     * @var string $chatId
     *
     * @ORM\Column(name="chatid", type="text", nullable=true)
     * @Assert\Regex("/^[0-9]+@c|g.us$/", message="Formato incorrecto. ejemplo de formato: 5493416699047@c.us")
     */
    private $chatId;
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="whatsappGroup", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $messages;
    
    
    /**
     * @var string $companyConfirmed
     *
     * @ORM\Column(name="companyconfirmed", type="boolean", nullable=true)
     * 
     */
    private $companyConfirmed;
    
    /**
     * @var string $whatsappNick
     *
     * @ORM\Column(name="whatsappnick", type="string", nullable=true)
     */
    private $whatsappNick;

    /**
     * @var string phoneNumber
     *
     * @ORM\Column(name="phonenumber", type="string", nullable=true)
     */
    private $phoneNumber;

    /**
     * @var string phoneNumber
     *
     * @ORM\Column(name="phonefixed", type="string", nullable=true)
     */
    private $phoneFixed;
    
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="domicilio", type="text", nullable=true)
     */
    private $domicilio;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="localidad", type="text", nullable=true)
     */
    private $localidad;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="provincia", type="text", nullable=true)
     */
    private $provincia;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="codigopostal", type="string", nullable=true)
     */
    private $codigoPostal;
   
    
    /**
     * @var string $isAnswering
     *
     * @ORM\Column(name="isanswering", type="boolean", nullable=true)
     * 
     */
    private $isAnswering;
    
    /**
     * @var string $answeringOperation
     *
     * @ORM\Column(name="answeringoperation", type="string", nullable=true)
     * 
     */
    private $answeringOperation;
    
    /**
     * @var datetime $dtmmessage Se utiliza para guardar la ultima vez que se envio una respuesta automatica para evitar enviar varias el mismo dia. En el bot se compara el dia actual con esta fecha.
     *
     * @ORM\Column(name="datelastautomaticmessage", type="datetime", nullable=true)
     */
    private $dateLastAutomaticMessage;
    
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
        $message->setWhatsappGroup($this);

        $this->messages->add($message);
    }

    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }
    
    
    function getTickets() {
        return $this->tickets;
    }
    
    function setTickets($tickets) {
        $this->tickets = new ArrayCollection();
        if (count($tickets) > 0) {
            foreach ($tickets as $i) {
                $this->addTickets($i);
            }
        }
        return $this;
    }
    
    public function addTickets(Ticket $ticket)
    {
        $ticket->setWhatsappGroup($this);

        $this->tickets->add($ticket);
    }

    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="whatsappGroups")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id" ) 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getChatId() {
        return $this->chatId;
    }

    function setChatId($chatId) {
        $this->chatId = $chatId;
    }
    
    function getCompanyConfirmed() {
        return $this->companyConfirmed;
    }

    function setCompanyConfirmed($companyConfirmed) {
        $this->companyConfirmed = $companyConfirmed;
    }    
    
    function getWhatsappNick() {
        return $this->whatsappNick;
    }

    function getPhoneNumber() {
        return $this->phoneNumber;
    }

    function setWhatsappNick($whatsappNick) {
        $this->whatsappNick = $whatsappNick;
    }

    function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }
    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Peticion", mappedBy="whatsappGroup", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $peticions;

    
    function getPeticions() {
        return $this->peticions;
    }
    
    function setPeticions($peticions) {
        $this->peticions = new ArrayCollection();
        if (count($peticions) > 0) {
            foreach ($peticions as $i) {
                $this->addPeticions($i);
            }
        }
        return $this;
    }
    
    public function addPeticions(Peticion $peticion)
    {
        $peticion->setWhatsappGroup($this);

        $this->peticions->add($peticion);
    }

    public function removePeticion(Peticion $peticion)
    {
        $this->peticions->removeElement($peticion);
    }
    
    function getEmail() {
        return $this->email;
    }

    function getDomicilio() {
        return $this->domicilio;
    }

    function getLocalidad() {
        return $this->localidad;
    }

    function getProvincia() {
        return $this->provincia;
    }

    function getCodigoPostal() {
        return $this->codigoPostal;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setDomicilio($domicilio) {
        $this->domicilio = $domicilio;
    }

    function setLocalidad($localidad) {
        $this->localidad = $localidad;
    }

    function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    function setCodigoPostal($codigoPostal) {
        $this->codigoPostal = $codigoPostal;
    }

    function getIsAnswering() {
        return $this->isAnswering;
    }

    function getAnsweringOperation() {
        return $this->answeringOperation;
    }

    function setIsAnswering($isAnswering) {
        $this->isAnswering = $isAnswering;
    }

    function setAnsweringOperation($answeringOperation) {
        $this->answeringOperation = $answeringOperation;
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="whatsappGroup" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $conversations;
    
    function getConversations() {
        return $this->conversations;
    }
    
    function setConversations($conversations) {
        $this->conversations = new ArrayCollection();
        if (count($conversations) > 0) {
            foreach ($conversations as $i) {
                $this->addConversation($i);
            }
        }
        return $this;
    }
    
    public function addConversation(Conversation $conversation)
    {
        $conversation->setWhatsappGroup($this);

        $this->conversations->add($conversation);
    }

    public function removeConversation(Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
    }
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="MessageSended", mappedBy="whatsappGroup", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $messageSendeds;


    function getMessageSendeds() {
        return $this->messageSendeds;
    }
    
    function setMessageSendeds($messageSendeds) {
        $this->messageSendeds = new ArrayCollection();
        if (count($messageSendeds) > 0) {
            foreach ($messageSendeds as $i) {
                $this->addMessageSendeds($i);
            }
        }
        return $this;
    }
    
    public function addMessageSendeds(MessageSended $messageSended)
    {
        $messageSended->setWhatsappGroup($this);

        $this->messageSendeds->add($messageSended);
    }

    public function removeMessageSended(MessageSended $messageSended)
    {
        $this->messageSendeds->removeElement($messageSended);
    }
    
    function getDateLastAutomaticMessage() {
        return $this->dateLastAutomaticMessage;
    }

    function setDateLastAutomaticMessage($dateLastAutomaticMessage) {
        $this->dateLastAutomaticMessage = $dateLastAutomaticMessage;
    }

    function getPhoneFixed() {
        return $this->phoneFixed;
    }

    function setPhoneFixed($phoneFixed) {
        $this->phoneFixed = $phoneFixed;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        //TODO poner que si no tiene el nombre retorne comillas
        $name = "";
        if($this->name != "")
            $name = $name.$this->name;
        else
            $name = "Cliente sin nombre";
        if($this->phoneNumber != "")
            $name = $name." (".$this->phoneNumber.")";
        return $name;
    }

}
