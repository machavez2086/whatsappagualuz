<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\SendedStatusRepository")
 * @ORM\Table(name="sendedstatus")
 */
class SendedStatus
{ 
    public function __construct() {
        $this->productSendeds = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="ProductSended", mappedBy="sendedStatus", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $productSendeds;
    
    function getPeticions() {
        return $this->productSendeds;
    }
    
    function setPeticions($productSendeds) {
        $this->productSendeds = new ArrayCollection();
        if (count($productSendeds) > 0) {
            foreach ($productSendeds as $i) {
                $this->addPeticion($i);
            }
        }
        return $this;
    }
    
    public function addPeticion(Peticion $productSended)
    {
        $productSended->setSendedStatus($this);
        $this->productSendeds->add($productSended);
    }

    public function removePeticion(Peticion $productSended)
    {
        $this->productSendeds->removeElement($productSended);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="sendedStatuss")
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
