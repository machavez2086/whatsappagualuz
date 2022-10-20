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

class TicketApiResponse 
{
    /**
     * Id de la petición.
     * 
     * @JMS\Type("double");
     */
    public $id;
    /**
     * Nombre de la petición.
     * 
     * @JMS\Type("string");
     */
    public $name;

    /**
     * Fecha de inicio de la petición.
     * 
     * @JMS\Type("DateTime");
     */
    public $startDate;

    /**
     * Día de la semana.
     * 
     * @JMS\Type("string");
     */
    public $weekDay;

    /**
     * Hora de respuesta.
     * 
     * @JMS\Type("DateTime");
     */
    public $resolutionDate;
    
    
    /**
     * Tiempo de respuesta en minutos.
     * 
     * @JMS\Type("integer");
     */
    public $timeAnswer;
    
    /**
     * Recurso o miembro de soporte que resolvió la petición de soporte.
     * 
     * @JMS\Type("string");
     */
    public $solvedBySupportMember;
    
    /**
     * Tipo o categoría de petición.
     * 
     * @JMS\Type("string");
     */
    public $ticketType;
    
    
    /**
     * Hora de cierre de la petición.
     * 
     * @JMS\Type("DateTime");
     */
    public $endDate;
    
    /**
     * Petición atendida.
     * 
     * @JMS\Type("boolean");
     */
    public $ticketAnswered;
    
    /**
     * Alerta de petición no atendida enviada.
     * 
     * @JMS\Type("boolean");
     */
    public $alertAnswerSended;
    
    /**
     * Alerta de petición solución fuera de tiempo enviada.
     * 
     * @JMS\Type("boolean");
     */
    public $alertSolutionSended;

    
    /**
     * Petición finalizada.
     * 
     * @JMS\Type("boolean");
     */
    public $ticketEnded;
    
    /**
     * Grupo de whatsapp.
     * 
     * @JMS\Type("string");
     */
    public $whatsappGroup;
    
    /**
     * Es un tique de no registro.
     * 
     * @JMS\Type("boolean");
     */
    public $noRegisterTicket;

}
