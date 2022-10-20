<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ContactRepository")
 * @ORM\Table(name="contact")
 */
class Contact
{
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
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;
    
    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;
    
    function getId() {
        return $this->id;
    }

    function getMessage() {
        return $this->message;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        if($this->name)
        return $this->name;
        return "";
    }

}