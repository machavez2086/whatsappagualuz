<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\PeticionRepository")
 * @ORM\Table(name="peticion")
 * @UniqueEntity(fields = {"noDelDia", "nroReclamo"})
 */
class Peticion {

    public function __construct() {
        $this->conversations = new ArrayCollection();
        $this->ticketSendedAreas = new ArrayCollection();
        $this->isFininshed = false;
        $this->ticketSendendAreaUsers = new ArrayCollection();
        $this->productRecepteds = new ArrayCollection();
        $this->productSendeds = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->createdAt = new \DateTime("now");
        $this->expirationDate = new \DateTime("now");
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
     * @ORM\Column(name="nroreclamo", type="string", nullable=true)
     */
    private $nroReclamo;

    /**
     * @var string $name
     *
     * @ORM\Column(name="timedisponibility", type="string", nullable=true)
     */
    private $timeDisponibility;

    /**
     * @var string $name
     *
     * @ORM\Column(name="clientname", type="string", nullable=true)
     */
    private $clientName;

    /**
     * @var string $name
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string $name
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var string $name
     *
     * @ORM\Column(name="domicilio", type="string", nullable=true)
     */
    private $domicilio;

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
     * @ORM\Column(name="codigopostal", type="string", nullable=true)
     */
    private $codigoPostal;

    /**
     * @var string $name
     *
     * @ORM\Column(name="expirationdate", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var string $name
     * @ORM\Column(name="expirationdatestr", type="string", nullable=true)
     */
    private $expirationDateStr;

    /**
     * @var string $name
     *
     * @ORM\Column(name="lote", type="string", nullable=true)
     */
    private $lote;

    /**
     * @var string $name
     *
     * @ORM\Column(name="loteno", type="string", nullable=true)
     */
    private $loteNo;

    /**
     * @var string $name
     *
     * @ORM\Column(name="loteHour", type="string", nullable=true)
     */
    private $loteHour;

    /**
     * @var string $name
     *
     * @ORM\Column(name="lotemaquina", type="string", nullable=true)
     */
    private $loteMaquina;

    /**
     * @var string $name
     *
     * @ORM\Column(name="cant", type="integer", nullable=true)
     */
    private $cant;

    /**
     * @var string $name
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     */
    private $observations;

    /**
     * @var string $name
     *
     * @ORM\Column(name="isfininshed", type="boolean", nullable=true)
     */
    private $isFininshed;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Category", inversedBy="peticions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id") 
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\ClientActitud", inversedBy="peticions")
     * @ORM\JoinColumn(name="clientActitud_id", referencedColumnName="id") 
     */
    private $clientActitud;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Presentation", inversedBy="peticions")
     * @ORM\JoinColumn(name="presentation_id", referencedColumnName="id") 
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Product", inversedBy="peticions")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id") 
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Motive", inversedBy="peticions")
     * @ORM\JoinColumn(name="motive_id", referencedColumnName="id") 
     */
    private $motive;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\ElementType", inversedBy="peticions")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id") 
     */
    private $elementType;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\PeticionType", inversedBy="peticions")
     * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
     */
    private $peticionType;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Area", inversedBy="peticions")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id") 
     */
    private $area;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\PeticionStatus", inversedBy="peticions")
     * @ORM\JoinColumn(name="peticionstatus_id", referencedColumnName="id") 
     */
    private $peticionStatus;

    /**
     * @var string $name
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string $noDelDia
     *
     * @ORM\Column(name="nodeldia", type="integer", nullable=true)
     */
    private $noDelDia;

    
    private $firsConversationType;

    function getPeticionType() {
        return $this->peticionType;
    }

    function setPeticionType($peticionType) {
        $this->peticionType = $peticionType;
    }

    function getCategory() {
        return $this->category;
    }

    function getClientActitud() {
        return $this->clientActitud;
    }

    function getPresentation() {
        return $this->presentation;
    }

    function getProduct() {
        return $this->product;
    }

    function getMotive() {
        return $this->motive;
    }

    function getElementType() {
        return $this->elementType;
    }

    function setCategory($category) {
        $this->category = $category;
    }

    function setClientActitud($clientActitud) {
        $this->clientActitud = $clientActitud;
    }

    function setPresentation($presentation) {
        $this->presentation = $presentation;
    }

    function setProduct($product) {
        $this->product = $product;
    }

    function setMotive($motive) {
        $this->motive = $motive;
    }

    function setElementType($elementType) {
        $this->elementType = $elementType;
    }

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getNroReclamo() {
        return $this->nroReclamo;
    }

    function setNroReclamo($nroReclamo) {
        $this->nroReclamo = $nroReclamo;
    }

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\SolutionType", inversedBy="peticions")
     * @ORM\JoinColumn(name="solutiontype_id", referencedColumnName="id") 
     */
    protected $solutionType;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="peticions")
     * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
     */
    protected $configuration;

    /**
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\WhatsappGroup", inversedBy="peticions") 
     * @ORM\JoinColumn(name="whatsappgroup_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $whatsappGroup;

    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    function getClientName() {
        return $this->clientName;
    }

    function getPhone() {
        return $this->phone;
    }

    function getEmail() {
        return $this->email;
    }

    function getDomicilio() {
        return $this->domicilio;
    }

    function getLocalidad() {
        return $this->localidad;
    }

    function getProvincia() {
        return $this->provincia;
    }

    function getCodigoPostal() {
        return $this->codigoPostal;
    }

    function getExpirationDate() {
        return $this->expirationDate;
    }

    function getLote() {
        return $this->lote;
    }

    function getCant() {
        return $this->cant;
    }

    function getObservations() {
        return $this->observations;
    }

    function setClientName($clientName) {
        $this->clientName = $clientName;
    }

    function setPhone($phone) {
        $this->phone = $phone;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setDomicilio($domicilio) {
        $this->domicilio = $domicilio;
    }

    function setLocalidad($localidad) {
        $this->localidad = $localidad;
    }

    function setProvincia($provincia) {
        $this->provincia = $provincia;
    }

    function setCodigoPostal($codigoPostal) {
        $this->codigoPostal = $codigoPostal;
    }

    function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
    }

    function setLote($lote) {
        $this->lote = $lote;
    }

    function setCant($cant) {
        $this->cant = $cant;
    }

    function setObservations($observations) {
        $this->observations = $observations;
    }

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
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

    public function addConversation(Conversation $conversation) {
        if($this->getId())
            $conversation->setPeticion($this);
        
        $this->conversations->add($conversation);
        
    }

    public function removeConversation(Conversation $conversation) {
        $conversation->setPeticion(null);
        $this->conversations->removeElement($conversation);
    }

    function getArea() {
        return $this->area;
    }

    function setArea($area) {
        $this->area = $area;
    }

    function getIsFininshed() {
        return $this->isFininshed;
    }

    function setIsFininshed($isFininshed) {
        $this->isFininshed = $isFininshed;
    }

    /**
     * @ORM\OneToMany(targetEntity="TicketSendedArea", mappedBy="peticion" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $ticketSendedAreas;

    function getTicketSendedAreas() {
        return $this->ticketSendedAreas;
    }

    function setTicketSendedAreas($ticketSendedAreas) {
        $this->ticketSendedAreas = new ArrayCollection();
        if (count($ticketSendedAreas) > 0) {
            foreach ($ticketSendedAreas as $i) {
                $this->addTicketSendedArea($i);
            }
        }
        return $this;
    }

    public function addTicketSendedArea(TicketSendedArea $ticketSendedArea) {
        $ticketSendedArea->setPeticion($this);
        $this->ticketSendedAreas->add($ticketSendedArea);
    }

    public function removeTicketSendedArea(TicketSendedArea $ticketSendedAreas) {
        $this->ticketSendedAreas->removeElement($ticketSendedAreas);
    }

    /**
     * @ORM\OneToMany(targetEntity="TicketSendendAreaUser", mappedBy="peticion" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $ticketSendendAreaUsers;

    function getTicketSendendAreaUsers() {
        return $this->ticketSendendAreaUsers;
    }

    function setTicketSendendAreaUsers($ticketSendendAreaUsers) {
        $this->ticketSendendAreaUsers = new ArrayCollection();
        if (count($ticketSendendAreaUsers) > 0) {
            foreach ($ticketSendendAreaUsers as $i) {
                $this->addTicketSendendAreaUser($i);
            }
        }
        return $this;
    }

    public function addTicketSendendAreaUser(TicketSendendAreaUser $ticketSendendAreaUser) {
        $ticketSendendAreaUser->setPeticion($this);
        $this->ticketSendendAreaUsers->add($ticketSendendAreaUser);
    }

    public function removeTicketSendendAreaUser(TicketSendendAreaUser $ticketSendendAreaUsers) {
        $this->ticketSendendAreaUsers->removeElement($ticketSendendAreaUsers);
    }

    function getSolutionType() {
        return $this->solutionType;
    }

    function setSolutionType($solutionType) {
        $this->solutionType = $solutionType;
    }

    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
    }

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ProductRecepted", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $productRecepteds;

    function getProductRecepteds() {
        return $this->productRecepteds;
    }

    function setProductRecepteds($productRecepteds) {
        $this->productRecepteds = new ArrayCollection();
        if (count($productRecepteds) > 0) {
            foreach ($productRecepteds as $i) {
                $this->addProductRecepted($i);
            }
        }
        return $this;
    }

    public function addProductRecepted(ProductRecepted $productRecepted) {
        $productRecepted->setProductReceptedType($this);
        $this->productRecepteds->add($productRecepted);
    }

    public function removeProductRecepted(ProductRecepted $productRecepted) {
        $this->productRecepteds->removeElement($productRecepted);
    }

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ProductSended", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $productSendeds;

    function getProductSendeds() {
        return $this->productSendeds;
    }

    function setProductSendeds($productSendeds) {
        $this->productSendeds = new ArrayCollection();
        if (count($productSendeds) > 0) {
            foreach ($productSendeds as $i) {
                $this->addProductSended($i);
            }
        }
        return $this;
    }

    public function addProductSended(ProductSended $productSended) {
        $productSended->setProductSendedType($this);
        $this->productSendeds->add($productSended);
    }

    public function removeProductSended(ProductSended $productSended) {
        $this->productSendeds->removeElement($productSended);
    }

    /**
     * @ORM\ManyToMany(targetEntity="ClaimType", mappedBy="peticions")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $claimTypes;

    function getClaimTypes() {
        return $this->claimTypes;
    }

    function setClaimTypes($claimTypes) {
        $this->claimTypes = new ArrayCollection();
        if (count($claimTypes) > 0) {
            foreach ($claimTypes as $i) {
                $this->addClaimType($i);
            }
        }
        return $this;
    }

    public function addClaimType(ClaimType $claimType) {
        $claimType->addPeticion($this);
        $this->claimTypes->add($claimType);
    }

    public function removeClaimType(ClaimType $claimTypes) {
        $this->claimTypes->removeElement($claimTypes);
    }

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ObleaRetiro", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $obleaRetiros;

    function getObleaRetiros() {
        return $this->obleaRetiros;
    }

    function setObleaRetiros($obleaRetiros) {
        $this->obleaRetiros = new ArrayCollection();
        if (count($obleaRetiros) > 0) {
            foreach ($obleaRetiros as $i) {
                $this->addObleaRetiro($i);
            }
        }
        return $this;
    }

    public function addObleaRetiro(ObleaRetiro $obleaRetiro) {
        $obleaRetiro->setObleaRetiroType($this);
        $this->obleaRetiros->add($obleaRetiro);
    }

    public function removeObleaRetiro(ObleaRetiro $obleaRetiro) {
        $this->obleaRetiros->removeElement($obleaRetiro);
    }

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="ObleaEnvio", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $obleaEnvios;

    function getObleaEnvios() {
        return $this->obleaEnvios;
    }

    function setObleaEnvios($obleaEnvios) {
        $this->obleaEnvios = new ArrayCollection();
        if (count($obleaEnvios) > 0) {
            foreach ($obleaEnvios as $i) {
                $this->addObleaEnvio($i);
            }
        }
        return $this;
    }

    public function addObleaEnvio(ObleaEnvio $obleaEnvio) {
        $obleaEnvio->setObleaEnvioType($this);
        $this->obleaEnvios->add($obleaEnvio);
    }

    public function removeObleaEnvio(ObleaEnvio $obleaEnvio) {
        $this->obleaEnvios->removeElement($obleaEnvio);
    }

    function getPeticionStatus() {
        return $this->peticionStatus;
    }

    function setPeticionStatus($peticionStatus) {
        $this->peticionStatus = $peticionStatus;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function getNoDelDia() {
        return $this->noDelDia;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    function setNoDelDia($noDelDia) {
        $this->noDelDia = $noDelDia;
    }

    function getExpirationDateStr() {
        return $this->expirationDateStr;
    }

    function getLoteNo() {
        return $this->loteNo;
    }

    function getLoteHour() {
        return $this->loteHour;
    }

    function getLoteMaquina() {
        return $this->loteMaquina;
    }

    function setExpirationDateStr($expirationDateStr) {
        $this->expirationDateStr = $expirationDateStr;
    }

    function setLoteNo($loteNo) {
        $this->loteNo = $loteNo;
    }

    function setLoteHour($loteHour) {
        $this->loteHour = $loteHour;
    }

    function setLoteMaquina($loteMaquina) {
        $this->loteMaquina = $loteMaquina;
    }

    function getTimeDisponibility() {
        return $this->timeDisponibility;
    }

    function setTimeDisponibility($timeDisponibility) {
        $this->timeDisponibility = $timeDisponibility;
    }

    function getFirsConversationType() {
        return $this->firsConversationType;
    }

    function setFirsConversationType($firsConversationType) {
        $this->firsConversationType = $firsConversationType;
    }
    
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="peticion", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=true)
     */
    private $files;
    
    function getFiles() {
        return $this->files;
    }
    
    function setFiles($files) {
        $this->files = new ArrayCollection();
        if (count($files) > 0) {
            foreach ($files as $i) {
                $this->addFile($i);
            }
        }
        return $this;
    }
    
    
    public function addFile($file)
    {
        $file->setPeticion($this);
        $this->files->add($file);
    }

    public function removeFile($file)
    {
        $this->files->removeElement($file);
    }
    
    
    private $sumaryObservations;
    
    function getSumaryObservations() {
        if(strlen($this->observations) > 100){
            return substr($this->observations,0,100).'...';
        }
        return $this->observations;
    }

    function setSumaryObservations($sumaryObservations) {
        $this->sumaryObservations = $sumaryObservations;
    }

    
    
    /**
     * @return string
     */
    public function __tostring() {
        if ($this->nroReclamo)
            return $this->nroReclamo;
        return "";
    }

}
