<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Sonata\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\UserCompanyRepository")
 * @ORM\Table(name="usercompany")
 */
class UserCompany
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
     * @ORM\Column(name="rol", type="string", nullable=true)
     */
    private $rol;
    
    
    function getId() {
        return $this->id;
    }

    function getRol() {
        return $this->rol;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setRol($rol) {
        $this->rol = $rol;
    }

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="configurations", cascade={"persist"})
     * @ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE")
     * @Assert\NotBlank
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Configuration", inversedBy="users", cascade={"persist"})
     * @ORM\JoinColumn(name="configuration", referencedColumnName="id")
     * @Assert\NotBlank
     */
    protected $configuration;
    
    
//    
//    function addConfiguration($configuration) {
//        $this->user->addConfiguration($configuration);
//    }
    
    
    function getUser() {
        return $this->user;
    }

    function getConfiguration() {
        return $this->configuration;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

     /**
     * @return string
     */
    public function __tostring()
    {
        return $this->user->getUsername()." - ".$this->configuration->getCompany();
    }

}
