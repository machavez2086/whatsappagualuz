<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ConversationTypeRepository")
 * @ORM\Table(name="conversationtype")
 */
class ConversationType
{ 
    public function __construct() {
        $this->conversations = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="conversationType", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $conversations;
    
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
        $conversation->setConversationType($this);
        $this->conversations->add($conversation);
    }

    public function removeConversation(Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="conversationTypes")
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
    public function __tostring() {
        if ($this->name)
            return $this->name;
        return "";
    }

}
