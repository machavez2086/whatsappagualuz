<?php

namespace WhatsappBundle\FormDataClass;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class TicketChangeGroup
{
    public function __construct() {
        $this->peticiones = new ArrayCollection();
    }
   
    private $peticiones;
    private $whatsappGroup;
    
    function getPeticiones() {
        return $this->peticiones;
    }

    function getWhatsappGroup() {
        return $this->whatsappGroup;
    }

    function setPeticiones($peticiones) {
        $this->peticiones = $peticiones;
    }

    function setWhatsappGroup($whatsappGroup) {
        $this->whatsappGroup = $whatsappGroup;
    }
    
    function addPeticion($ticket){
        $this->peticiones->add($ticket);
    }

    
    /**
     * @return string
     */
    public function __tostring()
    {
        return "s";
    }

}
