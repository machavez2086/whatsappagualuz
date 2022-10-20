<?php

namespace WhatsappBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WhatsappBundle\Form\MessageToTicketFormType;
use WhatsappBundle\Form\FechaRangeType;
use WhatsappBundle\Form\DashboardConfigurationType;
use WhatsappBundle\Entity\Message;
use WhatsappBundle\Entity\Ticket;
use WhatsappBundle\Entity\Contact;

class DefaultController extends Controller {

    /**
     * @Route("/")
     */
    public function indexAction() {
        return $this->redirectToRoute('home');
        return $this->render('WhatsappBundle:Default:home.html.twig');
    }

    /**
     * @Route("/frontend/terms", name="terms" )
     */
    public function TagsCloudAction() {
        $this->get('session')->set("menu", "terms");
        $form = $this->getFormConfiguration(false);
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userTimezone = $this->getTimeZone();
        if($userTimezone)
            $options["usertimezone"] = $userTimezone;
        else
            $options["usertimezone"] = 'America/Mexico_City';
        $form1 = $this->createForm(new FechaRangeType(), null, $options);
//        $inicial = new \DateTime("first day of this month");
        $userTimezone = $this->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $weekday = $this->getLastWeekday();
        $hourEndWeek = $this->getHourEndWeek();
        $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
        
        $ini = clone $inicial;
//        $ini->setTime(0, 0, 0);
//        $finalDay = new \DateTime("now");
//        $finalDay->setTime(23, 59, 59);
        $finalDay = $this->getNextFriday($userTimezone);
        $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
        $modelData = $form1->getData();
        $modelData["fechainicial"] = $ini;
        $modelData["fechafinal"] = $finalDay;
        $form1->setData($modelData);
        if ($request->getMethod() == 'POST') {
            $form1->bind($request);
            if ($form1->isValid()) {
                
                $data = $form1->getData();
                if ($data["fechainicial"]) {
                    $inicial = $data["fechainicial"];
                } else {
                    $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
                }
                $finalDay = clone $ini;
                if ($data["fechainicial"]) {
                    $finalDay = $data["fechafinal"];
                }
                if ($finalDay < $inicial) {
                    $this->setFlash(
                            'sonata_flash_error', 'La fecha inicial no debe ser mayor que la final.'
                    );
                }
                $ini = clone $inicial;
                $dateFirstTicket = $em->getRepository('WhatsappBundle:Ticket')->getFirtTicket($configuration_id);
                if($dateFirstTicket[0]->getStartDate() > $inicial)
                    $ini = clone $dateFirstTicket[0]->getStartDate();
//                $userTimezone = new \DateTimeZone('America/Mexico_City');
                $ini->setTimezone($userTimezone);
            } else {
                $form1 = $this->createForm(new FechaRangeType());
//                $inicial = new \DateTime("first day of this month");
                $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
                $finalDay = new \DateTime("today");
                $modelData = $form1->getData();
                $modelData["fechainicial"] = $inicial;
                $modelData["fechafinal"] = $finalDay;
                $form1->setData($modelData);
            }
        }
//        dump($inicial);
//        dump($finalDay);
        $arrayResultConfiguration = $em->getRepository('WhatsappBundle:TicketRepitlyKeywordGroup')->findByConfigurationByDateRange($configuration_id, $inicial, $finalDay);
        $arrayResultConfigurationResult = array();
        $arrayResultGroupResult = array();
        foreach ($arrayResultConfiguration as $value) {
            $stdObject = new \stdClass();
            $stdObject->word = $value["keyword"];
            $stdObject->weight = $value["total"];
            $arrayResultConfigurationResult[] = $stdObject;
        }
        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration_id);
        $groups = $configuration->getWhatsappGroups();
        foreach ($groups as $group) {
            $arrayResultConfiguration = $em->getRepository('WhatsappBundle:TicketRepitlyKeywordGroup')->findByWhatsappGroupByDateRange($group, $inicial, $finalDay);
            $arrayResultConfigurationResultGroup = array();            
            foreach ($arrayResultConfiguration as $value) {
                $stdObject = new \stdClass();
                $stdObject->word = $value["keyword"];
                $stdObject->weight = (int)intval($value["total"]);
                
                $arrayResultConfigurationResultGroup[] = $stdObject;
            }
            $stdObject = new \stdClass();
            $stdObject->name = $group->getName();
            $stdObject->array = $arrayResultConfigurationResultGroup;
            if(count($arrayResultConfigurationResultGroup) > 0){
                $arrayResultGroupResult[] = $stdObject;
            }
            
        }
        
//        dump($cant);die;
        return $this->render('WhatsappBundle:Default:tagscloud.html.twig', array(
            'form' => $form->createView(),
            'form2' => $form1->createView(),
            'arrayResultConfigurationResult' => $arrayResultConfigurationResult,
            'arrayResultGroupResult' => $arrayResultGroupResult,
            'configuration' => $configuration,
        ));

    }

    /**
     * @Route("/home", name="home" )
     */
    public function homeAction() {
        return $this->render('WhatsappBundle:Default:home.html.twig');
    }

    /**
     * @Route("/admin/synchronize_mail", name="synchronize_mail" )
     */
    public function synchronizeMailAction() {
        $client = new \Buzz\Client\Curl();
        $client->setTimeout(3600);
        $browser = new \Buzz\Browser($client);
        $uri = "http://localhost/sacsprov2/reademail";
//        $packagistResponse = $browser->get($uri);

        $data = array("connetc" => 1, "All" => 0); // Build your payload

        $headers = array(
            'Content-Type' => 'application/json',
                // Add any other header needed by the API
        );

        $packagistResponse = $browser->post($uri, $headers, json_encode($data));
//        $packagistResponse = $browser->get($uri);
        $packages = $packagistResponse->getContent();

//        dump($packages);die;
        $this->addFlash('sonata_flash_success', $packages);
        return $this->redirectToRoute('admin_whatsapp_configuration_list', array());
    }

    /**
     * @Route("/admin/synchronize_mail_all", name="synchronize_mail_all" )
     */
    public function synchronizeMailAllAction() {
        $client = new \Buzz\Client\Curl();
        $client->setTimeout(3600);
        $browser = new \Buzz\Browser($client);
        $uri = "http://localhost/sacsprov2/reademail";
//        $packagistResponse = $browser->get($uri);

        $data = array("connetc" => 1, "All" => 1); // Build your payload

        $headers = array(
            'Content-Type' => 'application/json',
                // Add any other header needed by the API
        );

        $packagistResponse = $browser->post($uri, $headers, json_encode($data));
//        $packagistResponse = $browser->get($uri);
        $packages = $packagistResponse->getContent();

//        dump($packages);die;
        $this->addFlash('sonata_flash_success', $packages);
        return $this->redirectToRoute('admin_whatsapp_configuration_list', array());
    }

    /**
     * @Route("/frontend/dashboard", name="dashboard" )
     */
    public function dashboardAction() {
        $request = $this->getRequest();
        $this->get('session')->set("menu", "dashboard");
        $em = $this->getDoctrine()->getManager();
        $form = $this->getFormConfiguration(true);
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        $userTimezone = $this->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $weekday = $this->getLastWeekday();
        $hourEndWeek = $this->getHourEndWeek();
        $groups = array();
        if($configuration_id)
            $groups = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration_id);
        $minutesSinceLastMessageGroups = array();
        foreach ($groups as $value) {
            $mediaResolutionTime = 0;
            $mediaResponseTime = 0;
            $ticketsByGroupThisDay = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisDayByGroup($value, $configuration_id, $weekday, $hourEndWeek, $userTimezone);
            foreach ($ticketsByGroupThisDay as $ticket) {

                $mediaResolutionTime = $mediaResolutionTime + $ticket->getMinutesSolutionTime();
//                dump($mediaResolutionTime);
                //calcular promedio de tiempo de respuesta de los tickets
                $messageFirstAnswer = $em->getRepository('WhatsappBundle:Message')->findByTicketFirstAnswere($ticket);

                $ahora = new \DateTime("now");
                $since_first_answer = new \DateTime("1700");
                $since_first_answer = $ahora->diff($since_first_answer);
                if (count($messageFirstAnswer) > 0) {
                    if ($messageFirstAnswer[0]) {
                        if ($messageFirstAnswer[0]->getDtmmessage() and $ticket->getStartDate()) {
                            $since_first_answer = $messageFirstAnswer[0]->getDtmmessage()->diff($ticket->getStartDate());
                        }
                    }
                } else {
                    if ($ticket->getEndDate() and $ticket->getStartDate()) {
                        $since_first_answer = $ticket->getEndDate()->diff($ticket->getStartDate());
                    }
                }
                $minutes = 0;
                $minutes = $since_first_answer->days * 24 * 60;
                $minutes += $since_first_answer->h * 60;
                $minutes += $since_first_answer->i;
                $minutes += $since_first_answer->s / 60;
                $mediaResponseTime = $mediaResponseTime + round($minutes, 2);
            }
            if (count($ticketsByGroupThisDay) > 0) {
                $mediaResolutionTime = $mediaResolutionTime / count($ticketsByGroupThisDay);
                $mediaResponseTime = $mediaResponseTime / count($ticketsByGroupThisDay);
            }

            $mediaResolutionTime = round($mediaResolutionTime, 2);

            $mediaResponseTime = round($mediaResponseTime, 2);
//            $ticketsCantByGroup[$value->getName()] = count($ticketsByGroupThisDay);
//            $mediaResolutionTimeByGroup[] = array($value->getName(), $mediaResolutionTime, $mediaResponseTime, $ticketsCantByGroup[$value->getName()]);
            // Sacar el numero de peticiones por cliente
            //Tabla para sacar estadisticas de tickets de la ultima semana
            $mediaResolutionTimeThisWeek = 0;
            $mediaResponseTimeThisWeek = 0;

//            $ticketsByGroup = $value->getTickets();
            $ticketsByGroupThisWeek = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisWeekByGroup($value, $weekday, $hourEndWeek, $userTimezone);
            foreach ($ticketsByGroupThisWeek as $ticket) {
                $mediaResolutionTimeThisWeek = $mediaResolutionTimeThisWeek + round($ticket->getMinutesSolutionTime(), 2);

                //calcular promedio de tiempo de respuesta de los tickets
                $messageFirstAnswer = $em->getRepository('WhatsappBundle:Message')->findByTicketFirstAnswere($ticket);
                if (count($messageFirstAnswer) > 0) {
                    $since_first_answer = $messageFirstAnswer[0]->getDtmmessage()->diff($ticket->getStartDate());
                } else {
                    $since_first_answer = $ticket->getEndDate()->diff($ticket->getStartDate());
                }
                $minutes = 0;
                $minutes = $since_first_answer->days * 24 * 60;
                $minutes += $since_first_answer->h * 60;
                $minutes += $since_first_answer->i;
                $minutes += $since_first_answer->s / 60;
                $mediaResponseTimeThisWeek = $mediaResponseTimeThisWeek + round($minutes, 2);
            }
            if (count($ticketsByGroupThisWeek) > 0) {
                $mediaResolutionTimeThisWeek = $mediaResolutionTimeThisWeek / count($ticketsByGroupThisWeek);
                $mediaResponseTimeThisWeek = $mediaResponseTimeThisWeek / count($ticketsByGroupThisWeek);
            }
            $mediaResolutionTimeThisWeek = round($mediaResolutionTimeThisWeek, 2);
            $mediaResponseTimeThisWeek = round($mediaResponseTimeThisWeek, 2);
//            $ticketsCantByGroupThisWeek[$value->getName()] = count($ticketsByGroupThisWeek);
            // Sacar el numero de peticiones por cliente
            // Sacar cuando fue el ultimo mensaje del grupo
            $lastMessageDate = new \DateTime("1970-01-01");
            $lastMessageFromGroup = $em->getRepository('WhatsappBundle:Message')->lastMessageFromGroup($value);
            if (count($lastMessageFromGroup)) {
                $lastMessageDate = $lastMessageFromGroup[0]->getDtmmessage();
            }
            $maxDate = new \DateTime("now");
            $since_last_message = $maxDate->diff($lastMessageDate);
            $minutesSinceLastMessageByGroup = "";
            if ($since_last_message->days > 0)
                $minutesSinceLastMessageByGroup = $since_last_message->days . " d, ";
            if ($since_last_message->h > 0)
                $minutesSinceLastMessageByGroup = $minutesSinceLastMessageByGroup . $since_last_message->h . " h, ";
            $minutesSinceLastMessageByGroup = $minutesSinceLastMessageByGroup . $since_last_message->i . " min";
            $minutesSinceLastMessageGroups[$value->getName()] = $minutesSinceLastMessageByGroup;
//            $mediaResolutionTimeByGroupThisWeek[] = array($value->getName(), $mediaResolutionTimeThisWeek, $mediaResponseTimeThisWeek, $ticketsCantByGroupThisWeek[$value->getName()], $minutesSinceLastMessageGroups[$value->getName()]);
        }

        // Calcular tiempo promedio de resolucion de la semana en curso
        $ticketsThisWeek = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisWeek($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantTicketsThisWeek = count($ticketsThisWeek);
        $sumTimeResolutionThisWeek = 0;
        $mediaResolutionTimeThisWeek = 0;
        $sumTimeSentimentThisWeek = 0;
        $mediaSentimentThisWeek = 0;
        $sumEvaluationThisWeek = 0;
        $mediaEvaluationThisWeek = -1;
        $cantEvaluationThisWeek = 0;
        foreach ($ticketsThisWeek as $ticket) {
            $sumTimeResolutionThisWeek = $sumTimeResolutionThisWeek + round($ticket->getMinutesSolutionTime(), 2);
            $sumTimeSentimentThisWeek = $sumTimeSentimentThisWeek + round($ticket->getSentimentAsureAllMessages(), 2);
            if($ticket->getSatisfaction() != null){
                $sumEvaluationThisWeek = $sumEvaluationThisWeek+$ticket->getSatisfaction();
                $cantEvaluationThisWeek = $cantEvaluationThisWeek+1;
            }
        }
        if (count($ticketsThisWeek) > 0){
            $mediaResolutionTimeThisWeek = $sumTimeResolutionThisWeek / count($ticketsThisWeek);
            $mediaSentimentThisWeek = $sumTimeSentimentThisWeek / count($ticketsThisWeek);
            
        }
        if ($cantEvaluationThisWeek > 0){
            $mediaEvaluationThisWeek = $sumEvaluationThisWeek / $cantEvaluationThisWeek;
        }
        
        $mediaResolutionTimeThisWeek = round($mediaResolutionTimeThisWeek, 2);
        $mediaSentimentThisWeek = round($mediaSentimentThisWeek, 2);
        $mediaEvaluationThisWeek = round($mediaEvaluationThisWeek, 2);

//        dump(count($ticketsThisWeek));die;
        // Calcular tiempo promedio de resolucion del dia en curso
        $ticketsThisDay = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisDay($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantTicketsThisDay = count($ticketsThisDay);
        $sumTimeResolutionThisDay = 0;
        $mediaResolutionTimeThisDay = 0;
        $sumSentimentThisDay = 0;
        $sumEvaluationThisDay = 0;
        $cantEvaluationThisDay = 0;
        $mediaEvaluationThisDay = -1;
        $mediaSentimentThisDay = 0;
        foreach ($ticketsThisDay as $ticket) {
            $sumTimeResolutionThisDay = $sumTimeResolutionThisDay + round($ticket->getMinutesSolutionTime(), 2);
            $sumSentimentThisDay = $sumSentimentThisDay + round($ticket->getSentimentAsureAllMessages(), 2);
            if($ticket->getSatisfaction() != null){
                $sumEvaluationThisDay = $sumEvaluationThisDay + round($ticket->getSatisfaction(), 2);
                $cantEvaluationThisDay = $cantEvaluationThisDay+1;
            }
        }
        if (count($ticketsThisDay) > 0){
            $mediaResolutionTimeThisDay = $sumTimeResolutionThisDay / count($ticketsThisDay);
            $sumSentimentThisDay = $sumSentimentThisDay / count($ticketsThisDay);
            
        }
        if($cantEvaluationThisDay > 0){
            $mediaEvaluationThisDay = $sumEvaluationThisDay / $cantEvaluationThisDay;
        }
        $mediaResolutionTimeThisDay = round($mediaResolutionTimeThisDay, 2);
        $sumSentimentThisDay = round($sumSentimentThisDay, 2);
        $mediaEvaluationThisDay = round($mediaEvaluationThisDay, 2);

        // Numero de peticiones totales del mes
        $cantTicketsThisMonth = $em->getRepository('WhatsappBundle:Ticket')->cantTicketsThisMonth($configuration_id, $userTimezone);

        // Tiempo transcurrido desde la ultima peticion
        $lastTicket = $em->getRepository('WhatsappBundle:Ticket')->findLastTicket($configuration_id);
        $minutesSinceLastTicket = 0;
        $minutesSinceLastTicket = "";
        if (count($lastTicket) > 0) {
            $maxDate = new \DateTime("now");
            $since_start = $maxDate->diff($lastTicket[0]->getEndDate());
//            echo($since_start->format("%Y years, %m months, %d days,  %H hours, %i minutes, %s seconds"));die;
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s / 60;
//            $minutesSinceLastTicket = $minutes;
            if ($since_start->days > 0)
                $minutesSinceLastTicket = $since_start->days . " d, ";
            if ($since_start->h > 0)
                $minutesSinceLastTicket = $minutesSinceLastTicket . $since_start->h . " h, ";
            $minutesSinceLastTicket = $minutesSinceLastTicket . $since_start->i . " min";
//            dump($since_start);
//            dump($minutesSinceLastTicket);die;
        }

//        if ($minutesSinceLastTicket > )
        // Calcular tiempo promedio de respuesta en la semana en curso
        $sumTimeAnswerMinutes = 0;
        $sumTimeDevelopMinutes = 0;
        $sumTimeValidationMinutes = 0;
        $validationCount = 0;
        foreach ($ticketsThisWeek as $ticket) {
            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicketOrderDt($ticket);
//            dump($messages);die;
            if (count($messages) == 0)
                continue;
            $countMessages = 0;
            $firstMessageDt = $messages[0]->getDtmmessage();
            $firstAnswer = null;
            $isFounded = false;
            foreach ($messages as $message) {
                $text = $message->getStrmenssagetext();
                $keywordsFirstAnswer = array();
                if($configuration_id)
                    $keywordsFirstAnswer = $em->getRepository('WhatsappBundle:FirstAnswerKeyword')->findByConfiguration($configuration_id);
                foreach ($keywordsFirstAnswer as $keyword) {
                    if (strpos($text, $keyword->getKeyword()) !== false) {
                        $firstAnswer = $message->getDtmmessage();
//                        dump($message);
                        $isFounded = true;
                        break;
                    }
                }
                if ($isFounded) {
                    break;
                }
                if ($message->getSupportMember() != null) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
//                else if($countMessages >= 5){
//                    $firstAnswer = $message->getDtmmessage();
//                    break;
//                }

                if ($message->getSupportFirstAnswer()) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }

                if ($countMessages == count($messages) - 1) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
                $countMessages = $countMessages + 1;
            }

            $since_start = $firstAnswer->diff($firstMessageDt);
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s / 60;
//            dump($since_start);
//            dump($firstMessageDt);die;
//            $sumTimeAnswerMinutes = $sumTimeAnswerMinutes+$minutes;
            $sumTimeAnswerMinutes = $sumTimeAnswerMinutes + $ticket->getMinutesAnswerTime();
            $sumTimeDevelopMinutes = $sumTimeDevelopMinutes + $ticket->getMinutesDevTime();
            $sumTimeValidationMinutes = $sumTimeValidationMinutes + $ticket->getMinutesValidationWaitTime();
            if ($ticket->getMinutesValidationWaitTime() > 0) {
                $validationCount = $validationCount + 1;
            }
        }
        $mediaAnswerTimeThisWeek = 0;
        $mediaDevelopTimeThisWeek = 0;
        $mediaValidationTimeThisWeek = 0;
        if (count($ticketsThisWeek) > 0) {
            $mediaAnswerTimeThisWeek = $sumTimeAnswerMinutes / count($ticketsThisWeek);
            $mediaDevelopTimeThisWeek = $sumTimeDevelopMinutes / count($ticketsThisWeek);
        }
        if ($validationCount > 0) {
            $mediaValidationTimeThisWeek = $sumTimeValidationMinutes / $validationCount;
        }
//        $mediaAnswerTimeThisWeek = intval($mediaAnswerTimeThisWeek);
        $mediaAnswerTimeThisWeek = round($mediaAnswerTimeThisWeek, 2);
        $mediaDevelopTimeThisWeek = round($mediaDevelopTimeThisWeek, 2);
        $mediaValidationTimeThisWeek = round($mediaValidationTimeThisWeek, 2);
        // Calcular tiempo promedio de respuesta en el dia en curso
        $sumTimeAnswerMinutesThisDay = 0;
        $sumTimeDevelopMinutesThisDay = 0;
        $sumTimeValidationMinutesThisDay = 0;
        $countTimeValidationMinutesThisDay = 0;
        foreach ($ticketsThisDay as $ticket) {
            //modificar el tiempo promedio de respuesta cuando ya tiene vinculo con los miembros de soporte
            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicketOrderDt($ticket);
            if (count($messages) == 0)
                continue;
            $countMessages = 0;
            $firstMessageDt = $messages[0]->getDtmmessage();
            $firstAnswer = new \DateTime();

            $isFounded = false;
            foreach ($messages as $message) {
                $text = $message->getStrmenssagetext();
                $keywordsFirstAnswer = array();
                if($configuration_id)
                    $keywordsFirstAnswer = $em->getRepository('WhatsappBundle:FirstAnswerKeyword')->findByConfiguration($configuration_id);
                foreach ($keywordsFirstAnswer as $keyword) {
                    if (strpos($text, $keyword->getKeyword()) !== false) {
                        $firstAnswer = $message->getDtmmessage();
                        $isFounded = true;
                        break;
                    }
                }
                if ($isFounded) {
                    break;
                }
                if ($message->getSupportMember() != null) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
                if ($message->getSupportFirstAnswer()) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
//                if($countMessages >= 5){
//                    $firstAnswer = $message->getDtmmessage();
//                    break;
//                }

                if ($countMessages == count($messages) - 1) {
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
                $countMessages = $countMessages + 1;
            }

            $since_start = $firstAnswer->diff($firstMessageDt);

            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s / 60;
//            $sumTimeAnswerMinutesThisDay = $sumTimeAnswerMinutesThisDay+$minutes;
            $sumTimeAnswerMinutesThisDay = $sumTimeAnswerMinutesThisDay + $ticket->getMinutesAnswerTime();
            $sumTimeDevelopMinutesThisDay = $sumTimeDevelopMinutesThisDay + $ticket->getMinutesDevTime();
            $sumTimeValidationMinutesThisDay = $sumTimeValidationMinutesThisDay + $ticket->getMinutesValidationWaitTime();
            if ($ticket->getMinutesValidationWaitTime() > 0) {
                $countTimeValidationMinutesThisDay = $countTimeValidationMinutesThisDay + 1;
            }
        }
        $mediaAnswerTimeThisDay = 0;
        $mediaDevelopTimeThisDay = 0;
        $mediaValidationTimeThisDay = 0;
        if (count($ticketsThisDay) > 0) {
            $mediaAnswerTimeThisDay = $sumTimeAnswerMinutesThisDay / count($ticketsThisDay);
            $mediaDevelopTimeThisDay = $sumTimeDevelopMinutesThisDay / count($ticketsThisDay);
        }
        if ($countTimeValidationMinutesThisDay > 0) {
            $mediaValidationTimeThisDay = $sumTimeValidationMinutesThisDay / $countTimeValidationMinutesThisDay;
        }
//        $mediaAnswerTimeThisDay = intval($mediaAnswerTimeThisDay);
        $mediaAnswerTimeThisDay = round($mediaAnswerTimeThisDay, 2);
        $mediaDevelopTimeThisDay = round($mediaDevelopTimeThisDay, 2);
        $mediaValidationTimeThisDay = round($mediaValidationTimeThisDay, 2);
        //        Peticiones por cliente del ultimo mes, peticiones por cliente de la ultima semana y peticiones por clientes del ultimo dia
        // Obtener tiempo promedio de resolucion por semanas
        // Obtener los grupos


        $cantTicketsOpened = $em->getRepository('WhatsappBundle:Ticket')->cantTicketsOpeneds($configuration_id);
        $openedTickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsOpeneds($configuration_id);
//        dump($cantTicketsOpened);die;
//        dump($minutesSinceLastMessageGroups);die;
//        $alerts = $em->getRepository('WhatsappBundle:Alert')->findAll();
        //sacar alertas de tipo respuesta
        $totalAlertasAnswer = $em->getRepository('WhatsappBundle:Alert')->countTotalAlertsAnswer($configuration_id);
        $cantAlertTodayAnswer = $em->getRepository('WhatsappBundle:Alert')->cantAlertTodayAnswer($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantAlertThisWeekAnswer = $em->getRepository('WhatsappBundle:Alert')->cantAlertThisWeekAnswer($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantAlertThisMonthAnswer = $em->getRepository('WhatsappBundle:Alert')->cantAlertThisMonthAnswer($configuration_id, $userTimezone);
        $cantAlertOpenAnswer = $em->getRepository('WhatsappBundle:Alert')->cantAlertOpenAnswer($configuration_id);
        $alertsAnswer = array("totalAlertas" => $totalAlertasAnswer, "cantAlertToday" => $cantAlertTodayAnswer, "cantAlertThisWeek" => $cantAlertThisWeekAnswer, "cantAlertThisMonth" => $cantAlertThisMonthAnswer, "cantAlertOpen" => $cantAlertOpenAnswer);

        //sacar alertas de tipo respuesta
        $totalAlertasSolution = $em->getRepository('WhatsappBundle:Alert')->countTotalAlertsSolution($configuration_id);
        $cantAlertTodaySolution = $em->getRepository('WhatsappBundle:Alert')->cantAlertTodaySolution($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantAlertThisWeekSolution = $em->getRepository('WhatsappBundle:Alert')->cantAlertThisWeekSolution($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $cantAlertThisMonthSolution = $em->getRepository('WhatsappBundle:Alert')->cantAlertThisMonthSolution($configuration_id, $userTimezone);
        $cantAlertOpenSolution = $em->getRepository('WhatsappBundle:Alert')->cantAlertOpenSolution($configuration_id);
        $alertsSolution = array("totalAlertas" => $totalAlertasSolution, "cantAlertToday" => $cantAlertTodaySolution, "cantAlertThisWeek" => $cantAlertThisWeekSolution, "cantAlertThisMonth" => $cantAlertThisMonthSolution, "cantAlertOpen" => $cantAlertOpenSolution);
//        usort($mediaResolutionTimeByGroup, array($this, "customCompare"));
//        usort($mediaResolutionTimeByGroupThisWeek, array($this, "customCompare"));
        if($configuration_id)
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration_id);
        return $this->render('WhatsappBundle:Default:dashboard.html.twig', array(
//                    'mediaResolutionTimeByGroup' => $mediaResolutionTimeByGroup,
//                    'ticketsCantByGroup' => $ticketsCantByGroup,
//                    'mediaResolutionTimeByGroupThisWeek' => $mediaResolutionTimeByGroupThisWeek,
//                    'ticketsCantByGroupThisWeek' => $ticketsCantByGroupThisWeek,
                    'mediaResolutionTimeThisWeek' => $mediaResolutionTimeThisWeek,
                    'mediaSentimentThisWeek' => $mediaSentimentThisWeek,
                    'mediaEvaluationThisWeek' => $mediaEvaluationThisWeek,
                    'cantEvaluationThisWeek' => $cantEvaluationThisWeek,
                    'mediaResolutionTimeThisDay' => $mediaResolutionTimeThisDay,
                    'sumSentimentThisDay' => $sumSentimentThisDay,
//                    'minutesSinceLastMessageGroups' => $minutesSinceLastMessageGroups,
                    'cantTicketsThisWeek' => $cantTicketsThisWeek,
                    'cantTicketsThisDay' => $cantTicketsThisDay,
                    'cantTicketsThisMonth' => $cantTicketsThisMonth,
                    'minutesSinceLastTicket' => $minutesSinceLastTicket,
                    'mediaAnswerTimeThisWeek' => $mediaAnswerTimeThisWeek,
                    'mediaAnswerTimeThisDay' => $mediaAnswerTimeThisDay,
                    'mediaDevelopTimeThisDay' => $mediaDevelopTimeThisDay,
                    'mediaValidationTimeThisDay' => $mediaValidationTimeThisDay,
                    'mediaDevelopTimeThisWeek' => $mediaDevelopTimeThisWeek,
                    'mediaValidationTimeThisWeek' => $mediaValidationTimeThisWeek,
                    'cantTicketsOpened' => $cantTicketsOpened,
                    'openedTickets' => $openedTickets,
                    'alertsAnswer' => $alertsAnswer,
                    'alertsSolution' => $alertsSolution,
//                    'statusPhoneConected' => $configuration->getStatusPhoneConected(),
                    'form' => $form->createView(),
                    'userTimezone' => $this->getTimeZone(),
                    'mediaEvaluationThisDay' => $mediaEvaluationThisDay,
                    'cantEvaluationThisDay' => $cantEvaluationThisDay,
            )
        );
    }

    /**
     * @Route("/frontend/report_peticion", name="report_peticion" )
     */
    public function reportPeticionAction() {
        $this->get('session')->set("menu", "report_peticion");
        $form = $this->getFormConfiguration(false);
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        $em = $this->getDoctrine()->getManager();
        $groups = array();
        if($configuration_id)
            $groups = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration_id);
        $userTimezone = $this->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $weekday = $this->getLastWeekday();
        $hourEndWeek = $this->getHourEndWeek();
        
        $mediaResolutionTimeByGroup = array();
        $ticketsCantByGroup = array();
        $minutesSinceLastMessageGroups = array();
        $mediaResolutionTimeByGroupThisWeek = array();
        $ticketsCantByGroupThisWeek = array();
        foreach ($groups as $value) {
            // Sacar el tiempo promedio de peticiones por cliente
            $mediaResolutionTime = 0;
            $mediaResponseTime = 0;
            $mediaDevelopTime = 0;
            $mediaValidationTime = 0;
            $mediaSentiment = 0;
            $mediaEvaluation = 0;
            $cantEvaluation = 0;
            $countValidationTime = 0;
            
//            $ticketsByGroup = $value->getTickets();
            
            $ticketsByGroupThisDay = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisDayByGroup($value, $configuration_id, $weekday, $hourEndWeek, $userTimezone);
            foreach ($ticketsByGroupThisDay as $ticket) {
                
                $mediaResponseTime = $mediaResponseTime+$ticket->getMinutesAnswerTime();
                $mediaResolutionTime = $mediaResolutionTime+$ticket->getMinutesSolutionTime();
                $mediaDevelopTime = $mediaDevelopTime+$ticket->getMinutesDevTime();
                $mediaValidationTime = $mediaValidationTime+$ticket->getMinutesValidationWaitTime();
                $mediaSentiment = $mediaSentiment+$ticket->getSentimentAsureAllMessages();
                if($ticket->getSatisfaction() != null){
                    $mediaEvaluation = $mediaEvaluation+$ticket->getSatisfaction();
                    $cantEvaluation = $cantEvaluation+1;
                }
                if($ticket->getMinutesValidationWaitTime() > 0){
                    $countValidationTime = $countValidationTime+1;
                }
//                dump($mediaResolutionTime);
                
                //calcular promedio de tiempo de respuesta de los tickets
                $messageFirstAnswer = $em->getRepository('WhatsappBundle:Message')->findByTicketFirstAnswere($ticket);
                
                $ahora = new \DateTime("now");
                $since_first_answer = new \DateTime("1700");
                $since_first_answer = $ahora->diff($since_first_answer);
                if(count($messageFirstAnswer) > 0){
                    if($messageFirstAnswer[0]){
                        if($messageFirstAnswer[0]->getDtmmessage() and $ticket->getStartDate()){
                        $since_first_answer = $messageFirstAnswer[0]->getDtmmessage()->diff($ticket->getStartDate());
                        }
                    }
                }
                else{
                    if($ticket->getEndDate() and $ticket->getStartDate()){
                        $since_first_answer = $ticket->getEndDate()->diff($ticket->getStartDate());
                    }
                }

            }
            if(count($ticketsByGroupThisDay) > 0){
                $mediaResolutionTime = $mediaResolutionTime/count($ticketsByGroupThisDay);
                $mediaResponseTime = $mediaResponseTime/count($ticketsByGroupThisDay);
                $mediaDevelopTime = $mediaDevelopTime/count($ticketsByGroupThisDay);
                $mediaSentiment = $mediaSentiment/count($ticketsByGroupThisDay);
            }
            
            if($countValidationTime > 0){
                $mediaValidationTime = $mediaValidationTime/$countValidationTime;
            }
            
            if($cantEvaluation > 0){                
                $mediaEvaluation = $mediaEvaluation/$cantEvaluation;
            }
            else{
                $mediaEvaluation = -1;
            }
            $mediaResolutionTime = round($mediaResolutionTime,2);
            $mediaDevelopTime = round($mediaDevelopTime,2);
            $mediaValidationTime = round($mediaValidationTime,2);
            $mediaEvaluation = round($mediaEvaluation,2);
            
            $mediaResponseTime = round($mediaResponseTime,2);
            $mediaSentiment = round($mediaSentiment,2);
            $ticketsCantByGroup[$value->getName()] = count($ticketsByGroupThisDay);
            $mediaResolutionTimeByGroup[] = array($value->getName(), $mediaResponseTime, $mediaDevelopTime, $mediaValidationTime, $mediaResolutionTime, $ticketsCantByGroup[$value->getName()], $mediaSentiment, $mediaEvaluation, $cantEvaluation);
            
            // Sacar el numero de peticiones por cliente
            //Tabla para sacar estadisticas de tickets de la ultima semana
            $mediaResolutionTimeThisWeek = 0;
            $mediaResponseTimeThisWeek = 0;
            $mediaDevelopTimeThisWeek = 0;
            $mediaSentimentThisWeek = 0;
            $mediaEvaluationThisWeek = 0;
            $cantEvaluationThisWeek = 0;
            $mediaValidationTimeThisWeek = 0;
            $countValidationTimeThisWeek = 0;
            
            //$ticketsByGroup = $value->getTickets();
            
            $ticketsByGroupThisWeek = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisWeekByGroup($value, $weekday, $hourEndWeek, $userTimezone);
            foreach ($ticketsByGroupThisWeek as $ticket) {
                $mediaResolutionTimeThisWeek = $mediaResolutionTimeThisWeek+round($ticket->getMinutesSolutionTime(),2);
                $mediaResponseTimeThisWeek = $mediaResponseTimeThisWeek+round($ticket->getMinutesAnswerTime(),2);
                $mediaDevelopTimeThisWeek = $mediaDevelopTimeThisWeek+round($ticket->getMinutesDevTime(),2);
                $mediaSentimentThisWeek = $mediaSentimentThisWeek+round($ticket->getSentimentAsureAllMessages(),2);
                $mediaValidationTimeThisWeek = $mediaValidationTimeThisWeek+round($ticket->getMinutesValidationWaitTime(),2);
                if($ticket->getMinutesValidationWaitTime() > 0){
                    $countValidationTimeThisWeek = $countValidationTimeThisWeek+1;
                }
                if($ticket->getSatisfaction() != null){
                    $mediaEvaluationThisWeek = $mediaEvaluationThisWeek+$ticket->getSatisfaction();
                    $cantEvaluationThisWeek = $cantEvaluationThisWeek+1;
                }
                
                //calcular promedio de tiempo de respuesta de los tickets
                $messageFirstAnswer = $em->getRepository('WhatsappBundle:Message')->findByTicketFirstAnswere($ticket);
                if(count($messageFirstAnswer) > 0){
                    $since_first_answer = $messageFirstAnswer[0]->getDtmmessage()->diff($ticket->getStartDate());
                }
                else{
                    $since_first_answer = $ticket->getEndDate()->diff($ticket->getStartDate());
                }
//                $minutes = 0;
//                $minutes = $since_first_answer->days * 24 * 60;
//                $minutes += $since_first_answer->h * 60;
//                $minutes += $since_first_answer->i;
//                $minutes += $since_first_answer->s/60;
//                $mediaResponseTimeThisWeek = $mediaResponseTimeThisWeek + round($minutes,2);
            }
            if(count($ticketsByGroupThisWeek) > 0){
                $mediaResolutionTimeThisWeek = $mediaResolutionTimeThisWeek/count($ticketsByGroupThisWeek);
                $mediaResponseTimeThisWeek = $mediaResponseTimeThisWeek/count($ticketsByGroupThisWeek);
                $mediaDevelopTimeThisWeek = $mediaDevelopTimeThisWeek/count($ticketsByGroupThisWeek);
                $mediaSentimentThisWeek = $mediaSentimentThisWeek/count($ticketsByGroupThisWeek);
                
                
            }
                if($cantEvaluationThisWeek > 0){
                    
                    $mediaEvaluationThisWeek = $mediaEvaluationThisWeek/$cantEvaluationThisWeek;
                }
                else{
                    
                    $mediaEvaluationThisWeek = -1;
                }
            if($countValidationTimeThisWeek > 0){
                $mediaValidationTimeThisWeek = $mediaValidationTimeThisWeek/$countValidationTimeThisWeek;
            }
            
            $mediaResolutionTimeThisWeek = round($mediaResolutionTimeThisWeek,2);
            $mediaResponseTimeThisWeek = round($mediaResponseTimeThisWeek,2);
            $mediaDevelopTimeThisWeek = round($mediaDevelopTimeThisWeek,2);
            $mediaSentimentThisWeek = round($mediaSentimentThisWeek,2);
            $mediaValidationTimeThisWeek = round($mediaValidationTimeThisWeek,2);
            $mediaEvaluationThisWeek = round($mediaEvaluationThisWeek,2);
            $ticketsCantByGroupThisWeek[$value->getName()] = count($ticketsByGroupThisWeek);
            // Sacar cuando fue el ultimo mensaje del grupo
            $lastMessageDate = new \DateTime("1970-01-01");
            $lastMessageFromGroup = $em->getRepository('WhatsappBundle:Message')->lastMessageFromGroup($value);
            if(count($lastMessageFromGroup)){
                $lastMessageDate = $lastMessageFromGroup[0]->getDtmmessage();
            }
            $maxDate = new \DateTime("now");
            $since_last_message = $maxDate->diff($lastMessageDate);
            $minutesSinceLastMessageByGroup = "";
            if ($since_last_message->days > 0)
                $minutesSinceLastMessageByGroup = $since_last_message->days." d, ";
            if($since_last_message->h > 0)
                $minutesSinceLastMessageByGroup = $minutesSinceLastMessageByGroup.$since_last_message->h." h, ";
            $minutesSinceLastMessageByGroup = $minutesSinceLastMessageByGroup.$since_last_message->i." min";
            $minutesSinceLastMessageGroups[$value->getName()] = $minutesSinceLastMessageByGroup;
            $mediaResolutionTimeByGroupThisWeek[] = array($value->getName(), $mediaResponseTimeThisWeek, $mediaResponseTimeThisWeek, $mediaValidationTimeThisWeek, $mediaResolutionTimeThisWeek, $ticketsCantByGroupThisWeek[$value->getName()], $minutesSinceLastMessageGroups[$value->getName()], $mediaSentimentThisWeek, $mediaEvaluationThisWeek, $cantEvaluationThisWeek);
        }
        
        // Calcular tiempo promedio de resolucion de la semana en curso
        
        $ticketsThisWeek = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisWeek($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        
        $sumTimeResolutionThisWeek = 0;
        foreach ($ticketsThisWeek as $ticket) {
            $sumTimeResolutionThisWeek = $sumTimeResolutionThisWeek+round($ticket->getMinutesSolutionTime(),2);
        }
        
        
        // Calcular tiempo promedio de resolucion del dia en curso
        
        $ticketsThisDay = $em->getRepository('WhatsappBundle:Ticket')->findTicketsThisDay($configuration_id, $weekday, $hourEndWeek, $userTimezone);
        $sumTimeResolutionThisDay = 0;
        foreach ($ticketsThisDay as $ticket) {
            $sumTimeResolutionThisDay = $sumTimeResolutionThisDay+round($ticket->getMinutesSolutionTime(),2);
        }
       
        
        // Calcular tiempo promedio de respuesta en el dia en curso
        $sumTimeAnswerMinutesThisDay = 0;
        foreach ($ticketsThisDay as $ticket) {
            //modificar el tiempo promedio de respuesta cuando ya tiene vinculo con los miembros de soporte
            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicketOrderDt($ticket);
            if(count($messages) == 0)
                continue;
            $countMessages = 0;
            $firstMessageDt = $messages[0]->getDtmmessage();
            $firstAnswer = new \DateTime();
            
            $isFounded = false;
            foreach ($messages as $message) {
                $text = $message->getStrmenssagetext();
                $keywordsFirstAnswer = array();
                if($configuration_id)
                    $keywordsFirstAnswer = $em->getRepository('WhatsappBundle:FirstAnswerKeyword')->findByConfiguration($configuration_id);
                foreach ($keywordsFirstAnswer as $keyword) {
                    if(strpos($text, $keyword->getKeyword())!== false){
                        $firstAnswer = $message->getDtmmessage();
                        $isFounded = true;
                        break;
                    }
                }
                if($isFounded){
                    break;
                }
                if($message->getSupportMember() != null){
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
                if($message->getSupportFirstAnswer()){
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
//                if($countMessages >= 5){
//                    $firstAnswer = $message->getDtmmessage();
//                    break;
//                }
                
                if($countMessages == count($messages)-1){
                    $firstAnswer = $message->getDtmmessage();
                    break;
                }
                $countMessages = $countMessages+1;
            }

            $since_start = $firstAnswer->diff($firstMessageDt);

            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;
            $minutes += $since_start->s/60;
            $sumTimeAnswerMinutesThisDay = $sumTimeAnswerMinutesThisDay+$minutes;
        }
       
        //        Peticiones por cliente del ultimo mes, peticiones por cliente de la ultima semana y peticiones por clientes del ultimo dia
        // Obtener tiempo promedio de resolucion por semanas
        // Obtener los grupos
        $groups = array();
        if($configuration_id)
            $groups = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration_id);
        // Peticiones por cliente del ultimo mes
        $ticketsByGroupsLastMonths = array();
        
        foreach ($groups as $group) {
            $cant = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByGroupsLastMonths($group, $userTimezone);
            if($cant > 0){
                $stdObject = new \stdClass();
                $stdObject->name = $group->getName();
                $stdObject->data = array(round($cant,2));
                $ticketsByGroupsLastMonths[] = $stdObject;
            }
        }
        
        // Peticiones por cliente del ultima semana
        $ticketsByGroupsLastWeek = array();
        
        foreach ($groups as $group) {
            $cant = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByGroupsLastWeek($group, $weekday, $hourEndWeek, $userTimezone);
            if($cant > 0){
                $stdObject = new \stdClass();
                $stdObject->name = $group->getName();
                $stdObject->data = array(round($cant,2));
                $ticketsByGroupsLastWeek[] = $stdObject;
            }
        }
        
        // Peticiones por cliente de hoy
        $ticketsByGroupsToday = array();
        
        foreach ($groups as $group) {
            $cant = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByGroupsToday($group, $weekday, $hourEndWeek, $userTimezone);
            if($cant > 0){
                
                $stdObject = new \stdClass();
                $stdObject->name = $group->getName();
                $stdObject->data = array(round($cant,2));
                $ticketsByGroupsToday[] = $stdObject;
            }
        }
        if(count($mediaResolutionTimeByGroup) > 0){
            usort($mediaResolutionTimeByGroup, array($this, "customCompare"));
        }
        if(count($mediaResolutionTimeByGroupThisWeek) > 0){
            usort($mediaResolutionTimeByGroupThisWeek, array($this, "customCompare"));
        }
//        $dailyTicketsCountLastWeek = $this->getDailyTicketsCountLastWeek($em);
//        $dailyMediaAnswerTimeLastWeek = $this->getDailyAnswwerLastWeek($em, $dailyTicketsCountLastWeek->data);
//        dump($ticketsByGroupsLastMonths);die;
//        $dailyMediaSolutionTimeLastWeek = $this->getDailySolutionLastWeek($em, $dailyTicketsCountLastWeek->data);
        return $this->render('WhatsappBundle:Default:report_peticion.html.twig',
                array(
                    'mediaResolutionTimeByGroup' => $mediaResolutionTimeByGroup,
                    'ticketsCantByGroup' => $ticketsCantByGroup,
                    'mediaResolutionTimeByGroupThisWeek' => $mediaResolutionTimeByGroupThisWeek,
                    'ticketsCantByGroupThisWeek' => $ticketsCantByGroupThisWeek,
                    'minutesSinceLastMessageGroups' => $minutesSinceLastMessageGroups,
                    'ticketsByGroupsLastMonths' => $ticketsByGroupsLastMonths,
                    'ticketsByGroupsLastWeek' => $ticketsByGroupsLastWeek,
                    'ticketsByGroupsToday' => $ticketsByGroupsToday,
                    'form' => $form->createView(),
                )
                );
    }

    /**
     * @Route("/frontend/report_by_date_range", name="report_by_date_range" )
     */
    public function reportByDateRangeAction() {
        $this->get('session')->set("menu", "report_by_date_range");
        $form = $this->getFormConfiguration(false);
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $userTimezone = $this->getTimeZone();
        if($userTimezone)
            $options["usertimezone"] = $userTimezone;
        else
            $options["usertimezone"] = 'America/Mexico_City';
        $form1 = $this->createForm(new FechaRangeType(), null, $options);
//        $inicial = new \DateTime("first day of this month");
        $userTimezone = $this->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $weekday = $this->getLastWeekday();
        $hourEndWeek = $this->getHourEndWeek();
        $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
        
        $ini = clone $inicial;
//        $ini->setTime(0, 0, 0);
//        $finalDay = new \DateTime("now");
//        $finalDay->setTime(23, 59, 59);
        $finalDay = $this->getNextFriday($userTimezone);
        $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
        $modelData = $form1->getData();
        $modelData["fechainicial"] = $ini;
        $modelData["fechafinal"] = $finalDay;
        $form1->setData($modelData);
        if ($request->getMethod() == 'POST') {
            $form1->bind($request);
            if ($form1->isValid()) {
                
                $data = $form1->getData();
                if ($data["fechainicial"]) {
                    $inicial = $data["fechainicial"];
                } else {
                    $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
                }
                $finalDay = clone $ini;
                if ($data["fechainicial"]) {
                    $finalDay = $data["fechafinal"];
                }
                if ($finalDay < $inicial) {
                    $this->setFlash(
                            'sonata_flash_error', 'La fecha inicial no debe ser mayor que la final.'
                    );
                }
                $ini = clone $inicial;
                $dateFirstTicket = $em->getRepository('WhatsappBundle:Ticket')->getFirtTicket($configuration_id);
                if($dateFirstTicket[0]->getStartDate() > $inicial)
                    $ini = clone $dateFirstTicket[0]->getStartDate();
//                $userTimezone = new \DateTimeZone('America/Mexico_City');
                $ini->setTimezone($userTimezone);
            } else {
                $form1 = $this->createForm(new FechaRangeType());
//                $inicial = new \DateTime("first day of this month");
                $inicial = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);
                $finalDay = new \DateTime("today");
                $modelData = $form1->getData();
                $modelData["fechainicial"] = $inicial;
                $modelData["fechafinal"] = $finalDay;
                $form1->setData($modelData);
            }
        }
//        dump($finalDay);die;
        $ini->setTimezone($userTimezone);
        
//        $fin->setTimezone($utc);
        
        $insidenciasPorDia = array();        
        $insidenciasPorDiaGraphic = array();
        $respuestasPorDiaGraphic = array();
        $developPorDiaGraphic = array();
        $validationPorDiaGraphic = array();
        $resolucionPorDiaGraphic = array();
        $sentimentPorDiaGraphic = array();
        $insidenciasPorDiaGraphicCategories = array();
        $totalPromedioinsidenciasPorDia = new \stdClass();
        $sumaInsidenciasTotal = 0;
        $promedioAnswerTotal = 0;
        $promedioSentimentTotal = 0;
        $promedioSentimentClientTotal = 0;
        $promedioSentimentSupportTotal = 0;
        $promedioResponseTotal = 0;
        $promedioDevelopTotal = 0;
        $promedioValidationTotal = 0;
        $promedioEvaluationTotal = 0;
        $cantEvaluationTotal = 0;
        $cantidadDias = 0;
        
        
        $insidenciasPorHoras = array(
            "00 horas" => 0, "01 horas" => 0, "02 horas" => 0, "03 horas" => 0, "04 horas" => 0, "05 horas" => 0, "06 horas" => 0,
            "07 horas" => 0, "08 horas" => 0, "09 horas" => 0, "10 horas" => 0, "11 horas" => 0, "12 horas" => 0, "13 horas" => 0,
            "14 horas" => 0, "15 horas" => 0, "16 horas" => 0, "17 horas" => 0, "18 horas" => 0, "19 horas" => 0, "20 horas" => 0,
            "21 horas" => 0, "22 horas" => 0, "23 horas" => 0 
            );        
        
//        dump($ini);
//        die;
        while ($ini < $finalDay) {            
            $final = clone $ini;
            $final->setTime(23, 59, 59);
            if ($final > $finalDay){
                $final = $finalDay;
            }
            $utc = new \DateTimeZone("UTC");
            $ini->setTimezone($utc);
            $final->setTimezone($utc);
            $sumaInsidencias = 0;
            $sumaAnswer = 0;
            $sumaSentiment = 0;
            $sumaEvaluation = 0;
            $cantEvaluation = 0;
            $sumaSentimentClient = 0;
            $sumaSentimentSupport = 0;
            $sumaResolution = 0;
            $sumaDevelop = 0;
            $sumaValidation = 0;
            $countValidation = 0;
//            die;
            $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsByDates($ini, $final, $configuration_id);
//            dump(count($tickets));die;
            $sumaInsidencias = count($tickets);
            foreach ($tickets as $ticket) {
                if($ticket->getStartTime()){
                    $startTime = $ticket->getStartTime();
                    $startTime->setTimezone($userTimezone);
                    $hora = $startTime->format("H");
                    $dateIniArray = explode(":",$hora);
//                    dump($dateIniArray);die;
                    $datetimeIni = new \DateTime("today", $userTimezone);
                    $user = $this->get('security.token_storage')->getToken()->getUser();
                    
//                    $userTimezone = new \DateTimeZone("UTC");
//                    $datetimeIni->setTimezone($userTimezone);
                    $datetimeIni->setTime($dateIniArray[0], 30, 0);
                    $datetimeIni->setTimezone($userTimezone);
                    $ini1 = $datetimeIni->format("H");
                    
                    $index = $ini1." horas";
                    if(!array_key_exists($index, $insidenciasPorHoras))
                            $insidenciasPorHoras[$index] = 0;
                    $insidenciasPorHoras[$index] = $insidenciasPorHoras[$index]+1;
                }
                $answer = floatval($ticket->getMinutesAnswerTime());
                $resolution = floatval($ticket->getMinutesSolutionTime());
                $develop = floatval($ticket->getMinutesDevTime());
                $validation = floatval($ticket->getMinutesValidationWaitTime());
                $sumaAnswer = $sumaAnswer + $answer;
                $sumaSentiment = $sumaSentiment + floatval($ticket->getSentimentAsureAllMessages());
//                $sumaSentimentClient = $sumaSentimentClient + floatval($ticket->getSentimentAsureClientMessages());
//                $sumaSentimentSupport = $sumaSentimentSupport + floatval($ticket->getSentimentasureSupportMessages());
                $sumaResolution = $sumaResolution + $resolution;
                $sumaDevelop = $sumaDevelop + $develop;
                $sumaValidation = $sumaValidation + $validation;
                if($validation > 0)
                    $countValidation = $countValidation+1;
                if($ticket->getSatisfaction() != null){
                    $sumaEvaluation = $sumaEvaluation+$ticket->getSatisfaction();
                    $cantEvaluation = $cantEvaluation+1;
                }
            }
            
            $dayWeek = $this->getDayOfWeek($ini);
            $dayWeek = $this->SpanishDate($dayWeek) . " " . strval(intval($ini->format('d')));
            
            
            $promedioAnswer = 0;
            $promedioSentiment = 0;
            $promedioEvaluation = 0;
//            $promedioSentimentClient = 0;
            $promedioSentimentSupport = 0;
            $promedioResolution = 0;
            $promedioDevelop = 0;
            $promedioValidation = 0;
            if($sumaInsidencias > 0){
                $promedioAnswer = round($sumaAnswer/$sumaInsidencias,2);            
                $promedioSentiment = round($sumaSentiment/$sumaInsidencias,2);        
                
//                $promedioSentimentClient = round($sumaSentimentClient/$sumaInsidencias,2);            
//                $promedioSentimentSupport = round($sumaSentimentSupport/$sumaInsidencias,2);            
                $promedioResolution = round($sumaResolution/$sumaInsidencias,2);
                $promedioDevelop = round($sumaDevelop/$sumaInsidencias,2);
            }
            if($cantEvaluation > 0){
                $promedioEvaluation = round($sumaEvaluation/$cantEvaluation,2);   
                $promedioEvaluationTotal = $promedioEvaluationTotal+$promedioEvaluation;
                $cantEvaluationTotal = $cantEvaluationTotal+1;
            }
            if($countValidation > 0)
                $promedioValidation = round($sumaValidation/$countValidation,2);
                
            $stdObject = new \stdClass();
            $stdObject->dayWeek = $dayWeek;
            $stdObject->tickets = $sumaInsidencias;
            $stdObject->answer = $promedioAnswer;
            $stdObject->resolution = $promedioResolution;
            $stdObject->develop = $promedioDevelop;
            $stdObject->validation = $promedioValidation;
            $stdObject->sentiment = $promedioSentiment;
            $stdObject->evaluation = $promedioEvaluation;
            $stdObject->cantEvaluation = $cantEvaluation;
//            $stdObject->sentimentClient = $promedioSentimentClient;
//            $stdObject->sentimentSupport = $promedioSentimentSupport;
            $insidenciasPorDia[] = $stdObject;
//            $stdObject = new \stdClass();
//            $stdObject->name = $dayWeek;
//            $stdObject->data = ;
            $insidenciasPorDiaGraphic[] = round($sumaInsidencias,2);
            $respuestasPorDiaGraphic[] = round($promedioAnswer,2);
            $developPorDiaGraphic[] = round($promedioDevelop,2);
            $validationPorDiaGraphic[] = round($promedioValidation,2);
            $resolucionPorDiaGraphic[] = round($promedioResolution,2);
            $sentimentPorDiaGraphic[] = round($promedioSentiment,2);
            $insidenciasPorDiaGraphicCategories [] = $dayWeek;
            $ini->setTimezone($userTimezone);
        
            $ini->modify("+1 day");
            $ini->setTime(0, 0, 0);
//            $utc = new \DateTimeZone("UTC");
//            $ini->setTimezone($utc);
            if($sumaInsidencias > 0){
            $sumaInsidenciasTotal = $sumaInsidenciasTotal+$sumaInsidencias;
            $promedioAnswerTotal = $promedioAnswerTotal+$promedioAnswer;
            $promedioSentimentTotal = $promedioSentimentTotal+$promedioSentiment;
//            $promedioSentimentClientTotal = $promedioSentimentClientTotal+$promedioSentimentClient;
//            $promedioSentimentSupportTotal = $promedioSentimentSupportTotal+$promedioSentimentSupport;
            $promedioResponseTotal = $promedioResponseTotal+$promedioResolution;
            $promedioDevelopTotal = $promedioDevelopTotal+$promedioDevelop;
            $promedioValidationTotal = $promedioValidationTotal+$promedioValidation;
            $cantidadDias = $cantidadDias+1;
            }
                                    
        }
//        dump($insidenciasPorHoras);
//        ;die;
        $totalPromedioinsidenciasPorDia->totalInsidencias = $sumaInsidenciasTotal;
        if($cantidadDias > 0){
            $totalPromedioinsidenciasPorDia->promedioRespuesta = round($promedioAnswerTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorDia->promedioSentiment = round($promedioSentimentTotal/$cantidadDias,2);
//            $totalPromedioinsidenciasPorDia->promedioSentimentClient = round($promedioSentimentClientTotal/$cantidadDias,2);
//            $totalPromedioinsidenciasPorDia->promedioSentimentSupport = round($promedioSentimentSupportTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorDia->promedioResolucion = round($promedioResponseTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorDia->promedioDevelop = round($promedioDevelopTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorDia->promedioValidation = round($promedioValidationTotal/$cantidadDias,2);
        }
        else{
            $totalPromedioinsidenciasPorDia->promedioRespuesta = 0;
            $totalPromedioinsidenciasPorDia->promedioSentiment = 0;
//            $totalPromedioinsidenciasPorDia->promedioSentimentClient = 0;
//            $totalPromedioinsidenciasPorDia->promedioSentimentSupport = 0;
            $totalPromedioinsidenciasPorDia->promedioResolucion = 0;
            $totalPromedioinsidenciasPorDia->promedioDevelop = 0;
            $totalPromedioinsidenciasPorDia->promedioValidation = 0;
        }
        if($cantEvaluationTotal > 0){
            $totalPromedioinsidenciasPorDia->promedioEvaluation = round($promedioEvaluationTotal/$cantEvaluationTotal,2);
            $totalPromedioinsidenciasPorDia->cantEvaluation = $cantEvaluationTotal;
        }
        else{
            $totalPromedioinsidenciasPorDia->promedioEvaluation = -1;
        }
        $stdObject = new \stdClass();
        $stdObject->name = "Incidencias";
        $stdObject->data = $insidenciasPorDiaGraphic;
        $insidenciasPorDiaGraphic = array($stdObject);
        
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de respuesta";
        $stdObject->data = $respuestasPorDiaGraphic;
        $respuestasPorDiaGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de desarrollo";
        $stdObject->data = $developPorDiaGraphic;
        $developPorDiaGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de validación";
        $stdObject->data = $validationPorDiaGraphic;
        $validationPorDiaGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de resolución";
        $stdObject->data = $resolucionPorDiaGraphic;
        $resolucionPorDiaGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Sentimiento promedio";
        $stdObject->data = $sentimentPorDiaGraphic;
        $sentimentPorDiaGraphic = array($stdObject);
        
        
        //Segunda tabla insidencias por cliente
        $groups = array();
        if($configuration_id)
            $groups = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration_id);
        $insidenciasPorGrupo = array();
        $insidenciasPorGrupoGraphic = array();
        $respuestasPorGrupoGraphic = array();
        $developPorGrupoGraphic = array();
        $validationPorGrupoGraphic = array();
        $resolucionPorGrupoGraphic = array();
        $sentimentPorGrupoGraphic = array();
        $insidenciasPorGrupoGraphicCategories = array();
        $totalPromedioinsidenciasPorGrupos = new \stdClass();
        $sumaInsidenciasTotal = 0;
        $promedioAnswerTotal = 0;
        $promedioSentimentTotal = 0;
        $promedioResponseTotal = 0;
        $promedioDevelopTotal = 0;
        $promedioValidationTotal = 0;
        $promedioEvaluationTotal = 0;
        $cantEvaluationTotal = 0;
        
        $cantidadDias = 0;
        $sumaInsidencias = 0;
        
        foreach ($groups as $group) {
            $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsByDatesByGroup($inicial, $finalDay, $group);
            
            $sumaInsidencias = count($tickets);
            
            $sumaAnswer = 0;
            $sumaSentiment = 0;
//            $sumaSentimentClient = 0;
//            $sumaSentimentSupport = 0;
            $sumaResolution = 0;
            $sumaDevelop = 0;
            $sumaValidation = 0;
            $sumaEvaluation = 0;
            $cantEvaluation = 0;
            $coutValidationTotal = 0;
            foreach ($tickets as $ticket) {
                $answer = floatval($ticket->getMinutesAnswerTime());
                $resolution = floatval($ticket->getMinutesSolutionTime());
                $develop = floatval($ticket->getMinutesDevTime());
                $validation = floatval($ticket->getMinutesValidationWaitTime());
                $sumaAnswer = $sumaAnswer + $answer;
                $sumaSentiment = $sumaSentiment + floatval($ticket->getSentimentAsureAllMessages());
//                $sumaSentimentClient = $sumaSentimentClient + floatval($ticket->getSentimentAsureClientMessages());
//                $sumaSentimentSupport = $sumaSentimentSupport + floatval($ticket->getSentimentasureSupportMessages());
                $sumaResolution = $sumaResolution + $resolution;
                $sumaDevelop = $sumaDevelop + $develop;
                $sumaValidation = $sumaValidation + $validation;
                if($validation>0){
                    $coutValidationTotal = $coutValidationTotal+1;
                }
                if($ticket->getSatisfaction() != null){
                    $sumaEvaluation = $sumaEvaluation+$ticket->getSatisfaction();
                    $cantEvaluation = $cantEvaluation+1;
                }
            }
            $dayWeek = $this->getDayOfWeek($ini);
            $dayWeek = $this->SpanishDate($dayWeek) . " " . strval(intval($ini->format('d')));
            
            
            $promedioAnswer = 0;
            $promedioSentiment = 0;
//            $promedioSentimentClient = 0;
            $promedioSentimentSupport = 0;
            $promedioResolution = 0;
            $promedioDevelop = 0;
            $promedioValidation = 0;
            $promedioEvaluation = 0;
            if($sumaInsidencias > 0){
                $promedioAnswer = round($sumaAnswer/$sumaInsidencias,2);
                $promedioSentiment = round($sumaSentiment/$sumaInsidencias,2);
//                $promedioSentimentClient = round($sumaSentimentClient/$sumaInsidencias,2);
//                $promedioSentimentSupport = round($sumaSentimentSupport/$sumaInsidencias,2);
                $promedioResolution = round($sumaResolution/$sumaInsidencias,2);
                $promedioDevelop = round($sumaDevelop/$sumaInsidencias,2);
            }
            if($cantEvaluation > 0){
                $promedioEvaluation = round($sumaEvaluation/$cantEvaluation,2);
            }
            if($coutValidationTotal > 0)
                $promedioValidation = round($sumaValidation/$coutValidationTotal,2);
            
            
            $stdObject = new \stdClass();
            $stdObject->client = $group->getName();
            $stdObject->tickets = $sumaInsidencias;
            $stdObject->answer = $promedioAnswer;
            $stdObject->sentiment = $promedioSentiment;
//            $stdObject->sentimentClient = $promedioSentimentClient;
//            $stdObject->sentimentSupport = $promedioSentimentSupport;
            $stdObject->resolution = $promedioResolution;
            $stdObject->develop = $promedioDevelop;
            $stdObject->validation = $promedioValidation;
            $stdObject->evaluation = $promedioEvaluation;
            $stdObject->cantEvaluation = $cantEvaluation;
            $insidenciasPorGrupo[] = $stdObject;
            
            $insidenciasPorGrupoGraphic[] = round($sumaInsidencias,2);
            $respuestasPorGrupoGraphic[] = round($promedioAnswer,2);
            $developPorGrupoGraphic[] = round($promedioDevelop,2);
            $validationPorGrupoGraphic[] = round($promedioValidation,2);
            $resolucionPorGrupoGraphic[] = round($promedioResolution,2);
            $sentimentPorGrupoGraphic[] = round($promedioSentiment,2);
            $insidenciasPorGrupoGraphicCategories [] = $group->getName();
            
            if($sumaInsidencias > 0){
                $sumaInsidenciasTotal = $sumaInsidenciasTotal+$sumaInsidencias;
                $promedioAnswerTotal = $promedioAnswerTotal+$promedioAnswer;
                $promedioSentimentTotal = $promedioSentimentTotal+$promedioSentiment;
//                $promedioSentimentClientTotal = $promedioSentimentClientTotal+$promedioSentimentClient;
//                $promedioSentimentSupportTotal = $promedioSentimentSupportTotal+$promedioSentimentSupport;
                $promedioResponseTotal = $promedioResponseTotal+$promedioResolution;
                $promedioDevelopTotal = $promedioDevelopTotal+$promedioDevelop;
                $promedioValidationTotal = $promedioValidationTotal+$promedioValidation;
                $cantidadDias = $cantidadDias+1;
            }
            if($cantEvaluation > 0){
                $promedioEvaluationTotal = $promedioEvaluationTotal+$sumaEvaluation;
                $cantEvaluationTotal = $cantEvaluationTotal+$cantEvaluation;
            }
        }
        $totalPromedioinsidenciasPorGrupos->totalInsidencias = $sumaInsidenciasTotal;
        if($cantidadDias > 0){
        $totalPromedioinsidenciasPorGrupos->promedioRespuesta = round($promedioAnswerTotal/$cantidadDias,2);
        $totalPromedioinsidenciasPorGrupos->promedioSentiment = round($promedioSentimentTotal/$cantidadDias,2);
//        $totalPromedioinsidenciasPorGrupos->promedioSentimentClient = round($promedioSentimentClientTotal/$cantidadDias,2);
//        $totalPromedioinsidenciasPorGrupos->promedioSentimentSupport = round($promedioSentimentSupportTotal/$cantidadDias,2);
        $totalPromedioinsidenciasPorGrupos->promedioResolucion = round($promedioResponseTotal/$cantidadDias,2);
        $totalPromedioinsidenciasPorGrupos->promedioDevelop = round($promedioDevelopTotal/$cantidadDias,2);
        $totalPromedioinsidenciasPorGrupos->promedioValidation = round($promedioValidationTotal/$cantidadDias,2);
        }
        else{
            $totalPromedioinsidenciasPorGrupos->promedioRespuesta = 0;
            $totalPromedioinsidenciasPorGrupos->promedioResolucion = 0;
            $totalPromedioinsidenciasPorGrupos->promedioSentiment = 0;
//            $totalPromedioinsidenciasPorGrupos->promedioSentimentClient = 0;
//            $totalPromedioinsidenciasPorGrupos->promedioSentimentSupport = 0;
            $totalPromedioinsidenciasPorGrupos->promedioDevelop = 0;
            $totalPromedioinsidenciasPorGrupos->promedioValidation = 0;
        }
        
        if($cantEvaluationTotal > 0){
            $totalPromedioinsidenciasPorGrupos->promedioEvaluation = round($promedioEvaluationTotal/$cantEvaluationTotal,2);
            $totalPromedioinsidenciasPorGrupos->cantEvaluation = $cantEvaluationTotal;
            
        }
        else{
            $totalPromedioinsidenciasPorGrupos->promedioEvaluation = -1;
        }
        
        $stdObject = new \stdClass();
        $stdObject->name = "Incidencias";
        $stdObject->data = $insidenciasPorGrupoGraphic;
        $insidenciasPorGrupoGraphic = array($stdObject);
        
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de respuesta";
        $stdObject->data = $respuestasPorGrupoGraphic;
        $respuestasPorGrupoGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de desarrollo";
        $stdObject->data = $developPorGrupoGraphic;
        $developPorGrupoGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de validación";
        $stdObject->data = $validationPorGrupoGraphic;
        $validationPorGrupoGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de  resolución";
        $stdObject->data = $resolucionPorGrupoGraphic;
        $resolucionPorGrupoGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Análisis de Sentimiento";
        $stdObject->data = $sentimentPorGrupoGraphic;
        $sentimentPorGrupoGraphic = array($stdObject);
        
        //Segunda tabla
        //recursos
        $supportMembers = array();
        if($configuration_id)
            $supportMembers = $em->getRepository('WhatsappBundle:SupportMember')->findByConfiguration($configuration_id);
        $insidenciasPorRecursos = array();
        $insidenciasPorRecursosGraphic = array();
        $validacionesPorRecursosGraphic = array();
        $respuestasPorRecursosGraphic = array();
        $developPorRecursosGraphic = array();
        $validationPorRecursosGraphic = array();
        $resolucionPorRecursosGraphic = array();
        $insidenciasPorRecursosGraphicCategories = array();
        $totalPromedioinsidenciasPorRecurso = new \stdClass();
        $sumaInsidenciasTotal = 0;
        $promedioAnswerTotal = 0;
        $promedioSentimentTotal = 0;
//        $promedioSentimentClientTotal = 0;
//        $promedioSentimentSupportTotal = 0;
        $promedioResponseTotal = 0;
        $promedioDevelopTotal = 0;
        $promedioValidationTotal = 0;
        $promedioEvaluationTotal = 0;
        $cantEvaluationTotal = 0;
        
        $cantidadDias = 0;
        foreach ($supportMembers as $supportMember) {
            $sumaInsidencias = 0;
            $sumaValidaciones = 0;
            $sumaAnswer = 0;
            $sumaSentiment = 0;
//            $sumaSentimentClient = 0;
//            $sumaSentimentSupport = 0;
            $sumaResolution = 0;
            $sumaDevelop = 0;
            $sumaValidation = 0;
            $sumaEvaluation = 0;
            $cantEvaluation = 0;
            $countValidationTotal = 0;
            $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsByDatesBySupportMember($inicial, $finalDay, $supportMember);
            $sumaInsidencias = count($tickets);
            foreach ($tickets as $ticket) {
                $answer = floatval($ticket->getMinutesAnswerTime());
                $resolution = floatval($ticket->getMinutesSolutionTime());
                $develop = floatval($ticket->getMinutesDevTime());
                $validation = floatval($ticket->getMinutesValidationWaitTime());
                $validaciones = floatval($ticket->getValidationCount());
                $sumaAnswer = $sumaAnswer + $answer;
                $sumaSentiment = $sumaSentiment + floatval($ticket->getSentimentAsureAllMessages());
//                $sumaSentimentClient = $sumaSentimentClient + floatval($ticket->getSentimentAsureClientMessages());
//                $sumaSentimentSupport = $sumaSentimentSupport + floatval($ticket->getSentimentasureSupportMessages());
                $sumaResolution = $sumaResolution + $resolution;
                $sumaDevelop = $sumaDevelop + $develop;
                $sumaValidation = $sumaValidation + $validation;
                $sumaValidaciones = $sumaValidaciones + $validaciones;
                if($validaciones>0){
                    $countValidationTotal = $countValidationTotal+1;
                }
                if($ticket->getSatisfaction()!=null){
                    $sumaEvaluation = $sumaEvaluation + $ticket->getSatisfaction();
                    $cantEvaluation = $cantEvaluation+1;
                }
            }
            $dayWeek = $this->getDayOfWeek($ini);
            $dayWeek = $this->SpanishDate($dayWeek) . " " . strval(intval($ini->format('d')));
            
            
            $promedioAnswer = 0;
            $promedioSentiment = 0;
//            $promedioSentimentClient = 0;
//            $promedioSentimentSupport = 0;
            $promedioResolution = 0;
            $promedioDevelop = 0;
            $promedioValidation = 0;
            $promedioEvaluation = 0;
            if($sumaInsidencias > 0){
                $promedioAnswer = round($sumaAnswer/$sumaInsidencias,2);
                $promedioSentiment = round($sumaSentiment/$sumaInsidencias,2);
//                $promedioSentimentClient = round($sumaSentimentClient/$sumaInsidencias,2);
//                $promedioSentimentSupport = round($sumaSentimentSupport/$sumaInsidencias,2);
                $promedioResolution = round($sumaResolution/$sumaInsidencias,2);
                $promedioDevelop = round($sumaDevelop/$sumaInsidencias,2);
            }
            if($countValidationTotal > 0){
                $promedioValidation = round($sumaValidation/$countValidationTotal,2);
            }
            if($cantEvaluation > 0){
                $promedioEvaluation = round($sumaEvaluation/$cantEvaluation,2);
            }
            else{
                $promedioEvaluation = -1;
            }
            $stdObject = new \stdClass();
            $stdObject->client = $supportMember->getName();
            $stdObject->tickets = $sumaInsidencias;
            $stdObject->answer = $promedioAnswer;
            $stdObject->sentiment = $promedioSentiment;
//            $stdObject->sentimentClient = $promedioSentimentClient;
//            $stdObject->sentimentSupport = $promedioSentimentSupport;
            $stdObject->resolution = $promedioResolution;
            $stdObject->develop = $promedioDevelop;
            $stdObject->validation = $promedioValidation;
            $stdObject->evaluation = $promedioEvaluation;
            $stdObject->cantEvaluation = $cantEvaluation;
            $insidenciasPorRecursos[] = $stdObject;
            
            $insidenciasPorRecursosGraphic[] = round($sumaInsidencias,2);
            $validacionesPorRecursosGraphic[] = round($sumaValidaciones,2);
            $respuestasPorRecursosGraphic[] = round($promedioAnswer,2);
            $developPorRecursosGraphic[] = round($promedioDevelop,2);
            $validationPorRecursosGraphic[] = round($promedioValidation,2);
            $resolucionPorRecursosGraphic[] = round($promedioResolution,2);
            $insidenciasPorRecursosGraphicCategories [] = $supportMember->getName();
            
            if($sumaInsidencias > 0){
                $sumaInsidenciasTotal = $sumaInsidenciasTotal+$sumaInsidencias;
                $promedioAnswerTotal = $promedioAnswerTotal+$promedioAnswer;
                $promedioSentimentTotal = $promedioSentimentTotal+$promedioSentiment;
//                $promedioSentimentClientTotal = $promedioSentimentClientTotal+$promedioSentimentClient;
//                $promedioSentimentSupportTotal = $promedioSentimentSupportTotal+$promedioSentimentSupport;
                $promedioResponseTotal = $promedioResponseTotal+$promedioResolution;
                $promedioDevelopTotal = $promedioDevelopTotal+$promedioDevelop;
                $promedioValidationTotal = $promedioValidationTotal+$promedioValidation;
                $cantidadDias = $cantidadDias+1;
            }
            if($cantEvaluation > 0){
                $cantEvaluationTotal = $cantEvaluationTotal+1;
                $promedioEvaluationTotal = $promedioEvaluationTotal+$promedioEvaluation;
            }
        }
        
        $totalPromedioinsidenciasPorRecurso->totalInsidencias = $sumaInsidenciasTotal;
        if($cantidadDias > 0)
        {
            $totalPromedioinsidenciasPorRecurso->promedioRespuesta = round($promedioAnswerTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorRecurso->promedioSentiment = round($promedioSentimentTotal/$cantidadDias,2);
//            $totalPromedioinsidenciasPorRecurso->promedioSentimentClient = round($promedioSentimentClientTotal/$cantidadDias,2);
//            $totalPromedioinsidenciasPorRecurso->promedioSentimentSupport = round($promedioSentimentSupportTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorRecurso->promedioResolucion = round($promedioResponseTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorRecurso->promedioDevelop = round($promedioDevelopTotal/$cantidadDias,2);
            $totalPromedioinsidenciasPorRecurso->promedioValidation = round($promedioValidationTotal/$cantidadDias,2);
        }
        else{
            $totalPromedioinsidenciasPorRecurso->promedioRespuesta = 0;
            $totalPromedioinsidenciasPorRecurso->promedioSentiment = 0;
//            $totalPromedioinsidenciasPorRecurso->promedioSentimentClient = 0;
//            $totalPromedioinsidenciasPorRecurso->promedioSentimentSupport = 0;
            $totalPromedioinsidenciasPorRecurso->promedioResolucion = 0;
            $totalPromedioinsidenciasPorRecurso->promedioDevelop = 0;
            $totalPromedioinsidenciasPorRecurso->promedioValidation = 0;
        }
        if($cantEvaluationTotal > 0){
            $totalPromedioinsidenciasPorRecurso->promedioEvaluation = round($promedioEvaluationTotal/$cantEvaluationTotal,2);;
            $totalPromedioinsidenciasPorRecurso->cantEvaluation = $cantEvaluationTotal;
        }
        else{
            $totalPromedioinsidenciasPorRecurso->promedioEvaluation = -1;
        }
        
        $stdObject = new \stdClass();
        $stdObject->name = "Incidencias";
        $stdObject->data = $insidenciasPorRecursosGraphic;
        
        $stdObject1 = new \stdClass();
        $stdObject1->name = "Validaciones";
        $stdObject1->data = $validacionesPorRecursosGraphic;
        $insidenciasPorRecursosGraphic = array($stdObject);
        $insidenciasvalidationsPorRecursosGraphic = array($stdObject, $stdObject1);
        
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de respuesta";
        $stdObject->data = $respuestasPorRecursosGraphic;
        $respuestasPorRecursosGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de desarrollo";
        $stdObject->data = $developPorRecursosGraphic;
        $developPorRecursosGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de validación";
        $stdObject->data = $validationPorRecursosGraphic;
        $validationPorRecursosGraphic = array($stdObject);
        
        $stdObject = new \stdClass();
        $stdObject->name = "Tiempo promedio de resolución";
        $stdObject->data = $resolucionPorRecursosGraphic;
        $resolucionPorRecursosGraphic = array($stdObject);

        //Totales por categorias
        $insidenciasPorCategorias = array();
        $ticketsType = array();
        if($configuration_id)
            $ticketsType = $em->getRepository('WhatsappBundle:TicketType')->findByConfiguration($configuration_id);
        $sumaInsidenciasPorCategoria = 0;
        foreach ($ticketsType as $ticketType) {
            $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsByDatesByTicketType($inicial, $finalDay, $ticketType);
            $sumaInsidencias = count($tickets);
            $stdObject = new \stdClass();
            $stdObject->client = $ticketType->getName();
            $stdObject->tickets = $sumaInsidencias;
            $insidenciasPorCategorias[] = $stdObject;
            $sumaInsidenciasPorCategoria = $sumaInsidenciasPorCategoria+$sumaInsidencias;
        }
         
        usort($insidenciasPorGrupo, array($this, "customCompareCantTickets"));
        usort($insidenciasPorRecursos, array($this, "customCompareCantTickets"));
        
        
        $insidenciasPorHorasCategories = array();
        $insidenciasPorHorasSeries = array();
        
        foreach ($insidenciasPorHoras as $key => $value) {
            $insidenciasPorHorasCategories[] = "$key";
            $insidenciasPorHorasSeries[] = $value;
        }
        $stdObject = new \stdClass();
        $stdObject->name = "Peticiones";
        $stdObject->data = $insidenciasPorHorasSeries;
        $insidenciasPorHorasSeries = array($stdObject);
        return $this->render('WhatsappBundle:Default:report_by_date_range.html.twig',
                array(
                    'form1' => $form1->createView(),
                    'insidenciasPorDia' => $insidenciasPorDia,
                    'insidenciasPorDiaGraphic' => $insidenciasPorDiaGraphic,
                    'developPorDiaGraphic' => $developPorDiaGraphic,
                    'validationPorDiaGraphic' => $validationPorDiaGraphic,
                    'respuestasPorDiaGraphic' => $respuestasPorDiaGraphic,
                    'resolucionPorDiaGraphic' => $resolucionPorDiaGraphic,
                    'sentimentPorDiaGraphic' => $sentimentPorDiaGraphic,
                    'insidenciasPorDiaGraphicCategories' => $insidenciasPorDiaGraphicCategories,
                    'totalPromedioinsidenciasPorDia' => $totalPromedioinsidenciasPorDia,
                    
                    'insidenciasPorGrupo' => $insidenciasPorGrupo,
                    'insidenciasPorGrupoGraphic' => $insidenciasPorGrupoGraphic,
                    'respuestasPorGrupoGraphic' => $respuestasPorGrupoGraphic,
                    'developPorGrupoGraphic' => $developPorGrupoGraphic,
                    'validationPorGrupoGraphic' => $validationPorGrupoGraphic,
                    'resolucionPorGrupoGraphic' => $resolucionPorGrupoGraphic,
                    'sentimentPorGrupoGraphic' => $sentimentPorGrupoGraphic,
                    'insidenciasPorGrupoGraphicCategories' => $insidenciasPorGrupoGraphicCategories,
                    'totalPromedioinsidenciasPorGrupos' => $totalPromedioinsidenciasPorGrupos,
                    
                    'insidenciasPorRecursos' => $insidenciasPorRecursos,
                    'insidenciasPorRecursosGraphic' => $insidenciasPorRecursosGraphic,
                    'insidenciasvalidationsPorRecursosGraphic' => $insidenciasvalidationsPorRecursosGraphic,
                    'respuestasPorRecursosGraphic' => $respuestasPorRecursosGraphic,
                    'developPorRecursosGraphic' => $developPorRecursosGraphic,
                    'validationPorRecursosGraphic' => $validationPorRecursosGraphic,
                    'resolucionPorRecursosGraphic' => $resolucionPorRecursosGraphic,
                    'insidenciasPorRecursosGraphicCategories' => $insidenciasPorRecursosGraphicCategories,
                    'totalPromedioinsidenciasPorRecurso' => $totalPromedioinsidenciasPorRecurso,
                    
                    'insidenciasPorCategorias' => $insidenciasPorCategorias,
                    'sumaInsidenciasPorCategoria' => $sumaInsidenciasPorCategoria,
                    'inicial' => $inicial,
                    'finalDay' => $finalDay,
                    'insidenciasPorHorasCategories' => $insidenciasPorHorasCategories,
                    'insidenciasPorHorasSeries' => $insidenciasPorHorasSeries,
                    'form' => $form->createView(),
                )
                );
    }

    public function customCompare($a, $b) {

        if ($a[3] == $b[3]) {
            return 0;
        }
        return ($a[3] < $b[3]) ? 1 : -1;
    }

    public function getDayOfWeek($date) {
        return date('l', strtotime($date->format('Y-m-d')));
    }

    function SpanishDate($weekDay) {
        $diassemanaN = array("Sunday" => "Domingo", "Monday" => "Lunes", "Tuesday" => "Martes", "Wednesday" => "Miércoles",
            "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado");
        return $diassemanaN[$weekDay];
    }

    /**
     * 
     * @Route("/frontend/status_phone_conected", name="status_phone_conected" )
     */
    public function statusPhoneConectedAction() {
        $wt = $this->get('whatsapp.sacspro.phonestatus');
        $conected = $wt->statusPhoneConected();
//        $conected = false;
        if ($conected) {
            $this->setFlash(
                    'sonata_flash_success', 'Teléfono conectado.'
            );
        } else {
            $this->setFlash(
                    'sonata_flash_error', 'Teléfono no conectado.'
            );
        }
        return $this->redirectToRoute('report_peticion');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value) {
        $this->get('session')->getFlashBag()->set($action, $value);
    }

//    private function getDailyTicketsCountLastWeek($em) {
//        return $this->getDailyLastWeek($em, 0);
//    }
//
//    private function getDailyAnswwerLastWeek($em, $cants) {
//        return $this->getDailyLastWeek($em, $cants, "answer");
//    }
//
//    private function getDailySolutionLastWeek($em, $cants) {
//        return $this->getDailyLastWeek($em, $cants, "solution");
//    }

    private function getNextFriday($userTimezone) {
        $nextFriday = new \DateTime("next friday 17:29:59", $userTimezone);
        $utc = new \DateTimeZone("UTC");
        $nextFriday->setTimezone($utc);
        return $nextFriday;
    }

    
    
    public function getLastFriday($weekday, $hourEndWeek, $userTimezone) {
        $day = new \DateTime("last ".$weekday." ".$hourEndWeek, $userTimezone);
        $friday =  new \DateTime($weekday." ".$hourEndWeek, $userTimezone);
        $now = new \DateTime("now", $userTimezone);
        if($now > $friday){
           $day = $friday;
        }
        $utc = new \DateTimeZone("UTC");
        $day->setTimezone($utc);
        return $day;
    }
    
//    private function getLastFriday($userTimezone) {
//        $day = new \DateTime("last friday 17:30:00", $userTimezone);
//        $friday =  new \DateTime("friday 17:30:00", $userTimezone);
//        $now = new \DateTime("now", $userTimezone);
//        if($now > $friday){
//           $day = $friday;
//        }
//        $utc = new \DateTimeZone("UTC");
//        $day->setTimezone($utc);
//        return $day;
//    }

    private function getDailyLastWeek($configuration_id, $em, $cants, $modo = "tickets") {
        $index = 0;
        $datas = array();
        $names = array();
        $userTimezone = $this->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $weekday = $this->getLastWeekday();
        $hourEndWeek = $this->getHourEndWeek();
        
        $lastFriday = $this->getLastFriday($weekday, $hourEndWeek, $userTimezone);;
        $ini = clone $lastFriday;
        $nextFriday = $this->getNextFriday();
        while ($ini < $nextFriday) {
            $iniNigth = clone $ini;
            $iniNigth->setTime(23, 59, 59);

            if ($modo == "answer") {
                $cant = $cants[$index];
                $sum = $em->getRepository('WhatsappBundle:Ticket')->sumTicketsAnswerTime($ini, $iniNigth, $configuration_id);
                if ($cant > 0)
                    $datas[] = round(floatval($sum / $cant), 2);
                else
                    $datas[] = 0;
                $index = $index + 1;
            }
            else if ($modo == "solution") {
                $cant = $cants[$index];
                $sum = $em->getRepository('WhatsappBundle:Ticket')->sumTicketsSolutionTime($ini, $iniNigth, $configuration_id);
                if ($cant > 0)
                    $datas[] = round($sum / $cant, 2);
                else
                    $datas[] = 0;
                $index = $index + 1;
            }
            else {
                $cant = $em->getRepository('WhatsappBundle:Ticket')->ticketsCountByDates($ini, $iniNigth);
                $datas[] = round($cant, 2);
            }
            $dayWeek = $this->getDayOfWeek($ini);
            $dayWeek = $this->SpanishDate($dayWeek) . " " . strval(intval($ini->format('d')));
            $names[] = $dayWeek;
            $ini->modify("+1 day");
            $ini->setTime(0, 0, 0);
        }
        $stdObject = new \stdClass();
        $stdObject->names = $names;
        $stdObject->data = $datas;
        return $stdObject;
    }

    public function customCompareCantTickets($a, $b) {

        if ($a->tickets == $b->tickets) {
            return 0;
        }
        return ($a->tickets < $b->tickets) ? 1 : -1;
    }

    /**
     * 
     * @Route("/ver", name="ver" )
     */
    public function sendEmailAction() {
        $user = $this->getUser();
        $wt = $this->get('whatsapp.sacspro.phonestatus');
        $wt->calcSentimentMessages();
         return $this->render('ApplicationSonataUserBundle:Registration:registration.email.twig',
                array(
                    "theme_color" => "#fff",
                    "happy_face" => "#fff",
                    "user" => $user,
                    "confirmationUrl" => "sdsddfsdfsdsfsd",
                    "logo_cuba_color" => "sdsddfsdfsdsfsd",
                    
                ));
        
    }
    
     private function is_super() {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        return false;        
    }
    
    private function getFormConfiguration($isDashboard){
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        $choices = array();
        if($this->is_super()){
            $configurations = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurations as $value) {
                $choices[$value->getId()]= $value->getCompany();
            }
        }
        else{
            $configurations = array();
            $user = $this->getUser();
            $configurationsUsers = $user->getConfigurations();
            foreach ($configurationsUsers as $key => $value) {
                $configurations[] = $value->getConfiguration();
            }
            foreach ($configurations as $value) {
                $choices[$value->getId()]= $value->getCompany();
            }
        }
        $options = array();
        $options["configurations"] = $choices;
//        dump($configuration_id);
        if(!$configuration_id){
            foreach ($options["configurations"] as $key => $value) {
//                dump($key);
                $configuration_id = $key;
                break;
            }
            
            $this->get('session')->set("dashboard-configuration-id", $configuration_id);
        }
//       die;
        
        $form = $this->createForm(new DashboardConfigurationType(), null, $options);
        if($isDashboard){
            if ($request->getMethod() == 'POST') {
                $form->bind($request);
                if ($form->isValid()) {
                    $newConfiguration = $form->get("configuration");
                    $newConfiguration = $newConfiguration->getData();
                    $configuration_id = $newConfiguration;
                    $this->get('session')->set("dashboard-configuration-id", $configuration_id);
                }
            }
            else{
                $form->get("configuration")->setData($configuration_id);
            }
        }
        else{
            
            $form->get("configuration")->setData($configuration_id);
        }
        
        return $form;
    }
    
    
    /**
     * 
     * @Route("/send_contact_message", name="send_contact_message" )
     */
    public function sendContactMessageAction() {
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $request = $this->getRequest();
            $name = $request->get("name");
            $email = $request->get("email");
            $message = $request->get("message");
            $contact = new Contact();
            $contact->setEmail($email);
            $contact->setName($name);
            $contact->setMessage($message);
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            $this->addFlash('sonata_flash_error', "Mensaje recibido. Pronto nos pondremos en contacto con usted.");
            return $this->redirectToRoute('home');

        }
        
        
        
    }
    
    public function getTimeZone(){
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        if($configuration_id){
            $em = $this->getDoctrine()->getManager();
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration_id);
            if($configuration->getTimeZone())
                return $configuration->getTimeZone();
        }
        return 'America/Mexico_City';
    }
    public function getLastWeekday(){
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        if($configuration_id){
            $em = $this->getDoctrine()->getManager();
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration_id);
            if($configuration->getDayEndWeek())
                return $configuration->getDayEndWeek();
            return "sunday";
        }
        return "sunday";
    }
    
    public function getHourEndWeek(){
        $configuration_id = $this->get('session')->get("dashboard-configuration-id");
        if($configuration_id){
            $em = $this->getDoctrine()->getManager();
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration_id);
            if($configuration->getHourEndWeek())
                return $configuration->getHourEndWeek();
            return "23:59:59";
        }
        return '23:59:59';
    }

}
