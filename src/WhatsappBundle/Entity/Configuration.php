<?php

namespace WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity(repositoryClass="WhatsappBundle\Repository\ConfigurationRepository")
 * @ORM\Table(name="configuration")
 * @UniqueEntity(fields = {"prefix"})
 */
class Configuration
{
    public function __construct() {
//        $this->id = 1;
        $this->whatsappGroups = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->firstNoFollowKeywords = new ArrayCollection();
        $this->validationKeywords = new ArrayCollection();
        $this->lastAnswerKeywords = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->solutionTypes = new ArrayCollection();
        $this->ticketTypes = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->dayEndWeek = 'sunday';
        $this->hourEndWeek = "23:59:59";
        $this->timeZone = "America/Mexico_City";
        $this->peticions = new ArrayCollection();
        $this->peticionTypes = new ArrayCollection();
        $this->categorys = new ArrayCollection();
        $this->clientActituds = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->presentations = new ArrayCollection();
        $this->motives = new ArrayCollection();
        $this->elementTypes = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->conversationTypes = new ArrayCollection();
        $this->questionPolls = new ArrayCollection();
        $this->polls = new ArrayCollection();
        $this->areas = new ArrayCollection();
        $this->productRecepteds = new ArrayCollection();
        $this->sendedStatuss = new ArrayCollection();
        $this->productSendeds = new ArrayCollection();
        $this->receptedStatuss = new ArrayCollection();
        $this->userConversations = new ArrayCollection();
        $this->ticketSendendAreaUsers = new ArrayCollection();
        $this->messageSendeds = new ArrayCollection();
        $this->ticketSendedAreas = new ArrayCollection();
        $this->claimTypes = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->obleaEnvios = new ArrayCollection();
        $this->obleaRetiros = new ArrayCollection();
        $this->peticionStatuss = new ArrayCollection();
        $this->askAndAnswerss = new ArrayCollection();
    }
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var string $company
     *
     * @ORM\Column(name="company", type="string", nullable=true)
     * @Assert\NotBlank
     */
    private $company;
    
    /**
     * @var string $prefix
     *
     * @ORM\Column(name="prefix", type="string", nullable=true)
     */
    private $prefix;
    
    /**
     * @var string $minutesAnswerAlert
     *
     * @ORM\Column(name="minutesansweralert", type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $minutesAnswerAlert;
    
    /**
     * @var string $minutesResolutionAlert
     *
     * @ORM\Column(name="minutesresolutionalert", type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $minutesResolutionAlert;
    
    /**
     * @var string $enabled
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;
    
    
    /**
     * @var string $demo
     *
     * @ORM\Column(name="demo", type="boolean", nullable=true)
     */
    private $demo;
    
    /**
     * @var string $demo
     *
     * @ORM\Column(name="democompleted", type="boolean", nullable=true)
     */
    private $demoCompleted;
    
    /**
     * @var datetime $dtmmessage
     *
     * @ORM\Column(name="demostartdate", type="datetime", nullable=true)
     */
    private $demoStartDate;
    
    
    /**
     * @var datetime $dtmmessage
     *
     * @ORM\Column(name="demoenddate", type="datetime", nullable=true)
     */
    private $demoEndDate;
    
    /**
     * @var string $isAlertMail
     *
     * @ORM\Column(name="isalertmail", type="boolean", nullable=true)
     */
    private $isAlertMail;
    
    /**
     * @var string $isAlertMail
     *
     * @ORM\Column(name="isalertcall", type="boolean", nullable=true)
     */
    private $isAlertCall;
    
    /**
     * @var string $isAlertMail
     *
     * @ORM\Column(name="isalertsms", type="boolean", nullable=true)
     */
    private $isAlertSms;
    
    function getId() {
        return $this->id;
    }

    function getMinutesAnswerAlert() {
        return $this->minutesAnswerAlert;
    }

