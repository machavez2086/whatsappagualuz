<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\AreaRepository")
 * @ORM\Table(name="area")
 */
class Area {

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
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Peticion", mappedBy="area", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
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
        $peticion->setArea($this);
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
     * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="areas")
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
     * @ORM\OneToMany(targetEntity="TicketSendedArea", mappedBy="area" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $ticketSendedAreas;

    function getTicketSendedAreas() {
        return $this->ticketSendedAreas;
    }

    function setTicketSendedAreas($ticketSendedAreas) {
        $this->ticketSendedAreas = new ArrayCollection();
        if (count($ticketSendedAreas) > 0) {
            foreach ($ticketSendedAreas as $i) {
                $this->addSolutionType($i);
            }
        }
        return $this;
    }

    public function addTicketSendedArea(TicketSendedArea $ticketSendedArea) {
        $ticketSendedArea->setArea($this);
        $this->ticketSendedAreas->add($ticketSendedArea);
    }

    public function removeTicketSendedArea(TicketSendedArea $ticketSendedAreas) {
        $this->ticketSendedAreas->removeElement($ticketSendedAreas);
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
