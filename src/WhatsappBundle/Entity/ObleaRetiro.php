<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ObleaRetiroRepository")
 * @ORM\Table(name="oblearetiro")
 */
class ObleaRetiro
{
     public function __construct() {
         $this->bulto = 1;
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
     * @var string $name
     *
     * @ORM\Column(name="imageurl", type="text", nullable=true)
     */
    private $imageurl;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="noreclamo", type="string", nullable=true)
     */
    private $noReclamo;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="bulto", type="integer", nullable=true)
     */
    private $bulto;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="peso", type="string", nullable=true)
     */
    private $peso;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="entregaendomicilio", type="text", nullable=true)
     */
    private $entregaEnDomicilio;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="destinatarionames", type="text", nullable=true)
     */
    private $destinatarioNames;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="destinatarioemails", type="text", nullable=true)
     */
    private $destinatarioEmails;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="destinatariophones", type="text", nullable=true)
     */
    private $destinatarioPhones;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="createdat", type="datetime", nullable=true)
     */
    private $createdAt;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="updatedat", type="datetime", nullable=true)
     */
    private $updatedAt;
    
     /**
     * @var string $createdAt
     *
     * @ORM\Column(name="cantPaquetesRetirar", type="integer", nullable=true)
     */
    private $cantPaquetesRetirar;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="obleaRetiroUpdateds")
    * @ORM\JoinColumn(name="userupdated_id", referencedColumnName="id") 
    */
    protected $updatedBy;     
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="obleaRetiroCreateds")
    * @ORM\JoinColumn(name="usercreated_id", referencedColumnName="id") 
    */
    protected $createdBy;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="obleaRetiros")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    private $peticion;
   
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="obleaRetiros")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getId() {
        return $this->id;
    }
    function getName() {
        return $this->name;
    }

    function getImageurl() {
        return $this->imageurl;
    }

    function getNoReclamo() {
        return $this->noReclamo;
    }

    function getBulto() {
        return $this->bulto;
    }

    function getPeso() {
        return $this->peso;
    }

    function getEntregaEnDomicilio() {
        return $this->entregaEnDomicilio;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setImageurl($imageurl) {
        $this->imageurl = $imageurl;
    }

    function setNoReclamo($noReclamo) {
        $this->noReclamo = $noReclamo;
    }

    function setBulto($bulto) {
        $this->bulto = $bulto;
    }

    function setPeso($peso) {
        $this->peso = $peso;
    }

    function setEntregaEnDomicilio($entregaEnDomicilio) {
        $this->entregaEnDomicilio = $entregaEnDomicilio;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }
    function getCreatedAt() {
        return $this->createdAt;
    }

    function getUpdatedAt() {
        return $this->updatedAt;
    }

    function getUpdatedBy() {
        return $this->updatedBy;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
    }

    function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
    }

    function getDestinatarioNames() {
        return $this->destinatarioNames;
    }

    function getDestinatarioEmails() {
        return $this->destinatarioEmails;
    }

    function getDestinatarioPhones() {
        return $this->destinatarioPhones;
    }

    function setDestinatarioNames($destinatarioNames) {
        $this->destinatarioNames = $destinatarioNames;
    }

    function setDestinatarioEmails($destinatarioEmails) {
        $this->destinatarioEmails = $destinatarioEmails;
    }

    function setDestinatarioPhones($destinatarioPhones) {
        $this->destinatarioPhones = $destinatarioPhones;
    }
    
    private $fechastr;
    
    function getFechastr() {
        return $this->peticion->getCreatedAt()->format("d-m-Y");
    }

    function setFechastr($fechastr) {
        $this->fechastr = $fechastr;
    }
    
    function getCantPaquetesRetirar() {
        return $this->cantPaquetesRetirar;
    }

    function setCantPaquetesRetirar($cantPaquetesRetirar) {
        $this->cantPaquetesRetirar = $cantPaquetesRetirar;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->name;
    }

}
