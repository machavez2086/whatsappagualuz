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

class OpenTicketListResponse 
{

    /**
     * Cantidad total de elementos.
     * 
     * @JMS\Type("integer");
     */
    public $total;

    /**
     * Peticiones abiertas.
     * 
     * @JMS\Type("array<WhatsappBundle\Model\OpenTicketApiResponse>");
     */
    public $tickets;

}
