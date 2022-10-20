<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ClaimTypeRepository")
 * @ORM\Table(name="claimtype")
 */
class ClaimType
{
    public function __construct() {
        $this->peticions = new ArrayCollection();
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
     * @ORM\ManyToMany(targetEntity="Peticion", inversedBy="claimTypes")
     * @ORM\JoinTable(name="peticion_claim_type",
     *     joinColumns={
     *     @ORM\JoinColumn(name="claimtype_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="peticion_id", referencedColumnName="id")
     *   }
     * ) 
     */
    protected $peticions;
    
    function getPeticions() {
        return $this->peticions;
    }
    
    function setPeticions($peticions) {
        $this->peticions = new ArrayCollection();
        if (count($peticions) > 0) {
            foreach ($peticions as $i) {
                $this->addPeticion($i);
            }
        }
        return $this;
    }
    
    public function addPeticion(Peticion $peticion)
    {
        $peticion->setClaimTypes($this);
        $this->peticions->add($peticion);
    }

    public function removePeticion(Peticion $peticions)
    {
        $this->peticions->removeElement($peticions);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="claimTypes")
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
