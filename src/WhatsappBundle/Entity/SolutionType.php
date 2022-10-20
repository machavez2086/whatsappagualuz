<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\SolutionTypeRepository")
 * @ORM\Table(name="solutiontype")
 */
class SolutionType
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
     * @ORM\OneToMany(targetEntity="Peticion", mappedBy="solutionType" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
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
        $peticion->setConfiguration($this);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="solutionTypes")
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
