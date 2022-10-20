<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\MessageRepository")
 * @ORM\Table(name="message")
 */
class Message
{
    public function __construct() {
        $this->enabled = true;
    }
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var boolean $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @var boolean $supportFirstAnswer
     *
     * @ORM\Column(name="supportfirstanswer", type="boolean", nullable=true)
     */
    private $supportFirstAnswer;
    
    /**
     * @var boolean $isValidationKeyword
     *
     * @ORM\Column(name="isvalidationkeyword", type="boolean", nullable=true)
     */
    private $isValidationKeyword;

    /**
     * @var string $struid
     *
     * @ORM\Column(name="struid", type="string", nullable=true)
     */
    private $struid;
    
    /**
     * @var string $strcontactuid
     *
     * @ORM\Column(name="strcontactuid", type="string", nullable=true)
     * 
     */
    private $strcontactuid;
    
    /**
     * @var string $strcontactname
     *
     * @ORM\Column(name="strcontactname", type="string", nullable=true)
     * 
     */
    private $strcontactname;
    
    /**
     * @var string $strcontacttype
     *
     * @ORM\Column(name="strcontacttype", type="string", nullable=true)
     * 
     */
    private $strcontacttype;

    /**
     * @var datetime $dtmmessage
     *
     * @ORM\Column(name="dtmmessage", type="datetime", nullable=true)
     */
    private $dtmmessage;
    
    /**
     * @var string $strmenssageuid
     *
     * @ORM\Column(name="strmenssageuid", type="string", nullable=true)
     * 
     */
    private $strmenssageuid;
    
    /**
     * @var string $strmenssagecuid
     *
     * @ORM\Column(name="strmenssagecuid", type="string", nullable=true)
     * 
     */
    private $strmenssagecuid;
    
    /**
     * @var string $strmenssagedir
     *
     * @ORM\Column(name="strmenssagedir", type="string", nullable=true)
     * 
     */
    private $strmenssagedir;
    
    /**
     * @var string $strmenssagetype
     *
     * @ORM\Column(name="strmenssagetype", type="string", nullable=true)
     * 
     */
    private $strmenssagetype;
    
    /**
     * @var string $strmenssagetext
     *
     * @ORM\Column(name="strmenssagetext", type="text", nullable=true)
     * 
     */
    private $strmenssagetext;
    
    /**
     * @var integer $intconversation
     *
     * @ORM\Column(name="intconversation", type="integer", nullable=true)
     * 
     */
    private $intconversation;
    
    /**
     * @var integer $intdiference
     *
     * @ORM\Column(name="intdiference", type="integer", nullable=true)
     * 
     */
    private $intdiference;
    
    /**
     * @var string $strchat
     *
     * @ORM\Column(name="strchat", type="string", nullable=true)
     * 
     */
    private $strchat;
    
    /**
     * @var string $mappedAuthorNick
     *
     * @ORM\Column(name="mappedauthornick", type="text", nullable=true)
     * 
     */
    private $mappedAuthorNick;
    
    /**
     * @var string $problemPart
     *
     * @ORM\Column(name="problempart", type="boolean", nullable=true)
     * 
     */
    private $problemPart;
    
    /**
     * @var string $solutionPart
     *
     * @ORM\Column(name="solutionpart", type="boolean", nullable=true)
     * 
     */
    private $solutionPart;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Ticket", inversedBy="messages")
    * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id", onDelete="SET NULL") 
    */
    protected $ticket;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\WhatsappGroup", inversedBy="messages")
    * @ORM\JoinColumn(name="whatsappgroup_id", referencedColumnName="id", onDelete="SET NULL") 
    */
    protected $whatsappGroup;
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Conversation", inversedBy="messages")
    * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id", onDelete="SET NULL") 
    */
    protected $conversation;
    
    /**
     * @var string $apimessageid
     *
     * @ORM\Column(name="apimessageid", type="text", nullable=true)
     * 
     */
    private $apimessageid;
    
    /**
     * @var string $messagetype
     *
     * @ORM\Column(name="messagetype", type="string", nullable=true)
     * 
     */
    private $messagetype;
    
    /**
     * @var string $senderName
     *
     * @ORM\Column(name="sendername", type="string", nullable=true)
     * 
     */
    private $senderName;
    
    /**
     * @var string $fromMe
     *
     * @ORM\Column(name="fromme", type="boolean", nullable=true)
     * 
     */
    private $fromMe;
    
    /**
     * @var string $author
     *
     * @ORM\Column(name="author", type="text", nullable=true)
     * 
     */
    private $author;
    
