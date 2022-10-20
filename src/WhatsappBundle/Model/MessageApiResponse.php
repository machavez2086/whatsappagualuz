<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhatsappBundle\Model;

use JMS\Serializer\Annotation as JMS;

class MessageApiResponse 
{
    /**
     * Id del mensaje.
     * 
     * @JMS\Type("double");
     */
    public $id;
    /**
     * Verifica si es mensaje de tipo primera respuesta.
     * 
     * @JMS\Type("boolean");
     */
    public $supportFirstAnswer;
    /**
     * Verifica si es mensaje de tipo primera respuesta.
     * 
     * @JMS\Type("boolean");
     */
    public $fromMe;
    
    /**
     * Verifica si es mensaje de tipo validación.
     * 
     * @JMS\Type("boolean");
     */
    public $isValidationKeyword;

    /**
     * Id de la petición relacionada.
     * 
     * @JMS\Type("integer");
     */
    public $ticket;

    /**
     * Id del grupo de whatsapp relacionado.
     * 
     * @JMS\Type("integer");
     */
    public $whatsappGroup;

    /**
     * Id del miembro de soporte relacionado.
     * 
     * @JMS\Type("integer");
     */
    public $supportMember;

    /**
     * Id del miembro cliente relacionado.
     * 
     * @JMS\Type("integer");
     */
    public $clientMember;
    
    /**
     * Cuerpo del mensaje.
     * 
     * @JMS\Type("string");
     */
    public $strmenssagetext;

    /**
     * Fecha del mensaje.
     * 
     * @JMS\Type("DateTime");
     */
    public $dtmmessage;

}
