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

class CompanySummaryStatusApiResponse 
{
    /**
     * Id de la empresa.
     * 
     * @JMS\Type("double");
     */
    public $companyId;
    
    /**
     * Prefijo de la compañía.
     * 
     * @JMS\Type("string");
     */
    public $companyPrefix;

    /**
     * Fecha de consulta.
     * 
     * @JMS\Type("DateTime");
     */
    public $date;

    /**
     * Cantidad de peticiones del día de hoy.
     * 
     * @JMS\Type("integer");
     */
    public $todayTicketsCount;

    /**
     * Cantidad de peticiones de la semana.
     * 
     * @JMS\Type("integer");
     */
    public $thisWeekTicketsCount;

    /**
     * Cantidad de peticiones del mes.
     * 
     * @JMS\Type("integer");
     */
    public $thisMonthTicketsCount;

    /**
     * Promedio de tiempo de respuesta de la semana.
     * 
     * @JMS\Type("float");
     */
    public $mediaAnswerTimeMinutesWeekAgo;
    
    
    /**
     * Promedio de tiempo de solución de la semana.
     * 
     * @JMS\Type("float");
     */
    public $mediaResolutionTimeMinutesWeekAgo;
    


}
