<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\TicketSendendAreaUserRepository")
 * @ORM\Table(name="ticketsendendareauser")
 */
class TicketSendendAreaUser
{ 
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    
    /**
     * @var string $ask
     *
     * @ORM\Column(name="ask", type="text", nullable=true)
     */
    private $ask;
    
    
    
    /**
     * @var string $answer
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\AreaUser", inversedBy="ticketSendendAreaUsers")
    * @ORM\JoinColumn(name="areauser_id", referencedColumnName="id") 
    */
    protected $areaUser;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="ticketSendendAreaUsers")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    protected $peticion;
    
    
    function getId() {
        return $this->id;
    }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="ticketSendendAreaUsers")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    function getAsk() {
        return $this->ask;
    }

    function getAnswer() {
        return $this->answer;
    }

    function getAreaUser() {
        return $this->areaUser;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function getConfiguration() {
        return $this->configuration;
    }

    function setAsk($ask) {
        $this->ask = $ask;
    }

    function setAnswer($answer) {
        $this->answer = $answer;
    }

    function setAreaUser($areaUser) {
        $this->areaUser = $areaUser;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

        
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
