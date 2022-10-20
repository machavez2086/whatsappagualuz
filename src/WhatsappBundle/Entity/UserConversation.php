<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\UserConversationRepository")
 * @ORM\Table(name="userconversation")
 */
class UserConversation
{ 
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="userConversations")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id") 
    */
    protected $user;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Conversation", inversedBy="userConversations")
    * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id") 
    */
    protected $conversation;
    
    
    function getId() {
        return $this->id;
    }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="userConversations")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }
    
    function getConversation() {
        return $this->conversation;
    }

    function setConversation($conversation) {
        $this->conversation = $conversation;
    }

    
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
