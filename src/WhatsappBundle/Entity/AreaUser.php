<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\AreaUserRepository")
 * @ORM\Table(name="areauser")
 */
class AreaUser
{ 
      public function __construct() {
        $this->ticketSendendAreaUsers = new ArrayCollection();
      }  
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="areaUsers")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id") 
    */
    protected $user;
    
     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Area", inversedBy="ticketSendedAreas")
    * @ORM\JoinColumn(name="area_id", referencedColumnName="id") 
    */
    protected $area;
    
    
    function getId() {
        return $this->id;
    }

     
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="peticionTypes")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    function getUser() {
        return $this->user;
    }

    function getArea() {
        return $this->area;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setArea($area) {
        $this->area = $area;
    }
    
     /**
     * @ORM\OneToMany(targetEntity="TicketSendendAreaUser", mappedBy="areaUser" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $ticketSendendAreaUsers;
    
    function getTicketSendendAreaUsers() {
        return $this->ticketSendendAreaUsers;
    }
    
    function setTicketSendendAreaUsers($ticketSendendAreaUsers) {
        $this->ticketSendendAreaUsers = new ArrayCollection();
        if (count($ticketSendendAreaUsers) > 0) {
            foreach ($ticketSendendAreaUsers as $i) {
                $this->addSolutionType($i);
            }
        }
        return $this;
    }
    
    public function addTicketSendendAreaUser(TicketSendendAreaUser $ticketSendendAreaUser)
    {
        $ticketSendendAreaUser->setAreaUser($this);
        $this->ticketSendendAreaUsers->add($ticketSendendAreaUser);
    }

    public function removeTicketSendendAreaUser(TicketSendendAreaUser $ticketSendendAreaUsers)
    {
        $this->ticketSendendAreaUsers->removeElement($ticketSendendAreaUsers);
    }
  

    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
