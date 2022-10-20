<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\GeneralConfigurationRepository")
 * @ORM\Table(name="generalconfiguration")
 */
class GeneralConfiguration
{
    public function __construct() {
        $this->id = 1;
    }
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="monday", type="boolean", nullable=true)
     */
    private $monday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="tuesday", type="boolean", nullable=true)
     */
    private $tuesday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="wednesday", type="boolean", nullable=true)
     */
    private $wednesday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="thursday", type="boolean", nullable=true)
     */
    private $thursday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="friday", type="boolean", nullable=true)
     */
    private $friday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="saturday", type="boolean", nullable=true)
     */
    private $saturday;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="sunday", type="boolean", nullable=true)
     */
    private $sunday;
    
    /**
     * @var string $name Si esta activo se envia el mensaje de ausencia que se le respondera en unos minutos
     *
     * @ORM\Column(name="isnothere", type="boolean", nullable=true)
     */
    private $isNotHere;
    
    /**
     * @var string $name Si esta activo se envia el mensaje de ausencia que se le respondera en unos minutos
     *
     * @ORM\Column(name="thereisnothereanswer", type="text", nullable=true)
     */
    private $thereIsNotHereAnswer;
    
    
    /**
     * @var string $name Si esta activo se envia el mensaje de ausencia que se le respondera en unos minutos
     *
     * @ORM\Column(name="restschedulestart", type="string", nullable=true)
     */
    private $restsCheduleStart;
    
    /**
     * @var string $name Si esta activo se envia el mensaje de ausencia que se le respondera en unos minutos
     *
     * @ORM\Column(name="restscheduleend", type="string", nullable=true)
     */
    private $restsCheduleEnd;
    
    
    /**
     * @var string $name mensaje de fuera de horario
     *
     * @ORM\Column(name="restscheduleanswer", type="text", nullable=true)
     */
    private $restsCheduleAnswer;
    
    function getId() {
        return $this->id;
    }

    function getMonday() {
        return $this->monday;
    }

    function getTuesday() {
        return $this->tuesday;
    }

    function getWednesday() {
        return $this->wednesday;
    }

    function getThursday() {
        return $this->thursday;
    }

    function getFriday() {
        return $this->friday;
    }

    function getSaturday() {
        return $this->saturday;
    }

    function getSunday() {
        return $this->sunday;
    }

    function getIsNotHere() {
        return $this->isNotHere;
    }

    function getThereIsNotHereAnswer() {
        return $this->thereIsNotHereAnswer;
    }

    function getRestsCheduleAnswer() {
        return $this->restsCheduleAnswer;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMonday($monday) {
        $this->monday = $monday;
    }

    function setTuesday($tuesday) {
        $this->tuesday = $tuesday;
    }

    function setWednesday($wednesday) {
        $this->wednesday = $wednesday;
    }

    function setThursday($thursday) {
        $this->thursday = $thursday;
    }

    function setFriday($friday) {
        $this->friday = $friday;
    }

    function setSaturday($saturday) {
        $this->saturday = $saturday;
    }

    function setSunday($sunday) {
        $this->sunday = $sunday;
    }

    function setIsNotHere($isNotHere) {
        $this->isNotHere = $isNotHere;
    }

    function setThereIsNotHereAnswer($thereIsNotHereAnswer) {
        $this->thereIsNotHereAnswer = $thereIsNotHereAnswer;
    }

    function setRestsCheduleAnswer($restsCheduleAnswer) {
        $this->restsCheduleAnswer = $restsCheduleAnswer;
    }
    
    function getRestsCheduleStart() {
        return $this->restsCheduleStart;
    }

    function getRestsCheduleEnd() {
        return $this->restsCheduleEnd;
    }

    function setRestsCheduleStart($restsCheduleStart) {
        $this->restsCheduleStart = $restsCheduleStart;
    }

    function setRestsCheduleEnd($restsCheduleEnd) {
        $this->restsCheduleEnd = $restsCheduleEnd;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return "Configuración";
    }

}