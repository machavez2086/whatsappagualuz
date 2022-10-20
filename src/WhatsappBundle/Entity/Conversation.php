<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ConversationRepository")
 * @ORM\Table(name="conversation")
 */
class Conversation
{ 
    public function __construct() {
        $this->peticions = new ArrayCollection();
        $this->messageSendeds = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->day = new \DateTime("now");
    }
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    
    /**
     * @var string $expirationDate
     *
     * @ORM\Column(name="day", type="datetime", nullable=true)
     */
    private $day;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;
    
    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\ConversationType", inversedBy="conversations")
    * @ORM\JoinColumn(name="conversationtype_id", referencedColumnName="id") 
    */
    private $conversationType;
    
    /**
     * @var string $expirationDate
     *
     * @ORM\Column(name="dateend", type="datetime", nullable=true)
     */
    private $dateEnd;
    
    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="ended", type="boolean", nullable=true)
     */
    private $ended;
    
    /**
     * @var string $satisfaction
     *
     * @ORM\Column(name="unreadmessage", type="boolean", nullable=true)
     */
    private $unreadMessage;
    
    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="conversations")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="conversations", cascade={"persist"})
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    protected $peticion;
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\WhatsappGroup", inversedBy="conversations")
    * @ORM\JoinColumn(name="whatsappgroup_id", referencedColumnName="id") 
    */
    protected $whatsappGroup;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getDay() {
        return $this->day;
    }

    function getPhone() {
        return $this->phone;
    }

    function getConversationType() {
        return $this->conversationType;
    }

    function setDay($day) {
        $this->day = $day;
    }

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setConversationType($conversationType) {
        $this->conversationType = $conversationType;
    }
    
    function getPeticion() {
        return $this->peticion;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }
     /**
     * @ORM\OneToMany(targetEntity="UserConversation", mappedBy="conversation" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $userConversations;
    
    function getUserConversations() {
        return $this->userConversations;
    }
    
    function setUserConversations($userConversations) {
        $this->userConversations = new ArrayCollection();
        if (count($userConversations) > 0) {
            foreach ($userConversations as $i) {
                $this->addUserConversation($i);
            }
        }
        return $this;
    }
    
    public function addUserConversation(UserConversation $userConversation)
    {
        $userConversation->setConversation($this);
        $this->userConversations->add($userConversation);
    }

    public function removeUserConversation(UserConversation $userConversations)
    {
        $this->userConversations->removeElement($userConversations);
    }
    
   /**
     * @ORM\OneToMany(targetEntity="MessageSended", mappedBy="conversation" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $messageSendeds;
    
    function getMessageSendeds() {
        return $this->messageSendeds;
    }
    
    function setMessageSendeds($messageSendeds) {
        $this->messageSendeds = new ArrayCollection();
        if (count($messageSendeds) > 0) {
            foreach ($messageSendeds as $i) {
                $this->addSolutionType($i);
            }
        }
        return $this;
    }
    
    public function addMessageSended(MessageSended $messageSended)
    {
        $messageSended->setConversation($this);
        $this->messageSendeds->add($messageSended);
    }

    public function removeMessageSended(MessageSended $messageSendeds)
    {
        $this->messageSendeds->removeElement($messageSendeds);
    }
  
    function getDescription() {
        return $this->description;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="conversation", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=true)
     */
    private $files;
    
    function getFiles() {
        return $this->files;
    }
    
    function setFiles($files) {
        $this->files = new ArrayCollection();
        if (count($files) > 0) {
            foreach ($files as $i) {
                $this->addFile($i);
            }
        }
        return $this;
    }
    
    
    public function addFile($file)
    {
        $file->setConversation($this);
        $this->files->add($file);
    }

    public function removeFile($file)
    {
        $message = $file->getMessage();
        if($message){
            $message->setConversation(null);
        }
        $this->files->removeElement($file);
    }
    
    function getDateEnd() {
        return $this->dateEnd;
    }

    function getEnded() {
        return $this->ended;
    }

    function setDateEnd($dateEnd) {
        $this->dateEnd = $dateEnd;
    }

    function setEnded($ended) {
        $this->ended = $ended;
    }
    
   /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $messages;
    
    function getMessages() {
        return $this->messages;
    }
    
    function setMessages($messages) {
        $this->messages = new ArrayCollection();
        if (count($messages) > 0) {
            foreach ($messages as $i) {
                $this->addSolutionType($i);
            }
        }
        return $this;
    }
    
    public function addMessage(Message $message)
    {
        $message->setConversation($this);
        $this->messages->add($message);
    }

    public function removeMessage(Message $messages)
    {
        $this->messages->removeElement($messages);
    }
    
    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
    }
    
    function getUnreadMessage() {
        return $this->unreadMessage;
    }

    function setUnreadMessage($unreadMessage) {
        $this->unreadMessage = $unreadMessage;
    }
    
    protected $dateLastMessage;
    
    function getDateLastMessage() {
        $lastDate = null;
        foreach ($this->messages as $message) {
            if($lastDate == null)
                $lastDate = $message->getDtmmessage();
            else if($lastDate < $message->getDtmmessage()){
                $lastDate = $message->getDtmmessage();
            }
        }
        if($lastDate != null){
            return $lastDate->format("d-m-Y H:i:s");
        }
        return "";
    }

    function setDateLastMessage($dateLastMessage) {
        $this->dateLastMessage = $dateLastMessage;
    }

    
    /**
     * @return string
     */
    public function __tostring()
    {
        $name = $this->phone;
        if($this->day != null)
            return $name." (".$this->day->format("d/m/y").")";
        return $this->phone;
    }

}
