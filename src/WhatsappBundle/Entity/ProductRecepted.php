<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ProductReceptedRepository")
 * @ORM\Table(name="productrecepted")
 */
class ProductRecepted
{ 
    public function __construct() {
//        $this->peticions = new ArrayCollection();
        $this->recivedAt = new \DateTime("now");
    }
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var datetime $recivedAt
     *
     * @ORM\Column(name="recivedat", type="datetime", nullable=true)
     */
    private $recivedAt;
    
    /**
     * @var datetime $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
        
     /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="productRecepteds")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    private $peticion;
    
    
    function getId() {
        return $this->id;
    }    
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\ReceptedStatus", inversedBy="productRecepteds")
    * @ORM\JoinColumn(name="receptedstatus_id", referencedColumnName="id") 
    */
    protected $receptedStatus;


    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="productRecepteds")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="productReceptedUpdateds")
    * @ORM\JoinColumn(name="userupdated_id", referencedColumnName="id") 
    */
    protected $updatedBy;     
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="productReceptedCreateds")
    * @ORM\JoinColumn(name="usercreated_id", referencedColumnName="id") 
    */
    protected $createdBy;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getRecivedAt() {
        return $this->recivedAt;
    }

    function getDescription() {
        return $this->description;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function getReceptedStatus() {
        return $this->receptedStatus;
    }

    function setRecivedAt($recivedAt) {
        $this->recivedAt = $recivedAt;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }

    function setReceptedStatus($receptedStatus) {
        $this->receptedStatus = $receptedStatus;
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

            
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
