<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\FileRepository")
 * @ORM\Table(name="file")
 */
class File
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
     * @ORM\Column(name="filetype", type="string", nullable=true)
     */
    private $fileType;
    
     /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", inversedBy="files", cascade={"persist"})
    * @ORM\JoinColumn(name="media_id", referencedColumnName="id") 
    */
    protected $media;
    
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;
    
     /** 
    * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="files",cascade={"persist"})
    * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id") 
    */
    protected $conversation;
    
     /** 
    * @ORM\ManyToOne(targetEntity="Peticion", inversedBy="files",cascade={"persist"})
    * @ORM\JoinColumn(name="peticion_id", referencedColumnName="id") 
    */
    protected $peticion;
    
    
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="files",cascade={"persist"})
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
    
    
    /**
     * @ORM\OneToOne(targetEntity="Message", inversedBy="file" ,cascade={"persist"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $message;
    
    function getConfiguration() {
        return $this->configuration;
    }

    function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }
    
    function getMedia() {
        return $this->media;
    }

    function setMedia($media) {
        $this->media = $media;
    }
    
    function getConversation() {
        return $this->conversation;
    }

    function setConversation($conversation) {
        $this->conversation = $conversation;
    }

    function getMessage() {
        return $this->message;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }
    
    function getFileType() {
        return $this->fileType;
    }

    function setFileType($fileType) {
        $this->fileType = $fileType;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        return "";
    }

}
