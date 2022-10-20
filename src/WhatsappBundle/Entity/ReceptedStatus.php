<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ReceptedStatusRepository")
 * @ORM\Table(name="receptedstatus")
 */
class ReceptedStatus
{ 
    public function __construct() {
        $this->productRecepteds = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="ProductRecepted", mappedBy="receptedStatus", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
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
    
    public function addProductRecepted(ProductRecepted $productRecepted)
    {
        $productRecepted->setProductReceptedType($this);
        $this->productRecepteds->add($productRecepted);
    }

    public function removeProductRecepted(ProductRecepted $productRecepted)
    {
        $this->productRecepteds->removeElement($productRecepted);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="receptedStatuss")
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