    /**
     * @var string $chatId
     *
     * @ORM\Column(name="chatid", type="text", nullable=true)
     * 
     */
    private $chatId;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="messagenumber", type="float", nullable=true)
     * 
     */
    private $messageNumber;
    
    /**
     * @var string $urlMedia
     *
     * @ORM\Column(name="urlmedia", type="text", nullable=true)
     * 
     */
    private $urlMedia;
    
    /**
     * @var string $chatName
     *
     * @ORM\Column(name="chatname", type="text", nullable=true)
     * 
     */
    private $chatName;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="sentimentnumber", type="float", nullable=true)
     * 
     */
    private $sentimentNumber;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="sentimentvader", type="float", nullable=true)
     * 
     */
    private $sentimentVader;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="sentimentspahish", type="float", nullable=true)
     * 
     */
    private $sentimentSpahish;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="sentimenttextblob", type="float", nullable=true)
     * 
     */
    private $sentimentTextblob;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="sentimentasure", type="float", nullable=true)
     * 
     */
    private $sentimentAsure;
    
    /**
     * @var long $messageNumber
     *
     * @ORM\Column(name="procesed", type="boolean", nullable=true)
     * 
     */
    private $procesed;
    
    /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="messages")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id") 
    */
    protected $user;
    
    /**
     * @ORM\OneToOne(targetEntity="MessageSended", mappedBy="whatsappMessage", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $messageSended;
    
     
    /**
     * @var long $isLoadedAudio Cada conversacion crea un mensaje fictisio que se agrega al chat como si se hubiera enviado por whatsapp. Si este campo esta en true es que hay que buscar en conversation los archivos cargados.
     *
     * @ORM\Column(name="isloadedaudio", type="boolean", nullable=true)
     * 
     */
    private $isLoadedAudio;
    
    
    /**
     * @ORM\OneToOne(targetEntity="File", mappedBy="message", cascade={"persist"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $file;
    
   
    function getId() {
        return $this->id;
    }

    function getStruid() {
        return $this->struid;
    }

    function getStrcontactuid() {
        return $this->strcontactuid;
    }

    function getStrcontactname() {
        return $this->strcontactname;
    }

    function getStrcontacttype() {
        return $this->strcontacttype;
    }

    function getDtmmessage() {
        return $this->dtmmessage;
    }
    function getDtmmessageText() {
        $dateAux = clone $this->dtmmessage;
        if($this->configuration){
            $timezone = new \DateTimeZone($this->configuration->getTimezone());
            $dateAux->setTimezone($timezone);
        }
        return $dateAux;
    }

    function getStrmenssageuid() {
        return $this->strmenssageuid;
    }

    function getStrmenssagecuid() {
        return $this->strmenssagecuid;
    }

    function getStrmenssagedir() {
        return $this->strmenssagedir;
    }

    function getStrmenssagetype() {
        return $this->strmenssagetype;
    }

    function getStrmenssagetext() {
        return $this->strmenssagetext;
    }

    function getIntconversation() {
        return $this->intconversation;
    }

    function getIntdiference() {
        return $this->intdiference;
    }

    function getStrchat() {
        return $this->strchat;
    }

    function getTicket() {
        return $this->ticket;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setStruid($struid) {
        $this->struid = $struid;
    }

    function setStrcontactuid($strcontactuid) {
        $this->strcontactuid = $strcontactuid;
    }

    function setStrcontactname($strcontactname) {
        $this->strcontactname = $strcontactname;
    }

    function setStrcontacttype($strcontacttype) {
        $this->strcontacttype = $strcontacttype;
    }

    function setDtmmessage($dtmmessage) {
        $this->dtmmessage = $dtmmessage;
    }

    function setStrmenssageuid($strmenssageuid) {
        $this->strmenssageuid = $strmenssageuid;
    }

    function setStrmenssagecuid($strmenssagecuid) {
        $this->strmenssagecuid = $strmenssagecuid;
    }

    function setStrmenssagedir($strmenssagedir) {
        $this->strmenssagedir = $strmenssagedir;
    }

    function setStrmenssagetype($strmenssagetype) {
        $this->strmenssagetype = $strmenssagetype;
    }

    function setStrmenssagetext($strmenssagetext) {
        $this->strmenssagetext = $strmenssagetext;
    }

    function setIntconversation($intconversation) {
        $this->intconversation = $intconversation;
    }

    function setIntdiference($intdiference) {
        $this->intdiference = $intdiference;
    }

    function setStrchat($strchat) {
        $this->strchat = $strchat;
    }

    function setTicket($ticket) {
        $this->ticket = $ticket;
    }
    
    function getProblemPart() {
        return $this->problemPart;
    }

    function getSolutionPart() {
        return $this->solutionPart;
    }

    function setProblemPart($problemPart) {
        $this->problemPart = $problemPart;
    }

    function setSolutionPart($solutionPart) {
        $this->solutionPart = $solutionPart;
    }
    
    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
    }
    
    function getEnabled() {
        return $this->enabled;
    }

    function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    function getSupportFirstAnswer() {
        return $this->supportFirstAnswer;
    }

    function setSupportFirstAnswer($supportFirstAnswer) {
        $this->supportFirstAnswer = $supportFirstAnswer;
    }
    
    function getMappedAuthorNick() {
        return $this->mappedAuthorNick;
    }

    function setMappedAuthorNick($mappedAuthorNick) {
        $this->mappedAuthorNick = $mappedAuthorNick;
    }

    
    
    function getIsValidationKeyword() {
        return $this->isValidationKeyword;
    }

    function setIsValidationKeyword($isValidationKeyword) {
        $this->isValidationKeyword = $isValidationKeyword;
    }

    
    
    /** 
    * @ORM\ManyToOne(targetEntity="WhatsappBundle\Entity\Configuration", inversedBy="messages")
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
        if($this->strmenssagetext != "")
            return $this->strmenssagetext;
        return "";
    }
    function getIsValidationKeywordText() {
        if($this->isValidationKeyword)
            return "sí";
        return "no";
    }
    function supportFirstAnswerText() {
        if($this->supportFirstAnswer)
            return "sí";
        return "no";
    }
    
    function getApimessageid() {
        return $this->apimessageid;
    }

    function getMessagetype() {
        return $this->messagetype;
    }

    function getSenderName() {
        return $this->senderName;
    }

    function getFromMe() {
        return $this->fromMe;
    }

    function getAuthor() {
        return $this->author;
    }

    function getChatId() {
        return $this->chatId;
    }

    function getMessageNumber() {
        return $this->messageNumber;
    }

    function setApimessageid($apimessageid) {
        $this->apimessageid = $apimessageid;
    }

    function setMessagetype($messagetype) {
        $this->messagetype = $messagetype;
    }

    function setSenderName($senderName) {
        $this->senderName = $senderName;
    }

    function setFromMe($fromMe) {
        $this->fromMe = $fromMe;
    }

    function setAuthor($author) {
        $this->author = $author;
    }

    function setChatId($chatId) {
        $this->chatId = $chatId;
    }

    function setMessageNumber($messageNumber) {
        $this->messageNumber = $messageNumber;
    }

    function getUrlMedia() {
        return $this->urlMedia;
    }

    function setUrlMedia($urlMedia) {
        $this->urlMedia = $urlMedia;
    }

    function getChatName() {
        return $this->chatName;
    }

    function setChatName($chatName) {
        $this->chatName = $chatName;
    }
    
    function getSentimentNumber() {
        return $this->sentimentNumber;
    }

    function setSentimentNumber($sentimentNumber) {
        $this->sentimentNumber = $sentimentNumber;
    }

    function getSentimentVader() {
        return $this->sentimentVader;
    }

    function getSentimentSpahish() {
        return $this->sentimentSpahish;
    }

    function getSentimentTextblob() {
        return $this->sentimentTextblob;
    }

    function getSentimentAsure() {
        return $this->sentimentAsure;
    }

    function setSentimentVader($sentimentVader) {
        $this->sentimentVader = $sentimentVader;
    }

    function setSentimentSpahish($sentimentSpahish) {
        $this->sentimentSpahish = $sentimentSpahish;
    }

    function setSentimentTextblob($sentimentTextblob) {
        $this->sentimentTextblob = $sentimentTextblob;
    }

    function setSentimentAsure($sentimentAsure) {
        $this->sentimentAsure = $sentimentAsure;
    }
    
    function getProcesed() {
        return $this->procesed;
    }

    function setProcesed($procesed) {
        $this->procesed = $procesed;
    }
    
    function getUser() {
        return $this->user;
    }

    function getMessageSended() {
        return $this->messageSended;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setMessageSended($messageSended) {
        $this->messageSended = $messageSended;
    }
    
    function getConversation() {
        return $this->conversation;
    }

    function setConversation($conversation) {
        $this->conversation = $conversation;
    }
    
    function getIsLoadedAudio() {
        return $this->isLoadedAudio;
    }

    function setIsLoadedAudio($isLoadedAudio) {
        $this->isLoadedAudio = $isLoadedAudio;
    }
    
    function getFile() {
        return $this->file;
    }

    function setFile($file) {
        $this->file = $file;
        $file->setMessage($this);
    }


}