    function getMinutesResolutionAlert() {
        return $this->minutesResolutionAlert;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setMinutesAnswerAlert($minutesAnswerAlert) {
        $this->minutesAnswerAlert = $minutesAnswerAlert;
    }

    function setMinutesResolutionAlert($minutesResolutionAlert) {
        $this->minutesResolutionAlert = $minutesResolutionAlert;
    }
    
    function getCompany() {
        return $this->company;
    }

    function getPrefix() {
        return $this->prefix;
    }

    function setCompany($company) {
        $this->company = $company;
    }

    function setPrefix($prefix) {
        $this->prefix = $prefix;
    }
    
    function getEnabled() {
        return $this->enabled;
    }

    function getDemo() {
        return $this->demo;
    }

    function getDemoCompleted() {
        return $this->demoCompleted;
    }

    function getDemoStartDate() {
        return $this->demoStartDate;
    }

    function getDemoEndDate() {
        return $this->demoEndDate;
    }

    function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    function setDemo($demo) {
        $this->demo = $demo;
    }

    function setDemoCompleted($demoCompleted) {
        $this->demoCompleted = $demoCompleted;
    }

    function setDemoStartDate($demoStartDate) {
        $this->demoStartDate = $demoStartDate;
    }

    function setDemoEndDate($demoEndDate) {
        $this->demoEndDate = $demoEndDate;
    }
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="WhatsappGroup", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $whatsappGroups;
    
    
    function getWhatsappGroups() {
        return $this->whatsappGroups;
    }
    
    function setWhatsappGroups($whatsappGroups) {
        $this->whatsappGroups = new ArrayCollection();
        if (count($whatsappGroups) > 0) {
            foreach ($whatsappGroups as $i) {
                $this->addWhatsappGroup($i);
            }
        }
        return $this;
    }
    
    public function addWhatsappGroup(WhatsappGroup $whatsappGroup)
    {
        $whatsappGroup->setConfiguration($this);

        $this->whatsappGroups->add($whatsappGroup);
    }

    public function removeWhatsappGroup(WhatsappGroup $whatsappGroup)
    {
        $this->whatsappGroups->removeElement($whatsappGroup);
    }
    
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $alerts;
    
    
    function getAlerts() {
        return $this->alerts;
    }
    
    function setAlerts($alerts) {
        $this->alerts = new ArrayCollection();
        if (count($alerts) > 0) {
            foreach ($alerts as $i) {
                $this->addAlert($i);
            }
        }
        return $this;
    }
    
    public function addAlert(Alert $alert)
    {
        $alert->setConfiguration($this);

        $this->alerts->add($alert);
    }

    public function removeAlert(Alert $alert)
    {
        $this->alerts->removeElement($alert);
    }
     
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $messages;
    
    
    function getMessages() {
        return $this->messages;
    }
    
    function setMessages($messages) {
        $this->messages = new ArrayCollection();
        if (count($messages) > 0) {
            foreach ($messages as $i) {
                $this->addMessage($i);
            }
        }
        return $this;
    }
    
    public function addMessage(Message $message)
    {
        $message->setConfiguration($this);

        $this->messages->add($message);
    }

    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }
    
     
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="SolutionType", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $solutionTypes;
    
    
    function getSolutionTypes() {
        return $this->solutionTypes;
    }
    
    function setSolutionTypes($solutionTypes) {
        $this->solutionTypes = new ArrayCollection();
        if (count($solutionTypes) > 0) {
            foreach ($solutionTypes as $i) {
                $this->addSolutionType($i);
            }
        }
        return $this;
    }
    
    public function addSolutionType(SolutionType $solutionType)
    {
        $solutionType->setConfiguration($this);

        $this->solutionTypes->add($solutionType);
    }

    public function removeSolutionType(SolutionType $solutionType)
    {
        $this->solutionTypes->removeElement($solutionType);
    }
    
     
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $tickets;
    
    
    function getTickets() {
        return $this->tickets;
    }
    
    function setTickets($tickets) {
        $this->tickets = new ArrayCollection();
        if (count($tickets) > 0) {
            foreach ($tickets as $i) {
                $this->addTicket($i);
            }
        }
        return $this;
    }
    
