<?php

namespace Deepweb\ClasificadosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="Deepweb\ClasificadosBundle\Entity\AlertRepository")
 * @ORM\Table(name="alert")
 */
class Alert
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string $kind
     *
     * @ORM\Column(name="kind", type="string", nullable=true)
     */
    private $kind;
    
    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     * 
     */
    private $name;

    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media") 
    */
    protected $photo;
   

    /**
     * @var string $body
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;
    /**
     * @var datetime $postDate
     *
     * @ORM\Column(name="post_date", type="datetime", nullable=true)
     */
    private $postDate;
    
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\ClassificationBundle\Entity\Category") 
    * @ORM\JoinColumn(onDelete="SET NULL")
    */
    protected $subcategoria;
    
    
    /**
     * @var integer $priority
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     * 
     */
    private $priority;
    
    /** 
    * @ORM\ManyToOne(targetEntity="Deepweb\ClasificadosBundle\Entity\Announcement") 
    * @ORM\JoinColumn(onDelete="SET NULL")
    */
    protected $anuncio;
    
    /** 
    * @ORM\ManyToOne(targetEntity="Deepweb\ClasificadosBundle\Entity\Provincia") 
    * @ORM\JoinColumn(onDelete="SET NULL")
    */
    protected $provincia;
    
    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set kind
     *
     * @param  $kind
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    }

    /**
     * Get kind
     *
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }
    
    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

   
    /**
     * Set subcategoria
     *
     * @param Clasificados\BackendBundle\Entity\Subcategoria $subcategoria
     */
    public function setPhoto(\Application\Sonata\MediaBundle\Entity\Media $media = null)
    {
        $this->photo = $media;
    }

    /**
     * Get subcategoria
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    
    /**
     * Set body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Set postDate
     *
     * @param datetime $postDate
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;
    }

    /**
     * Get postDate
     *
     * @return datetime 
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    
    /**
     * Set subcategoria
     *
     * @param Clasificados\BackendBundle\Entity\Subcategoria $subcategoria
     */
    public function setSubcategoria(\Application\Sonata\ClassificationBundle\Entity\Category $subcategoria = null)
    {
        $this->subcategoria = $subcategoria;
    }

    /**
     * Get subcategoria
     *
     * @return Clasificados\BackendBundle\Entity\Subcategoria 
     */
    public function getSubcategoria()
    {
        return $this->subcategoria;
    }
    
    /**
     * Set priority
     *
     * @param  $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * Set subcategoria
     *
     * @param Deepweb\ClasificadosBundle\Entity\Announcement $anuncio
     */
    public function setAnuncio(\Deepweb\ClasificadosBundle\Entity\Announcement $anuncio = null)
    {
        $this->anuncio = $anuncio;
    }

    /**
     * Get subcategoria
     *
     * @return Deepweb\ClasificadosBundle\Entity\Announcement 
     */
    public function getAnuncio()
    {
        return $this->anuncio;
    }
    
    /**
     * Set subcategoria
     *
     * @param Clasificados\BackendBundle\Entity\Subcategoria $subcategoria
     */
    public function setProvincia(\Deepweb\ClasificadosBundle\Entity\Provincia $provincia = null)
    {
        $this->provincia = $provincia;
    }

    /**
     * Get subcategoria
     *
     * @return Deepweb\ClasificadosBundle\Entity\Provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }
    
    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->name;
    }

}