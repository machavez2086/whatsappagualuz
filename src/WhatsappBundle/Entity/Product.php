<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ProductRepository")
 * @ORM\Table(name="product")
 */
class Product {

    public function __construct() {
        $this->peticions = new ArrayCollection();
        $this->expirationDate = new \DateTime("now");
        $this->packingDate = new \DateTime("now");
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
     * @var string $lote
     *
     * @ORM\Column(name="lote", type="string", nullable=true)
     */
    private $lote;

    /**
     * @var string $expirationDate
     *
     * @ORM\Column(name="expirationdate", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var string $packingDate
     *
     * @ORM\Column(name="packingdate", type="datetime", nullable=true)
     */
    private $packingDate;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Peticion", mappedBy="product", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $peticions;

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

    public function addPeticion(Peticion $peticion) {
        $peticion->setProduct($this);
        $this->peticions->add($peticion);
    }

    public function removePeticion(Peticion $peticion) {
        $this->peticions->removeElement($peticion);
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
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="products")
     * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
     */
    protected $configuration;

    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    function getExpirationDate() {
        return $this->expirationDate;
    }

    function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
    }

    function getLote() {
        return $this->lote;
    }

    function getPackingDate() {
        return $this->packingDate;
    }

    function setLote($lote) {
        $this->lote = $lote;
    }

    function setPackingDate($packingDate) {
        $this->packingDate = $packingDate;
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
