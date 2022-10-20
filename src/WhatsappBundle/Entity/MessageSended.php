<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\MessageSendedRepository")
 * @ORM\Table(name="messagesended")
 */
class MessageSended
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Conversation", inversedBy="messageSendeds")
    * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id") 
    */
    private $conversation;
    
    
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="messageSendeds")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id") 
    */
    protected $user;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;
   
    /**
     * @ORM\OneToOne(targetEntity="Message", inversedBy="messageSended")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $whatsappMessage;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\WhatsappGroup", inversedBy="messageSendeds")
    * @ORM\JoinColumn(name="whatsappgroup_id", referencedColumnName="id") 
    */
    protected $whatsappGroup;
    
    function getId() {
        return $this->id;
    }

   

    function setId($id) {
        $this->id = $id;
    }

    function getMessage() {
        return $this->message;
    }

    function setMessage($message) {
        $this->message = $message;
    }
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="messageSendeds")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getPoll() {
        return $this->poll;
    }

    function setPoll($poll) {
        $this->poll = $poll;
    }
    function getConversation() {
        return $this->conversation;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function setConversation($conversation) {
        $this->conversation = $conversation;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    
    function getUser() {
        return $this->user;
    }

    function getWhatsappMessage() {
        return $this->whatsappMessage;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setWhatsappMessage($whatsappMessage) {
        $this->whatsappMessage = $whatsappMessage;
    }
 
    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->message;
    }

}
