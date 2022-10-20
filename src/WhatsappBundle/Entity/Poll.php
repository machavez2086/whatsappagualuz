<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\PollRepository")
 * @ORM\Table(name="poll")
 */
class Poll
{ 
    public function __construct() {
        $this->questionPolls = new ArrayCollection();
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
     * @ORM\OneToMany(targetEntity="QuestionPoll", mappedBy="poll", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $questionPolls;
    
    function getQuestionPolls() {
        return $this->questionPolls;
    }
    
    function setQuestionPolls($questionPolls) {
        $this->questionPolls = new ArrayCollection();
        if (count($questionPolls) > 0) {
            foreach ($questionPolls as $i) {
                $this->addQuestionPoll($i);
            }
        }
        return $this;
    }
    
    public function addQuestionPoll(QuestionPoll $questionPoll)
    {
        $questionPoll->setPoll($this);
        $this->questionPolls->add($questionPoll);
    }

    public function removeQuestionPoll(QuestionPoll $questionPoll)
    {
        $this->questionPolls->removeElement($questionPoll);
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
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="polls")
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
    public function __tostring()
    {
        return $this->name;
    }

}
