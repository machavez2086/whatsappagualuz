<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ProductSendedRepository")
 * @ORM\Table(name="productsended")
 */
class ProductSended
{ 
    public function __construct() {
//        $this->peticions = new ArrayCollection();
        $this->sendedAt = new \DateTime("now");
    }
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var datetime $sendedAt
     *
     * @ORM\Column(name="sendedat", type="datetime", nullable=true)
     */
    private $sendedAt;
    
    /**
     * @var datetime $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
        
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="productSendeds")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    protected $peticion;
    
    
    function getId() {
        return $this->id;
    }    
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\SendedStatus", inversedBy="productSendeds")
    * @ORM\JoinColumn(name="receptedstatus_id", referencedColumnName="id") 
    */
    protected $sendedStatus;


    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="productSendeds")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="productSendedUpdateds")
    * @ORM\JoinColumn(name="userupdated_id", referencedColumnName="id") 
    */
    protected $updatedBy;     
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="productSendedCreateds")
    * @ORM\JoinColumn(name="usercreated_id", referencedColumnName="id") 
    */
    protected $createdBy;
    
     
    /**
     * @var datetime $$noOblea
     *
     * @ORM\Column(name="nooblea", type="string", nullable=true)
     */
    private $noOblea;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getRecivedAt() {
        return $this->sendedAt;
    }

    function getDescription() {
        return $this->description;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function getProductSended() {
        return $this->sendedStatus;
    }

    function setRecivedAt($sendedAt) {
        $this->sendedAt = $sendedAt;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }

    function setProductSended($sendedStatus) {
        $this->sendedStatus = $sendedStatus;
    }
        
    function getSendedAt() {
        return $this->sendedAt;
    }

    function getSendedStatus() {
        return $this->sendedStatus;
    }

    function getUpdatedBy() {
        return $this->updatedBy;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function setSendedAt($sendedAt) {
        $this->sendedAt = $sendedAt;
    }

    function setSendedStatus($sendedStatus) {
        $this->sendedStatus = $sendedStatus;
    }

    function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
    }

    function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }
    
    function getNoOblea() {
        return $this->noOblea;
    }

    function setNoOblea($noOblea) {
        $this->noOblea = $noOblea;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
