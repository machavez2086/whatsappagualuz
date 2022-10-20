<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\LogRepository")
 * @ORM\Table(name="log")
 */
class Log
{
    public function __construct() {
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
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="detexteddate", type="datetime", nullable=true)
     */
    private $detexteddate;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="deactive", type="boolean", nullable=true)
     */
    private $deactive;

    function getId() {
        return $this->id;
    }

    function getMessage() {
        return $this->message;
    }

    function getDetexteddate() {
        return $this->detexteddate;
    }

    function getType() {
        return $this->type;
    }

    function getDeactive() {
        return $this->deactive;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setDetexteddate($detexteddate) {
        $this->detexteddate = $detexteddate;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setDeactive($deactive) {
        $this->deactive = $deactive;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->message;
    }

}
