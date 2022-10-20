<?php

namespace WhatsappBundle\FormDataClass;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class MessagesChangeTicket
{
    public function __construct() {
        $this->messages = new ArrayCollection();
    }
   
    private $ticket;
    private $messages;
    
    function getTicket() {
        return $this->ticket;
    }

    function getMessages() {
        return $this->messages;
    }
    function addMessage($message){
        $this->messages->add($message);
    }
    function setTicket($ticket) {
        $this->ticket = $ticket;
    }

    function setMessages($messages) {
        $this->messages = $messages;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return "s";
    }

}
