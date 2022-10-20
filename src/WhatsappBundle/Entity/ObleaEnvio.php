<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ObleaEnvioRepository")
 * @ORM\Table(name="obleaenvio")
 */
class ObleaEnvio
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
     * @ORM\Column(name="paquetesretirar", type="integer", nullable=true)
     */
    private $paquetesRetirar;
    
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
     * @ORM\Column(name="localidad", type="string", nullable=true)
     */
    private $localidad;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="provincia", type="string", nullable=true)
     */
    private $provincia;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="cp", type="string", nullable=true)
     */
    private $cp;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="destinatario", type="text", nullable=true)
     */
    private $destinatario;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="product", type="text", nullable=true)
     */
    private $product;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="remitente", type="text", nullable=true)
     */
    private $remitente;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="remitentecontact", type="text", nullable=true)
     */
    private $remitenteContact;
    
    
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
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="obleaEnvioUpdateds")
    * @ORM\JoinColumn(name="userupdated_id", referencedColumnName="id") 
    */
    protected $updatedBy;     
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="obleaEnvioCreateds")
    * @ORM\JoinColumn(name="usercreated_id", referencedColumnName="id") 
    */
    protected $createdBy;
    
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Peticion", inversedBy="obleaEnvios")
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    private $peticion;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="obleaEnvios")
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

    function getPeticion() {
        return $this->peticion;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }
    function getNoReclamo() {
        return $this->noReclamo;
    }

    function getBulto() {
        return $this->bulto;
    }

    function getPaquetesRetirar() {
        return $this->paquetesRetirar;
    }

    function getPeso() {
        return $this->peso;
    }

    function getEntregaEnDomicilio() {
        return $this->entregaEnDomicilio;
    }

    function getLocalidad() {
        return $this->localidad;
    }

    function getProvincia() {
        return $this->provincia;
    }

    function getCp() {
        return $this->cp;
    }

    function getDestinatario() {
        return $this->destinatario;
    }

    function getObservaciones() {
        return $this->observaciones;
    }

    function getProduct() {
        return $this->product;
    }

    function getRemitente() {
        return $this->remitente;
    }

    function getRemitenteContact() {
        return $this->remitenteContact;
    }

    function setNoReclamo($noReclamo) {
        $this->noReclamo = $noReclamo;
    }

    function setBulto($bulto) {
        $this->bulto = $bulto;
    }

    function setPaquetesRetirar($paquetesRetirar) {
        $this->paquetesRetirar = $paquetesRetirar;
    }

    function setPeso($peso) {
        $this->peso = $peso;
    }

    function setEntregaEnDomicilio($entregaEnDomicilio) {
        $this->entregaEnDomicilio = $entregaEnDomicilio;
    }

    function setLocalidad($localidad) {
        $this->localidad = $localidad;
    }

    function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    function setCp($cp) {
        $this->cp = $cp;
    }

    function setDestinatario($destinatario) {
        $this->destinatario = $destinatario;
    }

    function setObservaciones($observaciones) {
        $this->observaciones = $observaciones;
    }

    function setProduct($product) {
        $this->product = $product;
    }

    function setRemitente($remitente) {
        $this->remitente = $remitente;
    }

    function setRemitenteContact($remitenteContact) {
        $this->remitenteContact = $remitenteContact;
    }

    function getImageurl() {
        return $this->imageurl;
    }

    function setImageurl($imageurl) {
        $this->imageurl = $imageurl;
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
    
    private $fechastr;
    
    function getFechastr() {
        return $this->peticion->getCreatedAt()->format("d-m-Y");
    }

    function setFechastr($fechastr) {
        $this->fechastr = $fechastr;
    }

    
    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->name;
    }

}
