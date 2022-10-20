<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\QuestionPollRepository")
 * @ORM\Table(name="questionpoll")
 */
class QuestionPoll
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
     * @ORM\Column(name="ask", type="string", nullable=true)
     */
    private $ask;

    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Poll", inversedBy="questionPolls")
    * @ORM\JoinColumn(name="poll_id", referencedColumnName="id") 
    */
    private $poll;
    
    function getId() {
        return $this->id;
    }

   

    function setId($id) {
        $this->id = $id;
    }

    function getAsk() {
        return $this->ask;
    }

    function setAsk($ask) {
        $this->ask = $ask;
    }
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="questionPolls")
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

        
    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->ask;
    }

}
