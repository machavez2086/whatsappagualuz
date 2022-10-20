<?php

namespace WhatsappBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use WhatsappBundle\Model\OpenTicketApiResponse;
use WhatsappBundle\Model\OpenTicketListResponse;
use WhatsappBundle\Model\TicketListResponse;
use WhatsappBundle\Model\TicketApiResponse;
use WhatsappBundle\Model\MessageApiResponse;
use WhatsappBundle\Model\MessageListResponse;
use WhatsappBundle\Model\CompanySummaryStatusApiResponse;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiController extends FOSRestController implements ClassResourceInterface {

    /**
     * @Route("/api/pushmessage/{id}", name="pushmessage")
     * @Method({"GET", "POST"})
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna los datos de un ticket dado su id", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\TicketApiResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when unsuccessful",
     *     400 = "Bad request",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function pushMessageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() == 'POST') {
            $key = $request->request->get('token');
            if ($key == 'fdsdk$dsfkj56454fdsfdsd5f465') {
                $message = $em->getRepository('WhatsappBundle:Message')->find($id);
                $pusher = $this->get('gos_web_socket.wamp.pusher');
                $pusher->push(array("id" => $message->getId(), "strmenssagetext" => $message->getStrmenssagetext(), "from_me" => $message->getFromMe()), "acme_topic", array('id' => $message->getWhatsappGroup()->getId()), array());
                $pusher->push(array("id" => $message->getId(), "strmenssagetext" => $message->getStrmenssagetext(), "from_me" => $message->getFromMe()), "acme_global", array(), array());
                
                $conversation = $message->getConversation();
                $conversation->setUnreadMessage(true);
                $em->persist($conversation);
                $em->flush();
//                $pusher->push(array("msg" => $message->getStrmenssagetext()), "acme_topic", array('id' => $message->getConversation()->getId()), array());
            }
        }
//        $message = $em->getRepository('WhatsappBundle:Message')->find($id);
//        
//        $pusher = $this->get('gos_web_socket.wamp.pusher');
//        $pusher->push(array("id" => $message->getId(), "strmenssagetext" => $message->getStrmenssagetext(), "from_me" => $message->getFromMe()), "acme_topic", array('id' => $message->getWhatsappGroup()->getId()), array());
//        $pusher->push(array("id" => $message->getId(), "strmenssagetext" => $message->getStrmenssagetext(), "from_me" => $message->getFromMe()), "acme_global", array(), array());
//        $conversation = $message->getConversation();
//        $conversation->setUnreadMessage(true);
//        $em->persist($conversation);
//        $em->flush();
        
        
        $view = $this->view(array());
        return $view;
//        $pusher = $this->get('gos_web_socket.amqp.pusher');
//         $pusher = $this->get('app.websocket.topic.acme');
//         $pusher->push(array("msg" => "hola"), "acme_topic", array(), array());
//        $pusher->push(array("msg" => "hola"), "acme_topic", array('id' => '2'));
//        $pusher->push(['msg' => 'data'], 'acme_topic', ['id' => '1']);
//        die();
//        $pusher->push("hola", "acme_topic", array(), array());
    }

    /**
     * @Route("/api/api_get_conversation_messages/{id}", name="api_get_conversation_messages")
     * @Method({"GET", "POST"})
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna los datos de un ticket dado su id", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\MessageListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when unsuccessful",
     *     400 = "Bad request",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function apiGetConversationMessagesAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $conversation = $em->getRepository('WhatsappBundle:Conversation')->find($id);
        $messages = $em->getRepository('WhatsappBundle:Message')->findLast15ByConversation($conversation);
        $result = new MessageListResponse();
        $sends = array();
        foreach ($messages as $value) {
            $resp = new MessageApiResponse();
            $resp->strmenssagetext = $value->getStrmenssagetext();
            $resp->fromMe = $value->getFromMe();
            $sends[] = $resp;
        }
        $result->messages = $sends;
        $view = $this->view($result);
        return $view;
    }

    /**
     * @Route("/api/getopenstickets", name="getopenstickets")
     * @Method({"GET", "POST"})
     * 
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna las peticiones abiertas", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\OpenTicketListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getOpensTicketsAction(Request $request) {
        $totalsend = array();
        $em = $this->getDoctrine()->getManager();
        $openedTickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsOpeneds(1);
        $response = new OpenTicketListResponse();
        foreach ($openedTickets as $key => $value) {
            $result = new OpenTicketApiResponse();
            $i = array();
            $result->id = $value->getId();
            $result->name = $value->getName();
            $result->startDate = $value->getStartDate();
            if ($value->getDayOfWeek($value->getStartDate()))
                $result->weekDay = $value->SpanishDate($value->getDayOfWeek($value->getStartDate()));
            else
                $result->weekDay = null;
            $result->resolutionDate = $value->getResolutionDate();
            $result->timeAnswer = $value->getMinutesAnswerTime();
            $result->solvedBySupportMember = $value->getSolvedBySupportMember();
            $result->ticketType = $value->getTicketType();
            $result->lastMessageDate = $value->getEndDate();
//            $i["tiempo_de_solucion"] = $value->getMinutesSolutionTime();
            $result->ticketAnswered = $value->getFirstanswer();
            $response->alertAnswerSended = $value->getSendalert();
            $response->alertSolutionSended = $value->getSendalertSolution();
//            $i["peticion_finalizada"] = $value->getTicketended();
//            $i["no_registro"] = $value->getTicketended();
            $totalsend[] = $result;
//            $value["cantprofesionals"] = $this->getDoctrine()->getManager()->getRepository('DirectorioBundle:Profesional')
//                ->getProfesionalesCantByCategory($value["id"]);
//            $value["cantempresas"] = $this->getDoctrine()->getManager()->getRepository('DirectorioBundle:Empresa')
//                ->getEmpresasCantByCategory($value["id"]);
//            $totalsend[] = $value;
        }
        $response->total = count($openedTickets);
        $response->tickets = $totalsend;

        $view = $this->view($response);
//                dump($view);die;
        return $view;
    }

    /**
     * @Route("/api/getticketbyid/{id}", name="getticketbyid")
     * @Method({"GET", "POST"})
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna los datos de un ticket dado su id", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\TicketApiResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when unsuccessful",
     *     400 = "Bad request",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getTicketByIdAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $value = $em->getRepository('WhatsappBundle:Ticket')->find($id);
        if (!$value) {
            throw new HttpException(404, "Ticket {$id} does not exist in this context");
        }
        $response = new TicketApiResponse();
        if ($value) {
            $response->id = $value->getId();
            $response->name = $value->getName();
            $response->startDate = $value->getStartDate();
            if ($value->getDayOfWeek($value->getStartDate()))
                $response->weekDay = $value->SpanishDate($value->getDayOfWeek($value->getStartDate()));
            else
                $response->weekDay = null;
            $response->resolutionDate = $value->getResolutionDate();
            $response->timeAnswer = $value->getMinutesAnswerTime();
            $response->solvedBySupportMember = $value->getSolvedBySupportMember();
            $response->ticketType = $value->getTicketType();
            $response->endDate = $value->getEndDate();
//            $i["tiempo_de_solucion"] = $value->getMinutesSolutionTime();
            $response->ticketAnswered = $value->getFirstanswer();
            $response->alertAnswerSended = $value->getSendalert();
            $response->alertSolutionSended = $value->getSendalertSolution();
            $response->ticketEnded = $value->getTicketended();
            $response->whatsappGroup = $value->getWhatsappGroup();
            $response->noRegisterTicket = $value->getNofollow();
        }

        $view = $this->view($response);
//                dump($view);die;
        return $view;
    }

    /**
     * Método que lista las peticiones a partir de varios filtros opcionales con configuraciones avanzadas. Cada filtro posee como mínimo dos parámetros, el tipo y el valor. Existen filtros de tipo between y notbetween que permiten dos valores para establecer un rango. En caso de que se omita el tipo pero se especifica un valor se toma por defecto los tipos like en los campos de tipo string y eq en el resto de los casos.<br/> 
     * Descripción de requerimientos para tipos de filtros y las operaciones que permiten: <br/> eq: es igual a<br/> neq: no es igua a<br/> lt: menor que<br/> lte: menor o igual que<br/> gt: mayor que<br/> gte: mayor o igual que<br/> isempty: está vacío<br/> isnotempty: no está vacío<br/> isnull: es nulo<br/> isnottull: no es nulo<br/> like: contiene el elemento<br/> notlike: no contiene el elemento<br/> between: Está entre los valores definidos por start y end<br/> notbetween: No está entre los valores definidos por start y end.<br/>
     * El resultado puede ser paginado y ordenado a partir de los campos de la entidad Ticket (id, name, startDate, startTime, endDate, minutesSolutionTime, minutesAnswerTime, ticketType, solutionType, whatsappGroup, solvedBySupportMember, sendalert, sendalertSolution, firstanswer, ticketended, nofollow, isValidated, weekday, validationCount, minutesDevTime, minutesValidationWaitTime)
     * 
     * @QueryParam(name="filter_id_type", requirements="(eq|neq|lt|lte|gt|gte|between|notbetween)", description="Tipo de filtro para el campo Id. ", nullable=true, strict=true)
     * @QueryParam(name="filter_id_value", requirements="\d+", description="Valor de filtro para el campo Id.", nullable=true, strict=true)
     * @QueryParam(name="filter_name_type", requirements="(eq|neq|isempty|isnotempty|isnull|isnottull|like|notlike)", description="Tipo de filtro para el campo Nombre.", nullable=true, strict=true)
     * @QueryParam(name="filter_name_value", requirements=".*", description="Valor de filtro para el campo Nombre.", nullable=true, strict=true)
     * @QueryParam(name="filter_weekday_type", requirements="(eq|neq)", description="Tipo de filtro para el campo Día de la semana.", nullable=true, strict=true)
     * @QueryParam(name="filter_weekday_value", requirements="(Lunes|Martes|Miércoles|Jueves|Viernes|Sábado|Domingo)", description="Valor de filtro para el campo Día de la semana.", nullable=true, strict=true)
     * @QueryParam(name="filter_whatsappGroup_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Grupo.", nullable=true, strict=true)
     * @QueryParam(name="filter_whatsappGroup_value", requirements="\d+", description="Id que representa la relación con Grupo.", nullable=true, strict=true)
     * @QueryParam(name="filter_solvedBySupportMember_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Miembro de soporte que resolvión la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_solvedBySupportMember_value", requirements="\d+", description="Id que representa la relación con miembro del grupo de soporte que dio solución a la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_ticketType_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Tipo de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_ticketType_value", requirements="\d+", description="Id que representa el tipo de petición asociado a la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_solutionType_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Tipo de solución.", nullable=true, strict=true)
     * @QueryParam(name="filter_solutionType_value", requirements="\d+", description="Id que representa el tipo de solución asociado a la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_firstanswer_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Primera respuesta.", nullable=true, strict=true)
     * @QueryParam(name="filter_firstanswer_value", requirements="(true|false)", description="Valor de filtro para el campo Primera respuesta. El campo contiene true si la petición fue respondida por un miembro de soporte, false en caso contrario", nullable=true, strict=true)
     * @QueryParam(name="filter_ticketended_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición finalizada.", nullable=true, strict=true)
     * @QueryParam(name="filter_ticketended_value", requirements="(true|false)", description="Valor de filtro para el campo Petición finalizada. El campo contiene true si la petición fue cerrada, false en caso contrario.", nullable=true, strict=true)
     * @QueryParam(name="filter_sendalert_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Alerta de tipo respuesta enviada.", nullable=true, strict=true)
     * @QueryParam(name="filter_sendalert_value", requirements="(true|false)", description="Valor de filtro para el campo Alerta de tipo respuesta enviada. El campo contiene true si se envió una alerta de tipo respuesta asociada a la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_sendalertSolution_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Alerta de tipo solución enviada.", nullable=true, strict=true)
     * @QueryParam(name="filter_sendalertSolution_value", requirements="(true|false)", description="Valor de filtro para el campo Alerta de tipo solución enviada. El campo contiene true si se envió una alerta de tipo solución asociada a la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_nofollow_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición de tipo aviso.", nullable=true, strict=true)
     * @QueryParam(name="filter_nofollow_value", requirements="(true|false)", description="Valor de filtro para el campo Petición de tipo aviso. El campo contiene true si la petición es marcada como de tipo aviso.", nullable=true, strict=true)
     * @QueryParam(name="filter_isValidated_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición validada.", nullable=true, strict=true)
     * @QueryParam(name="filter_isValidated_value", requirements="(true|false)", description="Valor de filtro para el campo Petición validada. El campo contiene true si la petición ha sido validada.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_minutesAnswerTime_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de primera respuesta.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesAnswerTime_value", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de primera respuesta.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesAnswerTime_value_start", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de primera respuesta. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesAnswerTime_value_end", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de primera respuesta. . Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_minutesSolutionTime_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de solución.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesSolutionTime_value", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de solución.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesSolutionTime_value_start", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de solución. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesSolutionTime_value_end", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de solución. Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_minutesDevTime_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de desarrollo.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesDevTime_value", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de desarrollo.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesDevTime_value_start", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de desarrollo. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesDevTime_value_end", requirements="\d+\.?\d*", description="Valor de filtro para el campo Minutos de desarrollo. Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_minutesValidationWaitTime_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Tiempo de espera de validación.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesValidationWaitTime_value", requirements="\d+\.?\d*", description="Valor de filtro para el campo Tiempo de espera de validación.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesValidationWaitTime_value_start", requirements="\d+\.?\d*", description="Valor de filtro para el campo Tiempo de espera de validación. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_minutesValidationWaitTime_value_end", requirements="\d+\.?\d*", description="Valor de filtro para el campo Tiempo de espera de validación. Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_startDate_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Fecha de comienzo de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_startDate_value", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de comienzo de petición. Ejemplo: 10-05-2018 02:25:34", nullable=true, strict=true)
     * @QueryParam(name="filter_startDate_value_start", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de comienzo de petición. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_startDate_value_end", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de comienzo de petición. Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_endDate_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Fecha de fin de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_endDate_value", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de fin de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_endDate_value_start", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de fin de petición. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_endDate_value_end", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha de fin de petición. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="filter_startTime_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Hora de inicio de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_startTime_value", requirements="\d\d\:\d\d\:?\d?\d?", description="Valor de filtro para el campo Hora de inicio de petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_startTime_value_start", requirements="\d\d\:\d\d\:?\d?\d?", description="Valor de filtro para el campo Hora de inicio de petición. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_startTime_value_end", requirements="\d\d\:\d\d\:?\d?\d?", description="Valor de filtro para el campo Hora de inicio de petición. Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     *
     * @QueryParam(name="_out_of_range", requirements="(true|false)", description="Filtra las peticiones que no cumplen con los parametros configurados (tiempo de respuesta, tiempo de solución).", nullable=true, strict=true)
     * @QueryParam(name="_page", requirements="\d+", default=1, strict=true, description="Página desde la cual se traen los resultados.", nullable=true, strict=true)
     * @QueryParam(name="_per_page", requirements="\d+", default=10, strict=false, description="Cantidad de elementos por página.", nullable=true, strict=true)
     * @QueryParam(name="_sort_by", requirements="(id|name|startDate|startTime|endDate|minutesSolutionTime|minutesAnswerTime|ticketType|solutionType|whatsappGroup|solvedBySupportMember|sendalert|sendalertSolution|firstanswer|ticketended|nofollow|isValidated|weekday|validationCount|minutesDevTime|minutesValidationWaitTime)", allowBlank=false, default="id", description="Campo por el que se desea ordenar los resultados", nullable=true, strict=true)
     * @QueryParam(name="_sort_order", requirements="(asc|desc)", allowBlank=false, default="desc", description="Dirección para ordenar", nullable=true, strict=true)
     *
     * @param ParamFetcher $paramFetcher

     * @Route("/api/gettickets/{configurationId}", name="gettickets")
     * @Method({"GET", "POST"})
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna las peticiones que cumplan con los filtros requeridos", resource=true,
     * requirements={
     *      {
     *          "name"="configurationId",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Id de la empresa."
     *      }
     * },
     *  output={
     *   "class"   = "WhatsappBundle\Model\TicketListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Bad request",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getTicketsAction(ParamFetcher $paramFetcher, $configurationId) {
        $filters = array();
        $page = null;
        $limit = null;
        $filterIdType = null;
        $filterIdValue = null;
//        $filter = $paramFetcher->get('filter');
        $filters = $this->getFiltersByParamFetcher($paramFetcher);
        $page = $paramFetcher->get('_page');
        $limit = $paramFetcher->get('_per_page');
        $sortBy = $paramFetcher->get('_sort_by');
        $sortOrder = $paramFetcher->get('_sort_order');
        $outOfRange = $paramFetcher->get('_out_of_range');
        $totalsend = array();
        $em = $this->getDoctrine()->getManager();
        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
        if ($configuration != null)
            $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsFiltered($page, $limit, $sortBy, $sortOrder, $outOfRange, $configuration, $filters);
        else
            $tickets = array();

        $response = new TicketListResponse();
        foreach ($tickets as $key => $value) {
//            dump($value);die;
            $result = new TicketApiResponse();
            $result->id = $value->getId();
            $result->name = $value->getName();
            $result->startDate = $value->getStartDate();
            if ($value->getDayOfWeek($value->getStartDate()))
                $result->weekDay = $value->SpanishDate($value->getDayOfWeek($value->getStartDate()));
            else
                $result->weekDay = null;
            $result->resolutionDate = $value->getResolutionDate();
            $result->timeAnswer = $value->getMinutesAnswerTime();
            $result->solvedBySupportMember = $value->getSolvedBySupportMember();
            $result->ticketType = $value->getTicketType();
            $result->endDate = $value->getEndDate();
//            $i["tiempo_de_solucion"] = $value->getMinutesSolutionTime();
            $result->ticketAnswered = $value->getFirstanswer();
            $result->alertAnswerSended = $value->getSendalert();
            $result->alertSolutionSended = $value->getSendalertSolution();
            $result->ticketEnded = $value->getTicketended();
            $result->whatsappGroup = $value->getWhatsappGroup();
            $result->noRegisterTicket = $value->getNofollow();
            $totalsend[] = $result;
        }
        if ($configuration != null)
            $response->total = $em->getRepository('WhatsappBundle:Ticket')->ticketsFilteredCount($page, $limit, $sortBy, $sortOrder, $outOfRange, $configuration, $filters);
        else
            $response->total = 0;
        $response->tickets = $totalsend;

        $view = $this->view($response);
//                dump($view);die;
        return $view;
    }

    /**
     * Método que lista los mensajes a partir de varios filtros opcionales con configuraciones avanzadas. Cada filtro posee como mínimo dos parámetros, el tipo y el valor. Existen filtros de tipo between y notbetween que permiten dos valores para establecer un rango. En caso de que se omita el tipo pero se especifica un valor se toma por defecto los tipos like en los campos de tipo string y eq en el resto de los casos.<br/> 
     * Descripción de requerimientos para tipos de filtros y las operaciones que permiten: <br/> eq: es igual a<br/> neq: no es igua a<br/> lt: menor que<br/> lte: menor o igual que<br/> gt: mayor que<br/> gte: mayor o igual que<br/> isempty: está vacío<br/> isnotempty: no está vacío<br/> isnull: es nulo<br/> isnottull: no es nulo<br/> like: contiene el elemento<br/> notlike: no contiene el elemento<br/> between: Está entre los valores definidos por start y end<br/> notbetween: No está entre los valores definidos por start y end.<br/>
     * El resultado puede ser paginado y ordenado a partir de los campos de la entidad Ticket (id, supportFirstAnswer, isValidationKeyword, ticket, whatsappGroup, supportMember, clientMember, strmenssagetext, dtmmessage)
     * 
     * @QueryParam(name="filter_id_type", requirements="(eq|neq|lt|lte|gt|gte|between|notbetween)", description="Tipo de filtro para el campo Id. ", nullable=true, strict=true)
     * @QueryParam(name="filter_id_value", requirements="\d+", description="Valor de filtro para el campo Id.", nullable=true, strict=true)
     * @QueryParam(name="filter_supportFirstAnswer_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Primera respuesta.", nullable=true, strict=true)
     * @QueryParam(name="filter_supportFirstAnswer_value", requirements="(true|false)", description="Valor de filtro para el campo Primera respuesta. El campo contiene true si el mensaje fue de tipo primera respuesta, false en caso contrario.", nullable=true, strict=true)
     * @QueryParam(name="filter_isValidationKeyword_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Mensaje de tipo validación.", nullable=true, strict=true)
     * @QueryParam(name="filter_isValidationKeyword_value", requirements="(true|false)", description="Valor de filtro para el campo Mensaje de tipo validación. El campo contiene true si el mensaje fue de tipo validación, false en caso contrario.", nullable=true, strict=true)
     * @QueryParam(name="filter_ticket_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_ticket_value", requirements="\d+", description="Id que representa la relación con la petición.", nullable=true, strict=true)
     * @QueryParam(name="filter_whatsappGroup_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Grupo de whatsapp.", nullable=true, strict=true)
     * @QueryParam(name="filter_whatsappGroup_value", requirements="\d+", description="Id que representa la relación con el grupo de whatsapp.", nullable=true, strict=true)
     * @QueryParam(name="filter_supportMember_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Miembro de soporte.", nullable=true, strict=true)
     * @QueryParam(name="filter_supportMember_value", requirements="\d+", description="Id que representa la relación con el miembro de soporte que escribió el mensaje.", nullable=true, strict=true)
     * @QueryParam(name="filter_clientMember_type", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Miembro cliente.", nullable=true, strict=true)
     * @QueryParam(name="filter_clientMember_value", requirements="\d+", description="Id que representa la relación con el cliente que escribió el mensaje.", nullable=true, strict=true)
     * 
     * @QueryParam(name="filter_strmenssagetext_type", requirements="(eq|neq|isempty|isnotempty|isnull|isnottull|like|notlike)", description="Tipo de filtro para el campo Mensaje.", nullable=true, strict=true)
     * @QueryParam(name="filter_strmenssagetext_value", requirements=".*", description="Valor de filtro para el campo Mensaje.", nullable=true, strict=true)

     * @QueryParam(name="filter_dtmmessage_type", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Fecha.", nullable=true, strict=true)
     * @QueryParam(name="filter_dtmmessage_value", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Fecha del mensaje.", nullable=true, strict=true)
     * @QueryParam(name="filter_dtmmessage_value_start", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Minutos de primera respuesta. Válido en las opciones type=between|notbetween. Inicio del rango.", nullable=true, strict=true)
     * @QueryParam(name="filter_dtmmessage_value_end", requirements="\d?\d-\d?\d-\d{4}\s\d\d?\:\d\d?\:\d\d?", description="Valor de filtro para el campo Minutos de primera respuesta. . Válido en las opciones type=between|notbetween. Fin del rango.", nullable=true, strict=true)
     * 
     * @QueryParam(name="_page", requirements="\d+", default=1, strict=true, description="Página desde la cual se traen los resultados.", nullable=true, strict=true)
     * @QueryParam(name="_per_page", requirements="\d+", default=10, strict=false, description="Cantidad de elementos por página.", nullable=true, strict=true)
     * @QueryParam(name="_sort_by", requirements="(id|supportFirstAnswer|isValidationKeyword|ticket|whatsappGroup|supportMember|clientMember|strmenssagetext|dtmmessage)", allowBlank=false, default="id", description="Campo por el que se desea ordenar los resultados", nullable=true, strict=true)
     * @QueryParam(name="_sort_order", requirements="(asc|desc)", allowBlank=false, default="desc", description="Dirección para ordenar", nullable=true, strict=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @Route("/api/getmessages", name="getmessages")
     * @Method({"GET", "POST"})
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna los mensajes que cumplan con los filtros requeridos", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\MessageListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Bad request",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getMessagesAction(ParamFetcher $paramFetcher) {
        $filters = array();
        $page = null;
        $limit = null;
        $filterIdType = null;
        $filterIdValue = null;
//        $filter = $paramFetcher->get('filter');
        $filters = $this->getFiltersMessageByParamFetcher($paramFetcher);
        $page = $paramFetcher->get('_page');
        $limit = $paramFetcher->get('_per_page');
        $sortBy = $paramFetcher->get('_sort_by');
        $sortOrder = $paramFetcher->get('_sort_order');
        $totalsend = array();
        $em = $this->getDoctrine()->getManager();
        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find(1);
        if ($configuration != null)
            $messages = $em->getRepository('WhatsappBundle:Message')->messagesFiltered($page, $limit, $sortBy, $sortOrder, $configuration, $filters);
        else
            $messages = array();

        $response = new MessageListResponse();
        foreach ($messages as $key => $value) {
//            dump($value);die;
            $result = new MessageApiResponse();
            $result->id = $value->getId();
            if ($value->getClientMember())
                $result->clientMember = $value->getClientMember()->getId();
            $result->dtmmessage = $value->getDtmmessage();
            $result->isValidationKeyword = $value->getIsValidationKeyword();
            $result->strmenssagetext = $value->getStrmenssagetext();
            $result->supportFirstAnswer = $value->getSupportFirstAnswer();
            if ($value->getSupportMember())
                $result->supportMember = $value->getSupportMember()->getId();
            if ($value->getTicket())
                $result->ticket = $value->getTicket()->getId();
            if ($value->getWhatsappGroup())
                $result->whatsappGroup = $value->getWhatsappGroup()->getId();
            $totalsend[] = $result;
        }
        if ($configuration != null)
            $response->total = $em->getRepository('WhatsappBundle:Message')->messagesFilteredCount($page, $limit, $sortBy, $sortOrder, $configuration, $filters);
        else
            $response->total = 0;
        $response->messages = $totalsend;

        $view = $this->view($response);
//                dump($view);die;
        return $view;
    }

    private function getDefaultDataType($key) {
        $results = array(
            "id" => "eq",
            "name" => "like",
            "weekday" => "eq",
            "whatsappGroup" => "eq",
            "solvedBySupportMember" => "eq",
            "ticketType" => "eq",
            "solutionType" => "eq",
            "firstanswer" => "eq",
            "ticketended" => "eq",
            "sendalert" => "eq",
            "sendalertSolution" => "eq",
            "nofollow" => "eq",
            "isValidated" => "eq",
            "minutesAnswerTime" => "eq",
            "minutesSolutionTime" => "eq",
            "minutesDevTime" => "eq",
            "minutesValidationWaitTime" => "eq",
            "startDate" => "eq",
            "endDate" => "eq",
            "startTime" => "eq",
//            "id" => "eq","id" => "eq","id" => "eq","id" => "eq","id" => "eq","id" => "eq",
        );
        return $results[$key];
    }

    private function getMessagesDefaultDataType($key) {
        $results = array(
            "id" => "eq",
            "supportFirstAnswer" => "eq",
            "isValidationKeyword" => "eq",
            "ticket" => "eq",
            "whatsappGroup" => "eq",
            "supportMember" => "eq",
            "clientMember" => "eq",
            "strmenssagetext" => "like",
            "dtmmessage" => "eq",
        );
        return $results[$key];
    }

    private function isValidFilter($key) {
        $results = array("id", "name", "startDate", "whatsappGroup", "solvedBySupportMember", "ticketType", "solutionType", "endDate", "firstanswer", "ticketended", "minutesAnswerTime", "minutesSolutionTime", "startTime", "nofollow", "minutesDevTime", "minutesValidationWaitTime", "weekday"
//            "id" => "eq","id" => "eq","id" => "eq","id" => "eq","id" => "eq","id" => "eq",
        );
        foreach ($results as $value) {
            if ($key == $value)
                return true;
        }
        return false;
    }

    //    
//    /**
//     *  /**
//     * 
//     * @QueryParam(array=true, name="filter")
//     * @QueryParam(name="filter[id][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|like|notlike|between|notbetween)", description="Tipo de filtro para el campo Id.", strict=true)
//     * @QueryParam(name="filter[id][value]", requirements="\d+", description="Valor de filtro para el campo Id.")
//     * @QueryParam(name="filter[name][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|like|notlike)", description="Tipo de filtro para el campo Nombre. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento")
//     * @QueryParam(name="filter[name][value]", requirements="\d+", description="Valor de filtro para el campo Nombre.")
//     * @QueryParam(name="filter[weekday][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|like|notlike)", description="Tipo de filtro para el campo Día de la semana. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento")
//     * @QueryParam(name="filter[weekday][value]", requirements="\d+", description="Valor de filtro para el campo Nombre.")
//     * @QueryParam(name="filter[whatsappGroup][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Día de la semana. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[whatsappGroup][value]", requirements="\d+", description="Id que representa la relación con grupos de whatsapp.")
//     * @QueryParam(name="filter[solvedBySupportMember][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Miembro de soporte que resolvión la petición. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[solvedBySupportMember][value]", requirements="\d+", description="Id que representa la relación con miembro del grupo de soporte que dio solución a la petición.")
//     * @QueryParam(name="filter[ticketType][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Tipo de petición. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[ticketType][value]", requirements="\d+", description="Id que representa el tipo de petición asociado a la petición.")
//     * @QueryParam(name="filter[solutionType][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Tipo de solución. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[solutionType][value]", requirements="\d+", description="Id que representa el tipo de solución asociado a la petición.")
//     * @QueryParam(name="filter[firstanswer][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Primera respuesta. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[firstanswer][value]", requirements="(true|false)", description="Valor de filtro para el campo Primera respuesta. El campo contiene true si la petición fue respondida por un miembro de soporte, false en caso contrario")
//     * @QueryParam(name="filter[ticketended][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición finalizada. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[ticketended][value]", requirements="(true|false)", description="Valor de filtro para el campo petición terminada. El campo contiene true si la petición fue cerrada, false en caso contrario.")
//     * @QueryParam(name="filter[sendalert][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Alerta de tipo respuesta enviada. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[sendalert][value]", requirements="(true|false)", description="Valor de filtro para el campo Alerta de tipo respuesta enviada. El campo contiene true si se envió una alerta de tipo respuesta asociada a la petición.")
//     * @QueryParam(name="filter[sendalertSolution][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Alerta de tipo solución enviada. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[sendalertSolution][value]", requirements="(true|false)", description="Valor de filtro para el campo Alerta de tipo solución enviada. El campo contiene true si se envió una alerta de tipo solución asociada a la petición.")
//     * @QueryParam(name="filter[nofollow][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición de tipo aviso.")
//     * @QueryParam(name="filter[nofollow][value]", requirements="(true|false)", description="Valor de filtro para el campo Petición de tipo aviso. El campo contiene true si la petición es marcada como de tipo aviso.")
//     * @QueryParam(name="filter[isValidated][type]", requirements="(eq|neq|isnull|isnottull)", description="Tipo de filtro para el campo Petición validada. posibles valores eq: es igual a, neq:no es igua a, isnull: es nulo, isnottull: no es nulo")
//     * @QueryParam(name="filter[isValidated][value]", requirements="(true|false)", description="Valor de filtro para el campo Petición validada. El campo contiene true si la petición ha sido validada.")
//     *
//     * @QueryParam(name="filter[minutesAnswerTime][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de primera respuesta. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[minutesAnswerTime][value]", requirements="\d+", description="Valor de filtro para el campo Minutos de primera respuesta.")
//     * @QueryParam(name="filter[minutesAnswerTime][value][start]", requirements="\d+", description="Valor de filtro para el campo Minutos de primera respuesta. Cuando en filter[minutesAnswerTime][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[minutesAnswerTime][value][end]", requirements="\d+", description="Valor de filtro para el campo Minutos de primera respuesta. Cuando en filter[minutesAnswerTime][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[minutesSolutionTime][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de solución. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[minutesSolutionTime][value]", requirements="\d+", description="Valor de filtro para el campo Minutos de solución.")
//     * @QueryParam(name="filter[minutesSolutionTime][value][start]", requirements="\d+", description="Valor de filtro para el campo Minutos de solución. Cuando en filter[minutesSolutionTime][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[minutesSolutionTime][value][end]", requirements="\d+", description="Valor de filtro para el campo Minutos de solución. Cuando en filter[minutesSolutionTime][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[minutesDevTime][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Minutos de desarrollo. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[minutesDevTime][value]", requirements="\d+", description="Valor de filtro para el campo Minutos de desarrollo.")
//     * @QueryParam(name="filter[minutesDevTime][value][start]", requirements="\d+", description="Valor de filtro para el campo Minutos de desarrollo. Cuando en filter[minutesDevTime][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[minutesDevTime][value][end]", requirements="\d+", description="Valor de filtro para el campo Minutos de desarrollo. Cuando en filter[minutesDevTime][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[minutesValidationWaitTime][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Tiempo de espera de validación. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[minutesValidationWaitTime][value]", requirements="\d+", description="Valor de filtro para el campo Tiempo de espera de validación.")
//     * @QueryParam(name="filter[minutesValidationWaitTime][value][start]", requirements="\d+", description="Valor de filtro para el campo Tiempo de espera de validación. Cuando en filter[minutesValidationWaitTime][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[minutesValidationWaitTime][value][end]", requirements="\d+", description="Valor de filtro para el campo Tiempo de espera de validación. Cuando en filter[minutesValidationWaitTime][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[startDate][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Fecha de comienzo de petición. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[startDate][value]", requirements="\d+", description="Valor de filtro para el campo Fecha de comienzo de petición.")
//     * @QueryParam(name="filter[startDate][value][start]", requirements="\d+", description="Valor de filtro para el campo Fecha de comienzo de petición. Cuando en filter[startDate][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[startDate][value][end]", requirements="\d+", description="Valor de filtro para el campo Fecha de comienzo de petición. Cuando en filter[startDate][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[endDate][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Fecha de fin de petición. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[endDate][value]", requirements="\d+", description="Valor de filtro para el campo Fecha de fin de petición.")
//     * @QueryParam(name="filter[endDate][value][start]", requirements="\d+", description="Valor de filtro para el campo Fecha de fin de petición. Cuando en filter[endDate][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[endDate][value][end]", requirements="\d+", description="Valor de filtro para el campo Fecha de fin de petición. Cuando en filter[endDate][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="filter[startTime][type]", requirements="(eq|neq|lt|lte|gt|gte|isempty|isnotempty|isnull|isnottull|between|notbetween)", description="Tipo de filtro para el campo Hora de inicio de petición. posibles valores eq: es igual a, neq:no es igua a, lt: menor que, lte: menor o igual que, gt: mayor que, gte: mayor o igual que, isempty: está vacío, isnotempty: no está vacío, isnull: es nulo, isnottull: no es nulo, like: contiene el elemento, notlike: no contiene el elemento, between: Está entre los valores definidos por start y end, between: No está entre los valores definidos por start y end")
//     * @QueryParam(name="filter[startTime][value]", requirements="\d+", description="Valor de filtro para el campo Hora de inicio de petición.")
//     * @QueryParam(name="filter[startTime][value][start]", requirements="\d+", description="Valor de filtro para el campo Hora de inicio de petición. Cuando en filter[startTime][type] se selecciona alguna de las opciones between|notbetween. Representa el inicio del rango")
//     * @QueryParam(name="filter[startTime][value][end]", requirements="\d+", description="Valor de filtro para el campo Hora de inicio de petición. Cuando en filter[startTime][type] se selecciona alguna de las opciones between|notbetween. Representa el fin del rango")
//     *
//     * @QueryParam(name="_out_of_range", requirements="(true|false)", description="Filtra las peticiones que no cumplen con los parametros configurados (tiempo de respuesta, tiempo de solución). Si está en true retorna solo las peticiones que sobrepasan estos algunos de tiempos configurados.")
//     * @QueryParam(name="_page", requirements="\d+", default=1, strict=true, description="Page from which to start listing objects.")
//     * @QueryParam(name="_per_page", requirements="\d+", default=100, strict=false, description="How many objects to return.")
//     * @QueryParam(name="sort", requirements="(asc|desc)", allowBlank=false, default="asc", description="Sort direction")
//     *
//     * @param ParamFetcher $paramFetcher
//     *
//     * @Route("/api/gettickets", name="gettickets")
//     * @View(
//     *  templateVar="",
//     *  statusCode=null,
//     *  serializerGroups={},
//     *  populateDefaultVars=true,
//     *  serializerEnableMaxDepthChecks=false
//     * )
//    *     
//     * @ApiDoc(
//     *  resource=true,
//     *  description="Retorna las peticiones que cumplan con los filtros requerido. ", resource=true,
//     *  output={
//     *   "class"   = "WhatsappBundle\Model\TicketListResponse",
//     *   "parsers" = {
//     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
//     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
//     *   }
//     * },
//     * statusCodes = {
//     *     200 = "Returned when successful",
//     *     400 = "Returned when unsuccessful"
//     *   },
//     * 
//     * )
//     */
//    
//    public function getTicketsAction(ParamFetcher $paramFetcher) {
//        $filters = array();
//        $page = null;
//        $limit = null;
//        $filterIdType = null;
//        $filterIdValue = null;
//        $filter = $paramFetcher->get('filter');
////        if(array_key_exists("_page", $filter))
////            $page = $filter["_page"];
////        else
//        $page = $paramFetcher->get('_page');
////        if(array_key_exists("_per_page", $filter))
////            $limit = $filter["_per_page"];
////        else
//        $limit = $paramFetcher->get('_per_page');
//        foreach ($filter as $key => $value) {
//            if(!$this->isValidFilter($key)){
////                continue;
//                throw new HttpException(400, "Filter {$key} does not exist in this context");
//            }
//            if(array_key_exists("type", $filter[$key]))
//                $filterIdType = $filter[$key]["type"];
//            if(!$filterIdType){
//                $filterIdType = $this->getDefaultDataType($key);
//                continue;
//            }
//            if(array_key_exists("value", $filter[$key]))
//                $filterIdValue = $filter[$key]["value"];
//                $filters[$key] = array("type"=> $filterIdType, "value"=>$filterIdValue);
//        }
//        dump($filters);die;
////        if(array_key_exists("id", $filter)){
////            if(array_key_exists("type", $filter["id"]))
////                $filterIdType = $filter["id"]["type"];
////            if(!$filterIdType)
////                $filterIdType = "eq";
////            if(array_key_exists("value", $filter["id"]))
////                $filterIdValue = $filter["id"]["value"];
////                $filters["id"] = array("type"=> $filterIdType, "value"=>$filterIdValue);
////        }
////        
////        if(array_key_exists("name", $filter)){
////            if(array_key_exists("type", $filter["name"]))
////                $filterIdType = $filter["name"]["type"];
////            if(!$filterIdType)
////                $filterIdType = "like";
////            if(array_key_exists("value", $filter["name"]))
////                $filterIdValue = $filter["name"]["value"];
////                $filters["name"] = array("type"=> $filterIdType, "value"=>$filterIdValue);
////        }
//            
////        $filterIdValue = $paramFetcher->get('filter[id][value]');
////        $sort = $paramFetcher->get('filter');
//        
////        $page = $paramFetcher->get('page');
//        $totalsend = array();
//        $em = $this->getDoctrine()->getManager();
//        $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsFiltered($page, $limit, $filterIdType, $filterIdValue, $filters);
////        $tickets = array();
//        
//        $response = new TicketListResponse();
//        foreach ($tickets as $key => $value) {
////            dump($value);die;
//           $result = new TicketApiResponse();
//            $result->id = $value->getId();
//            $result->name = $value->getName();
//            $result->startDate = $value->getStartDate();
//            if($value->getDayOfWeek($value->getStartDate()))
//                $result->weekDay = $value->SpanishDate($value->getDayOfWeek($value->getStartDate()));
//            else
//                $result->weekDay = null;
//            $result->resolutionDate = $value->getResolutionDate();
//            $result->timeAnswer = $value->getMinutesAnswerTime();
//            $result->solvedBySupportMember = $value->getSolvedBySupportMember();
//            $result->ticketType = $value->getTicketType();
//            $result->endDate = $value->getEndDate();
////            $i["tiempo_de_solucion"] = $value->getMinutesSolutionTime();
//            $result->ticketAnswered = $value->getFirstanswer();
//            $result->alertAnswerSended = $value->getSendalert();
//            $result->alertSolutionSended = $value->getSendalertSolution();
//            $result->ticketEnded = $value->getTicketended();
//            $result->whatsappGroup = $value->getWhatsappGroup();
//            $result->noRegisterTicket = $value->getNofollow();
//            $totalsend[] = $result;
//        }
//        $response->total = $em->getRepository('WhatsappBundle:Ticket')->ticketsFilteredCount($page, $limit, $filterIdType, $filterIdValue, $filters);
//        $response->tickets = $totalsend;
//
//        $view = $this->view($response);
//        return $view;
//    }

    private function getFiltersByParamFetcher($paramFetcher) {
        $filters = array();
        $filter_type = $paramFetcher->get('filter_id_type');
        $filter_value = $paramFetcher->get('filter_id_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("id");
        if ($filter_type)
            $filters["id"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_name_type');
        $filter_value = $paramFetcher->get('filter_name_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("name");
        if ($filter_type)
            $filters["name"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_weekday_type');
        $filter_value = $paramFetcher->get('filter_weekday_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("weekday");
        if ($filter_type)
            $filters["weekday"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_whatsappGroup_type');
        $filter_value = $paramFetcher->get('filter_whatsappGroup_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("whatsappGroup");
        if ($filter_type)
            $filters["whatsappGroup"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_solvedBySupportMember_type');
        $filter_value = $paramFetcher->get('filter_solvedBySupportMember_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("solvedBySupportMember");
        if ($filter_type)
            $filters["solvedBySupportMember"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_ticketType_type');
        $filter_value = $paramFetcher->get('filter_ticketType_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("ticketType");
        if ($filter_type)
            $filters["ticketType"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_solutionType_type');
        $filter_value = $paramFetcher->get('filter_solutionType_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("solutionType");
        if ($filter_type)
            $filters["solutionType"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_firstanswer_type');
        $filter_value = $paramFetcher->get('filter_firstanswer_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("firstanswer");
        if ($filter_type)
            $filters["firstanswer"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_ticketended_type');
        $filter_value = $paramFetcher->get('filter_ticketended_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("ticketended");
        if ($filter_type)
            $filters["ticketended"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_sendalert_type');
        $filter_value = $paramFetcher->get('filter_sendalert_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("sendalert");
        if ($filter_type)
            $filters["sendalert"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_sendalertSolution_type');
        $filter_value = $paramFetcher->get('filter_sendalertSolution_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("sendalertSolution");
        if ($filter_type)
            $filters["sendalertSolution"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_nofollow_type');
        $filter_value = $paramFetcher->get('filter_nofollow_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("nofollow");
        if ($filter_type)
            $filters["nofollow"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_isValidated_type');
        $filter_value = $paramFetcher->get('filter_isValidated_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("isValidated");
        if ($filter_type)
            $filters["isValidated"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_minutesAnswerTime_type');
        $filter_value = $paramFetcher->get('filter_minutesAnswerTime_value');
        $filter_value_start = $paramFetcher->get('filter_minutesAnswerTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_minutesAnswerTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("minutesAnswerTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["minutesAnswerTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["minutesAnswerTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_minutesSolutionTime_type');
        $filter_value = $paramFetcher->get('filter_minutesSolutionTime_value');
        $filter_value_start = $paramFetcher->get('filter_minutesSolutionTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_minutesSolutionTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("minutesSolutionTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["minutesSolutionTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["minutesSolutionTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_minutesDevTime_type');
        $filter_value = $paramFetcher->get('filter_minutesDevTime_value');
        $filter_value_start = $paramFetcher->get('filter_minutesDevTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_minutesDevTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("minutesDevTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["minutesDevTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["minutesDevTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_minutesDevTime_type');
        $filter_value = $paramFetcher->get('filter_minutesDevTime_value');
        $filter_value_start = $paramFetcher->get('filter_minutesDevTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_minutesDevTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("minutesDevTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["minutesDevTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["minutesDevTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_minutesValidationWaitTime_type');
        $filter_value = $paramFetcher->get('filter_minutesValidationWaitTime_value');
        $filter_value_start = $paramFetcher->get('filter_minutesValidationWaitTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_minutesValidationWaitTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("minutesValidationWaitTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["minutesValidationWaitTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["minutesValidationWaitTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_startDate_type');
        $filter_value = $paramFetcher->get('filter_startDate_value');
        $filter_value_start = $paramFetcher->get('filter_startDate_value_start');
        $filter_value_end = $paramFetcher->get('filter_startDate_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("startDate");
        if ($filter_type)
            if ($filter_value != null)
                $filters["startDate"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["startDate"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_endDate_type');
        $filter_value = $paramFetcher->get('filter_endDate_value');
        $filter_value_start = $paramFetcher->get('filter_endDate_value_start');
        $filter_value_end = $paramFetcher->get('filter_endDate_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("endDate");
        if ($filter_type)
            if ($filter_value != null)
                $filters["endDate"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["endDate"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        $filter_type = $paramFetcher->get('filter_startTime_type');
        $filter_value = $paramFetcher->get('filter_startTime_value');
        $filter_value_start = $paramFetcher->get('filter_startTime_value_start');
        $filter_value_end = $paramFetcher->get('filter_startTime_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getDefaultDataType("startTime");
        if ($filter_type)
            if ($filter_value != null)
                $filters["startTime"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["startTime"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));
        return $filters;
    }

    private function getFiltersMessageByParamFetcher($paramFetcher) {
        $filters = array();
        $filter_type = $paramFetcher->get('filter_id_type');
        $filter_value = $paramFetcher->get('filter_id_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("id");
        if ($filter_type)
            $filters["id"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_supportFirstAnswer_type');
        $filter_value = $paramFetcher->get('filter_supportFirstAnswer_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("supportFirstAnswer");
        if ($filter_type)
            $filters["supportFirstAnswer"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_isValidationKeyword_type');
        $filter_value = $paramFetcher->get('filter_isValidationKeyword_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("isValidationKeyword");
        if ($filter_type)
            $filters["isValidationKeyword"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_ticket_type');
        $filter_value = $paramFetcher->get('filter_ticket_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("ticket");
        if ($filter_type)
            $filters["ticket"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_whatsappGroup_type');
        $filter_value = $paramFetcher->get('filter_whatsappGroup_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("whatsappGroup");
        if ($filter_type)
            $filters["whatsappGroup"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_supportMember_type');
        $filter_value = $paramFetcher->get('filter_supportMember_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("supportMember");
        if ($filter_type)
            $filters["supportMember"] = array("type" => $filter_type, "value" => $filter_value);



        $filter_type = $paramFetcher->get('filter_clientMember_type');
        $filter_value = $paramFetcher->get('filter_clientMember_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("clientMember");
        if ($filter_type)
            $filters["clientMember"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_strmenssagetext_type');
        $filter_value = $paramFetcher->get('filter_strmenssagetext_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("strmenssagetext");
        if ($filter_type)
            $filters["strmenssagetext"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_dtmmessage_type');
        $filter_value = $paramFetcher->get('filter_dtmmessage_value');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("dtmmessage");
        if ($filter_type)
            $filters["dtmmessage"] = array("type" => $filter_type, "value" => $filter_value);

        $filter_type = $paramFetcher->get('filter_dtmmessage_type');
        $filter_value = $paramFetcher->get('filter_dtmmessage_value');
        $filter_value_start = $paramFetcher->get('filter_dtmmessage_value_start');
        $filter_value_end = $paramFetcher->get('filter_dtmmessage_value_end');
        if ($filter_value && !$filter_type)
            $filter_type = $this->getMessagesDefaultDataType("dtmmessage");
        if ($filter_type)
            if ($filter_value != null)
                $filters["dtmmessage"] = array("type" => $filter_type, "value" => $filter_value);
            elseif (($filter_value_start != null || $filter_value_end != null) && ($filter_type == "between" || $filter_type == "notbetween"))
                $filters["dtmmessage"] = array("type" => $filter_type, "value" => array("start" => $filter_value_start, "end" => $filter_value_end));

        return $filters;
    }

    /**
     * * @QueryParam(name="company_suffix", requirements=".*", description="Sufijo de la empresa Nombre.", nullable=true, strict=true)
     * @Route("/api/getsummarybycompany", name="getsummarybycompany")
     * @Method({"GET", "POST"})
     * 
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna un resumen de estado de las peticiones de una empresa", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\CompanySummaryStatusApiResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getSummaryByCompanyAction($paramFetcher) {
        $prefix = $paramFetcher->get('company_suffix');
        $response = new CompanySummaryStatusApiResponse();
        $em = $this->getDoctrine()->getManager();
        $configuration = $em->getRepository('WhatsappBundle:Configuration')->findByPrefix($prefix);
        if (count($configuration) > 0) {
            $configuration = $configuration[0];
            $userTimezone = $configuration->getTimeZone();
            $userTimezone = new \DateTimeZone($userTimezone);
            $configuration_id = $configuration->getId();
            $weekday = $this->getLastWeekday($configuration);
            $hourEndWeek = $this->getHourEndWeek($configuration);
            $response->companyPrefix = $prefix;
            $response->companyId = $configuration_id;
            $response->date = new \DateTime("now");
//            $response->thisMonthTicketsCount = $em->getRepository('WhatsappBundle:Ticket')->cantTicketsThisMonth($configuration_id, $userTimezone);
            $response->thisMonthTicketsCount = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByDates(new \DateTime("-30 days"), new \DateTime("now"), $configuration_id);
//            $response->thisWeekTicketsCount = $em->getRepository('WhatsappBundle:Ticket')->countTicketsThisWeek($configuration_id, $weekday, $hourEndWeek, $userTimezone);
            $response->thisWeekTicketsCount = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByDates(new \DateTime("-7 days"), new \DateTime("now"), $configuration_id);
            $response->todayTicketsCount = $em->getRepository('WhatsappBundle:Ticket')->countTicketsThisDay($configuration_id, $weekday, $hourEndWeek, $userTimezone);
            if ($response->thisWeekTicketsCount > 0) {
                $response->mediaAnswerTimeMinutesWeekAgo = $em->getRepository('WhatsappBundle:Ticket')->sumTicketsAnswerTime(new \DateTime("-7 days"), new \DateTime("now"), $configuration_id) / $response->thisWeekTicketsCount;
                $response->mediaResolutionTimeMinutesWeekAgo = $em->getRepository('WhatsappBundle:Ticket')->sumTicketsSolutionTime(new \DateTime("-7 days"), new \DateTime("now"), $configuration_id) / $response->thisWeekTicketsCount;
            }
        }

//        foreach ($openedTickets as $key => $value) {
//            $result = new OpenTicketApiResponse();
//               $i = array();
//            $result->id = $value->getId();
//            $result->name = $value->getName();
//            $result->startDate = $value->getStartDate();
//            if($value->getDayOfWeek($value->getStartDate()))
//                $result->weekDay = $value->SpanishDate($value->getDayOfWeek($value->getStartDate()));
//            else
//                $result->weekDay = null;
//            $result->resolutionDate = $value->getResolutionDate();
//            $result->timeAnswer = $value->getMinutesAnswerTime();
//            $result->solvedBySupportMember = $value->getSolvedBySupportMember();
//            $result->ticketType = $value->getTicketType();
//            $result->lastMessageDate = $value->getEndDate();
////            $i["tiempo_de_solucion"] = $value->getMinutesSolutionTime();
//            $result->ticketAnswered = $value->getFirstanswer();
//            $response->alertAnswerSended = $value->getSendalert();
//            $response->alertSolutionSended = $value->getSendalertSolution();
////            $i["peticion_finalizada"] = $value->getTicketended();
////            $i["no_registro"] = $value->getTicketended();
//            $totalsend[] = $result;
////            $value["cantprofesionals"] = $this->getDoctrine()->getManager()->getRepository('DirectorioBundle:Profesional')
////                ->getProfesionalesCantByCategory($value["id"]);
////            $value["cantempresas"] = $this->getDoctrine()->getManager()->getRepository('DirectorioBundle:Empresa')
////                ->getEmpresasCantByCategory($value["id"]);
////            $totalsend[] = $value;
//        }
//        $response->total = count($openedTickets);
//        $response->tickets = $totalsend;

        $view = $this->view($response);
        return $view;
    }

    public function getLastWeekday($configuration) {
        if ($configuration->getDayEndWeek())
            return $configuration->getDayEndWeek();
        return "sunday";
    }

    public function getHourEndWeek($configuration) {
        if ($configuration->getHourEndWeek())
            return $configuration->getHourEndWeek();
        return "23:59:59";
    }

}
