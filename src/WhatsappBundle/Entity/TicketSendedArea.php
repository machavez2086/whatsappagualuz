<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\TicketSendedAreaRepository")
 * @ORM\Table(name="ticketsendedarea")
 */
class TicketSendedArea
{ 
 public function __construct() {
        $this->createdAt = new \DateTime("now");
    }
    
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="ticketSendedAreas")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    protected $peticion;    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Area", inversedBy="ticketSendedAreas")
    * @ORM\JoinColumn(name="area_id", referencedColumnName="id") 
    */
    protected $area;
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="ticketSendedAreaUpdateds")
    * @ORM\JoinColumn(name="userupdated_id", referencedColumnName="id") 
    */
    protected $updatedBy;     
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="ticketSendedAreaCreateds")
    * @ORM\JoinColumn(name="usercreated_id", referencedColumnName="id") 
    */
    protected $createdBy;
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="ticketSendedAreas")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id") 
    */
    protected $user;
    
    /**
     * @var string $answer
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="createdat", type="datetime", nullable=true)
     */
    private $createdAt;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="answeredat", type="datetime", nullable=true)
     */
    private $answeredAt;

    
    function getId() {
        return $this->id;
    }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="ticketSendedAreas")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getAsk() {
        return $this->ask;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function getArea() {
        return $this->area;
    }

    function setAsk($ask) {
        $this->ask = $ask;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }

    function setArea($area) {
        $this->area = $area;
    }
    
    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }
    
    function getAnswer() {
        return $this->answer;
    }

    function setAnswer($answer) {
        $this->answer = $answer;
    }
    
    function getUpdatedBy() {
        return $this->updatedBy;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
    }

    function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }
    function getCreatedAt() {
        return $this->createdAt;
    }

    function getAnsweredAt() {
        return $this->answeredAt;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function setAnsweredAt($answeredAt) {
        $this->answeredAt = $answeredAt;
    }

            
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
