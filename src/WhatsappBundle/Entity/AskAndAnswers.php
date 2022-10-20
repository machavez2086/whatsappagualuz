<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\AskAndAnswersRepository")
 * @ORM\Table(name="askandanswers")
 */
class AskAndAnswers
{
    
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="question", type="text", nullable=true)
     */
    private $question;

    /**
     * @var boolean $supportFirstAnswer
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;
    

    /**
     * @var string $struid
     *
     * @ORM\Column(name="normalizedquestion", type="text", nullable=true)
     */
    private $normalizedQuestion;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="askAndAnswerss")
    * @ORM\JoinColumn(name="configuration_id", referencedColumnName="id") 
    */
    protected $configuration;
   
    function getId() {
        return $this->id;
    }
    
    function getQuestion() {
        return $this->question;
    }

    function getAnswer() {
        return $this->answer;
    }

    function getNormalizedQuestion() {
        return $this->normalizedQuestion;
    }

    function setQuestion($question) {
        $this->question = $question;
    }

    function setAnswer($answer) {
        $this->answer = $answer;
    }

    function setNormalizedQuestion($normalizedQuestion) {
        $this->normalizedQuestion = $normalizedQuestion;
    }
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
        if($this->question != "")
            return $this->question;
        return "";
    }

}