    public function addTicket(Ticket $ticket)
    {
        $ticket->setConfiguration($this);

        $this->tickets->add($ticket);
    }

    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }
    
     
    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @ORM\OneToMany(targetEntity="TicketType", mappedBy="configuration", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    private $ticketTypes;
    
    
    function getTicketTypes() {
        return $this->ticketTypes;
    }
    
    function setTicketTypes($ticketTypes) {
        $this->ticketTypes = new ArrayCollection();
        if (count($ticketTypes) > 0) {
            foreach ($ticketTypes as $i) {
                $this->addTicketType($i);
            }
        }
        return $this;
    }
    
    public function addTicketType(TicketType $ticketType)
    {
        $ticketType->setConfiguration($this);

        $this->ticketTypes->add($ticketType);
    }

    public function removeTicketType(TicketType $ticketType)
    {
        $this->ticketTypes->removeElement($ticketType);
    }
    
    
    public function getUsers()
    {
        return $this->users;
    }

    public function addUser(UserCompany $user)
    {
        if ($this->getUsers()->contains($user)) {
            return $this;
        }

        $this->getUsers()->add($user);
//        $user->addConfiguration($this);

        return $this;
    }

    public function removeUser(UserCompany $user)
    {
        if (!$this->getUsers()->contains($user)) {
            return $this;
        }

        $this->getUsers()->removeElement($user);
        $user->removeConfiguration($this);

        return $this;
    }
    
    function getIsAlertMail() {
        return $this->isAlertMail;
    }

    function getIsAlertCall() {
        return $this->isAlertCall;
    }

    function getIsAlertSms() {
        return $this->isAlertSms;
    }

    function setIsAlertMail($isAlertMail) {
        $this->isAlertMail = $isAlertMail;
    }

    function setIsAlertCall($isAlertCall) {
        $this->isAlertCall = $isAlertCall;
    }

    function setIsAlertSms($isAlertSms) {
        $this->isAlertSms = $isAlertSms;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        if($this->company)
            return $this->company;
        return "Configuración";
    }
    
    /**
     * @ORM\OneToMany(targetEntity="UserCompany", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $users;
    
    
    /**
     * @var string $timeZone
     *
     * @ORM\Column(name="timezone", type="string", nullable=true)
     */
    private $timeZone;
    function getTimeZone() {
        return $this->timeZone;
    }

    function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;
    }
    
     /** 
    * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="companies")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL") 
    */
    protected $owner;
    
    function getOwner() {
        return $this->owner;
    }

    function setOwner($owner) {
        $this->owner = $owner;
    }

    
    /**
     * @var string $dayEndWeek
     *
     * @ORM\Column(name="dayEndWeek", type="string", nullable=true)
     * @Assert\NotBlank
     * 
     */
    private $dayEndWeek;

    
    /**
     * @var string $hourEndWeek
     *
     * @ORM\Column(name="hourEndWeek", type="string", nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex("/^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/")
     */
    private $hourEndWeek;


    function getDayEndWeek() {
        return $this->dayEndWeek;
    }

    function getHourEndWeek() {
        return $this->hourEndWeek;
    }

    function setDayEndWeek($dayEndWeek) {
        $this->dayEndWeek = $dayEndWeek;
    }

    function setHourEndWeek($hourEndWeek) {
        $this->hourEndWeek = $hourEndWeek;
    }
    
    function getEnabledText() {
        if($this->enabled)
            return "sí";
        return "no";
    }
    
    
    /**
     * @ORM\OneToMany(targetEntity="PeticionType", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $peticionTypes;
    
    function getPeticionTypes() {
        return $this->peticionTypes;
    }
    
    function setPeticionTypes($peticionTypes) {
        $this->peticionTypes = new ArrayCollection();
        if (count($peticionTypes) > 0) {
            foreach ($peticionTypes as $i) {
                $this->addPeticionType($i);
            }
        }
        return $this;
    }
    
    public function addPeticionType(PeticionType $peticionType)
    {
        $peticionType->setConfiguration($this);
        $this->peticionTypes->add($peticionType);
    }

    public function removePeticionType(PeticionType $peticionTypes)
    {
        $this->peticionTypes->removeElement($peticionTypes);
    }
    
    
    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $categorys;
    
    function getCategorys() {
        return $this->categorys;
    }
    
    function setCategorys($categorys) {
        $this->categorys = new ArrayCollection();
        if (count($categorys) > 0) {
            foreach ($categorys as $i) {
                $this->addCategory($i);
            }
        }
        return $this;
    }
    
    public function addCategory(Category $category)
    {
        $category->setConfiguration($this);
        $this->categorys->add($category);
    }

    public function removeCategory(Category $categorys)
    {
        $this->categorys->removeElement($categorys);
    }
    
      /**
     * @ORM\OneToMany(targetEntity="ClientActitud", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $clientActituds;
    
    function getClientActituds() {
        return $this->clientActituds;
    }
    
    function setClientActituds($clientActituds) {
        $this->clientActituds = new ArrayCollection();
        if (count($clientActituds) > 0) {
            foreach ($clientActituds as $i) {
                $this->addClientActitud($i);
            }
        }
        return $this;
    }
    
    public function addClientActitud(ClientActitud $clientActitud)
    {
        $ClientActitud->setConfiguration($this);
        $this->clientActituds->add($clientActitud);
    }

    public function removeClientActitud(ClientActitud $clientActituds)
    {
        $this->clientActituds->removeElement($clientActituds);
    }
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $products;
    
    function getProducts() {
        return $this->products;
    }
    
    function setProducts($products) {
        $this->products = new ArrayCollection();
        if (count($products) > 0) {
            foreach ($products as $i) {
                $this->addProduct($i);
            }
        }
        return $this;
    }
    
    public function addProduct(Product $product)
    {
        $product->setConfiguration($this);
        $this->products->add($product);
    }

    public function removeProduct(Product $products)
    {
        $this->products->removeElement($products);
    }
    
    
    /**
     * @ORM\OneToMany(targetEntity="Presentation", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $presentations;
    
    function getPresentations() {
        return $this->presentations;
    }
    
    function setPresentations($presentations) {
        $this->presentations = new ArrayCollection();
        if (count($presentations) > 0) {
            foreach ($presentations as $i) {
                $this->addPresentation($i);
            }
        }
        return $this;
    }
    
    public function addPresentation(Presentation $presentation)
    {
        $presentation->setConfiguration($this);
        $this->presentations->add($presentation);
    }

    public function removePresentation(Presentation $presentations)
    {
        $this->presentations->removeElement($presentations);
    }
    
    
    /**
     * @ORM\OneToMany(targetEntity="Motive", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $motives;
    
    function getMotives() {
        return $this->motives;
    }
    
    function setMotives($motives) {
        $this->motives = new ArrayCollection();
        if (count($motives) > 0) {
            foreach ($motives as $i) {
                $this->addMotive($i);
            }
        }
        return $this;
    }
    
    public function addMotive(Motive $motive)
    {
        $motive->setConfiguration($this);
        $this->motives->add($motive);
    }

    public function removeMotive(Motive $motives)
    {
        $this->motives->removeElement($motives);
    }
    
    
    /**
     * @ORM\OneToMany(targetEntity="ElementType", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $elementTypes;
    
    function getElementTypes() {
        return $this->elementTypes;
    }
    
    function setElementTypes($elementTypes) {
        $this->elementTypes = new ArrayCollection();
        if (count($elementTypes) > 0) {
            foreach ($elementTypes as $i) {
                $this->addElementType($i);
            }
        }
        return $this;
    }
    
    public function addElementType(ElementType $elementType)
    {
        $elementType->setConfiguration($this);
        $this->elementTypes->add($elementType);
    }

    public function removeElementType(ElementType $elementTypes)
    {
        $this->elementTypes->removeElement($elementTypes);
    }
    
    
    
    /**
     * @ORM\OneToMany(targetEntity="Peticion", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $peticions;
    
    function getPeticions() {
        return $this->peticions;
    }
    
    function setPeticions($peticions) {
        $this->peticions = new ArrayCollection();
        if (count($peticions) > 0) {
            foreach ($peticions as $i) {
                $this->addPeticion($i);
            }
        }
        return $this;
    }
    
    public function addPeticion(Peticion $peticion)
    {
        $peticion->setConfiguration($this);
        $this->peticions->add($peticion);
    }

    public function removePeticion(Peticion $peticions)
    {
        $this->peticions->removeElement($peticions);
    }
    
    /**
     * @ORM\OneToMany(targetEntity="Conversation", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $conversations;
    
    function getConversations() {
        return $this->conversations;
    }
    
    function setConversations($conversations) {
        $this->conversations = new ArrayCollection();
        if (count($conversations) > 0) {
            foreach ($conversations as $i) {
                $this->addConversation($i);
            }
        }
        return $this;
    }
    
    public function addConversation(Conversation $conversation)
    {
        $conversation->setConfiguration($this);
        $this->conversations->add($conversation);
    }

    public function removeConversation(Conversation $conversations)
    {
        $this->conversations->removeElement($conversations);
    }
    
    /**
     * @ORM\OneToMany(targetEntity="ConversationType", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $conversationTypes;
    
    function getConversationTypes() {
        return $this->conversationTypes;
    }
    
    function setConversationTypes($conversationTypes) {
        $this->conversationTypes = new ArrayCollection();
        if (count($conversationTypes) > 0) {
            foreach ($conversationTypes as $i) {
                $this->addConversationType($i);
            }
        }
        return $this;
    }
    
    public function addConversationType(ConversationType $conversationType)
    {
        $conversationType->setConfiguration($this);
        $this->conversationTypes->add($conversationType);
    }

    public function removeConversationType(ConversationType $conversationTypes)
    {
        $this->conversationTypes->removeElement($conversationTypes);
    }
    
     /**
     * @ORM\OneToMany(targetEntity="Poll", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $polls;
    
    function getPolls() {
        return $this->polls;
    }
    
    function setPolls($polls) {
        $this->polls = new ArrayCollection();
        if (count($polls) > 0) {
            foreach ($polls as $i) {
                $this->addPoll($i);
            }
        }
        return $this;
    }
    
    public function addPoll(Poll $poll)
    {
        $poll->setConfiguration($this);
        $this->polls->add($poll);
    }

    public function removePoll(Poll $polls)
    {
        $this->polls->removeElement($polls);
    }
  
     /**
     * @ORM\OneToMany(targetEntity="QuestionPoll", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $questionPolls;
    
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
        $questionPoll->setConfiguration($this);
        $this->questionPolls->add($questionPoll);
    }

    public function removeQuestionPoll(QuestionPoll $questionPolls)
    {
        $this->questionPolls->removeElement($questionPolls);
    }
    
     /**
     * @ORM\OneToMany(targetEntity="Area", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $areas;
    
    function getAreas() {
        return $this->areas;
    }
    
    function setAreas($areas) {
        $this->areas = new ArrayCollection();
        if (count($areas) > 0) {
            foreach ($areas as $i) {
                $this->addArea($i);
            }
        }
        return $this;
    }
    
    public function addArea(Area $area)
    {
        $area->setConfiguration($this);
        $this->areas->add($area);
    }

    public function removeArea(Area $areas)
    {
        $this->areas->removeElement($areas);
    }
    
     /**
     * @ORM\OneToMany(targetEntity="ProductRecepted", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $productRecepteds;
    
    function getProductRecepteds() {
        return $this->productRecepteds;
    }
    
    function setProductRecepteds($productRecepteds) {
        $this->productRecepteds = new ArrayCollection();
        if (count($productRecepteds) > 0) {
            foreach ($productRecepteds as $i) {
                $this->addProductRecepted($i);
            }
        }
        return $this;
    }
    
    public function addProductRecepted(ProductRecepted $productRecepted)
    {
        $productRecepted->setConfiguration($this);
        $this->productRecepteds->add($productRecepted);
    }

    public function removeProductRecepted(ProductRecepted $productRecepteds)
    {
        $this->productRecepteds->removeElement($productRecepteds);
    }
    
     /**
     * @ORM\OneToMany(targetEntity="SendedStatus", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $sendedStatuss;
    
    function getSendedStatuss() {
        return $this->sendedStatuss;
    }
    
    function setSendedStatuss($sendedStatuss) {
        $this->sendedStatuss = new ArrayCollection();
        if (count($sendedStatuss) > 0) {
            foreach ($sendedStatuss as $i) {
                $this->addSendedStatus($i);
            }
        }
        return $this;
    }
    
    public function addSendedStatus(SendedStatus $sendedStatus)
    {
        $sendedStatus->setConfiguration($this);
        $this->sendedStatuss->add($sendedStatus);
    }

    public function removeSendedStatus(SendedStatus $sendedStatuss)
    {
        $this->sendedStatuss->removeElement($sendedStatuss);
    }
  
   /**
     * @ORM\OneToMany(targetEntity="ProductSended", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $productSendeds;
    
    function getProductSendeds() {
        return $this->productSendeds;
    }
    
    function setProductSendeds($productSendeds) {
        $this->productSendeds = new ArrayCollection();
        if (count($productSendeds) > 0) {
            foreach ($productSendeds as $i) {
                $this->addProductSended($i);
            }
        }
        return $this;
    }
    
    public function addProductSended(ProductSended $productSended)
    {
        $productSended->setConfiguration($this);
        $this->productSendeds->add($productSended);
    }

    public function removeProductSended(ProductSended $productSendeds)
    {
        $this->productSendeds->removeElement($productSendeds);
    }
   /**
     * @ORM\OneToMany(targetEntity="ReceptedStatus", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $receptedStatuss;
    
    function getReceptedStatuss() {
        return $this->receptedStatuss;
    }
    
    function setReceptedStatuss($receptedStatuss) {
        $this->receptedStatuss = new ArrayCollection();
        if (count($receptedStatuss) > 0) {
            foreach ($receptedStatuss as $i) {
                $this->addReceptedStatus($i);
            }
        }
        return $this;
    }
    
    public function addReceptedStatus(ReceptedStatus $receptedStatus)
    {
        $receptedStatus->setConfiguration($this);
        $this->receptedStatuss->add($receptedStatus);
    }

    public function removeReceptedStatus(ReceptedStatus $receptedStatuss)
    {
        $this->receptedStatuss->removeElement($receptedStatuss);
    }
  
     /**
     * @ORM\OneToMany(targetEntity="UserConversation", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $userConversations;
    
    function getUserConversations() {
        return $this->userConversations;
    }
    
    function setUserConversations($userConversations) {
        $this->userConversations = new ArrayCollection();
        if (count($userConversations) > 0) {
            foreach ($userConversations as $i) {
                $this->addUserConversation($i);
            }
        }
        return $this;
    }
    
    public function addUserConversation(UserConversation $userConversation)
    {
        $userConversation->setConfiguration($this);
        $this->userConversations->add($userConversation);
    }

    public function removeUserConversation(UserConversation $userConversations)
    {
        $this->userConversations->removeElement($userConversations);
    }
    
     /**
     * @ORM\OneToMany(targetEntity="TicketSendendAreaUser", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
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
        $ticketSendendAreaUser->setConfiguration($this);
        $this->ticketSendendAreaUsers->add($ticketSendendAreaUser);
    }

    public function removeTicketSendendAreaUser(TicketSendendAreaUser $ticketSendendAreaUsers)
    {
        $this->ticketSendendAreaUsers->removeElement($ticketSendendAreaUsers);
    }
  
   /**
     * @ORM\OneToMany(targetEntity="MessageSended", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $messageSendeds;
    
    function getMessageSendeds() {
        return $this->messageSendeds;
    }
    
    function setMessageSendeds($messageSendeds) {
        $this->messageSendeds = new ArrayCollection();
        if (count($messageSendeds) > 0) {
            foreach ($messageSendeds as $i) {
                $this->addMessageSended($i);
            }
        }
        return $this;
    }
    
    public function addMessageSended(MessageSended $messageSended)
    {
        $messageSended->setConfiguration($this);
        $this->messageSendeds->add($messageSended);
    }

    public function removeMessageSended(MessageSended $messageSendeds)
    {
        $this->messageSendeds->removeElement($messageSendeds);
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="TicketSendedArea", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $ticketSendedAreas;
    
    function getTicketSendedAreas() {
        return $this->ticketSendedAreas;
    }
    
    function setTicketSendedAreas($ticketSendedAreas) {
        $this->ticketSendedAreas = new ArrayCollection();
        if (count($ticketSendedAreas) > 0) {
            foreach ($ticketSendedAreas as $i) {
                $this->addTicketSendedArea($i);
            }
        }
        return $this;
    }
    
    public function addTicketSendedArea(TicketSendedArea $ticketSendedArea)
    {
        $ticketSendedArea->setConfiguration($this);
        $this->ticketSendedAreas->add($ticketSendedArea);
    }

    public function removeTicketSendedArea(TicketSendedArea $ticketSendedAreas)
    {
        $this->ticketSendedAreas->removeElement($ticketSendedAreas);
    }
    
   /**
     * @ORM\OneToMany(targetEntity="ClaimType", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $claimTypes;
    
    function getClaimTypes() {
        return $this->claimTypes;
    }
    
    function setClaimTypes($claimTypes) {
        $this->claimTypes = new ArrayCollection();
        if (count($claimTypes) > 0) {
            foreach ($claimTypes as $i) {
                $this->addClaimType($i);
            }
        }
        return $this;
    }
    
    public function addClaimType(ClaimType $claimType)
    {
        $claimType->setConfiguration($this);
        $this->claimTypes->add($claimType);
    }

    public function removeClaimType(ClaimType $claimTypes)
    {
        $this->claimTypes->removeElement($claimTypes);
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $files;
    
    function getFiles() {
        return $this->files;
    }
    
    function setFiles($files) {
        $this->files = new ArrayCollection();
        if (count($files) > 0) {
            foreach ($files as $i) {
                $this->addFile($i);
            }
        }
        return $this;
    }
    
    public function addFile(File $file)
    {
        $file->setConfiguration($this);
        $this->files->add($file);
    }

    public function removeFile(File $files)
    {
        $this->files->removeElement($files);
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="ObleaEnvio", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $obleaEnvios;
    
    function getObleaEnvios() {
        return $this->obleaEnvios;
    }
    
    function setObleaEnvios($obleaEnvios) {
        $this->obleaEnvios = new ArrayCollection();
        if (count($obleaEnvios) > 0) {
            foreach ($obleaEnvios as $i) {
                $this->addObleaEnvio($i);
            }
        }
        return $this;
    }
    
    public function addObleaEnvio(ObleaEnvio $obleaEnvio)
    {
        $obleaEnvio->setConfiguration($this);
        $this->obleaEnvios->add($obleaEnvio);
    }

    public function removeObleaEnvio(ObleaEnvio $obleaEnvios)
    {
        $this->obleaEnvios->removeElement($obleaEnvios);
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="ObleaRetiro", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $obleaRetiros;
    
    function getObleaRetiros() {
        return $this->obleaRetiros;
    }
    
    function setObleaRetiros($obleaRetiros) {
        $this->obleaRetiros = new ArrayCollection();
        if (count($obleaRetiros) > 0) {
            foreach ($obleaRetiros as $i) {
                $this->addObleaRetiro($i);
            }
        }
        return $this;
    }
    
    public function addObleaRetiro(ObleaRetiro $obleaRetiro)
    {
        $obleaRetiro->setConfiguration($this);
        $this->obleaRetiros->add($obleaRetiro);
    }

    public function removeObleaRetiro(ObleaRetiro $obleaRetiros)
    {
        $this->obleaRetiros->removeElement($obleaRetiros);
    }
    
    
    
   /**
     * @ORM\OneToMany(targetEntity="PeticionStatus", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $peticionStatuss;
    
    function getPeticionStatuss() {
        return $this->peticionStatuss;
    }
    
    function setPeticionStatuss($peticionStatuss) {
        $this->peticionStatuss = new ArrayCollection();
        if (count($peticionStatuss) > 0) {
            foreach ($peticionStatuss as $i) {
                $this->addPeticionStatus($i);
            }
        }
        return $this;
    }
    
    public function addPeticionStatus(PeticionStatus $peticionStatus)
    {
        $peticionStatus->setConfiguration($this);
        $this->peticionStatuss->add($peticionStatus);
    }

    public function removePeticionStatus(PeticionStatus $peticionStatuss)
    {
        $this->peticionStatuss->removeElement($peticionStatuss);
    }
    
    
   /**
     * @ORM\OneToMany(targetEntity="AskAndAnswers", mappedBy="configuration" ,cascade={"persist", "remove"}, fetch="EXTRA_LAZY", orphanRemoval=false)
     */
    protected $askAndAnswerss;
    
    function getAskAndAnswerss() {
        return $this->askAndAnswerss;
    }
    
    function setAskAndAnswerss($askAndAnswerss) {
        $this->askAndAnswerss = new ArrayCollection();
        if (count($askAndAnswerss) > 0) {
            foreach ($askAndAnswerss as $i) {
                $this->addAskAndAnswers($i);
            }
        }
        return $this;
    }
    
    public function addAskAndAnswers(AskAndAnswers $askAndAnswers)
    {
        $askAndAnswers->setConfiguration($this);
        $this->askAndAnswerss->add($askAndAnswers);
    }

    public function removeAskAndAnswers(AskAndAnswers $askAndAnswerss)
    {
        $this->askAndAnswerss->removeElement($askAndAnswerss);
    }
}