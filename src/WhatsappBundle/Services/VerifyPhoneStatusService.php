<?php

namespace WhatsappBundle\Services;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\ClientErrorResponseException;
use WhatsappBundle\Entity\RepitlyKeyword;
use WhatsappBundle\Entity\TicketRepitlyKeywordGroup;
use WhatsappBundle\Entity\TicketLog;

class VerifyPhoneStatusService {

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function statusPhoneConected() {
        //visitar http://localhost/sacspro/normalize_questions
        $client = new \Buzz\Client\Curl();
        $client->setTimeout(3600);
        $client->setVerifyPeer(false);
        $browser = new \Buzz\Browser($client);
        $uri = "https://www.waboxapp.com/api/status/5218183874811?token=14b24cdcbe9de01bab76a56c7dc6f61a5a70f16c4ec79";
        try {


            $packagistResponse = $browser->get($uri);
//        $packages = $packagistResponse->getContent();
            $define = json_decode($packagistResponse->getContent());
        } catch (ClientErrorResponseException $ex) {
            
        }
        $status = false;
        if (isset($define->success)) {
            $status = true;
        }
        else{
            $url = "http://soporteweb.azurewebsites.net/alertwhatsapp.aspx";
            $date = new \DateTime("now");
            $timezone = new \DateTimeZone("America/Mexico_city");
            $date->setTimezone($timezone);
            $jsonData = array(
              'grupo'=> "Sin grupo",
              'fecha'=> $date->format('Y-m-d'),
              'hora'=> $date->format('H:i:s'),
              'mensaje'=> "El teléfono en Waboxapp tiene estado desconectado. Por favor, verificar la cobertura, el uso de datos y la sección de Chrome en la máquina virtual.",
              'tipo_alerta'=> "Conexion con Waboxapp",
              'send_email'=> 0,
              'send_call'=> 0,
              'send_sms'=> 0,
              'emails'=> array(),
              'phones'=> array(),
              'company_name'=> "No es una compañía"
            );
             $this->postCurlUrl($url, $jsonData);
        }
        $configuration = $this->em->getRepository('WhatsappBundle:GeneralConfiguration')->find(1);
        $configuration->setStatusPhoneConected($status);
        $this->em->persist($configuration);
        $this->em->flush();
        //        if not "success" in json_obj:
        return $status;
    }

   

    public function closePendingTickets() {
        $em = $this->em;
        $this->fixMessagesPhantomTicket($em);
//        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findUnclosed();
//        foreach ($tickets as $value) {
//            $value->setTicketended(true);
//            $em->persist($value);
//        }
//        $em->flush();
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findNotSolvedBySupportMember();
        foreach ($tickets as $ticket) {
            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
            $supportId = null;
            $dateFirstMessage = null;
            foreach ($messages as $message) {
                $supportId = $message->getSupportMember();
                if ($supportId) {
                    $dateFirstMessage = $message->getDtmmessage();
                    break;
                }
                if ($message->getSupportFirstAnswer()) {
                    $dateFirstMessage = $message->getDtmmessage();
                    break;
                }
            }
            if ($supportId)
                $ticket->setSolvedBySupportMember($supportId);
            if ($dateFirstMessage) {
                if($ticket->getStartDate() != null){
                    $since_first_answer = $dateFirstMessage->diff($ticket->getStartDate());
                    $minutes = 0;
                    $minutes = $since_first_answer->days * 24 * 60;
                    $minutes += $since_first_answer->h * 60;
                    $minutes += $since_first_answer->i;
                    $minutes += $since_first_answer->s / 60;
                    $ticket->setMinutesAnswerTime(strval(round($minutes, 2)));
                }
            }
            if (!$ticket->getMinutesAnswerTime()) {
                $ticket->setMinutesAnswerTime($ticket->getMinutesSolutionTime());
            }
            $em->persist($ticket);
        }
//        buscar los tickets de las semana
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findTicketsweeks();
        foreach ($tickets as $ticket) {
            //buscar alertas de solicitudes respondidas
            $responseAlerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketByEnabled($ticket, "Respuesta");
            foreach ($responseAlerts as $alert) {
                if ($ticket->getFirstanswer() or $ticket->getTicketended()) {
                    $alert->setOpen(false);
                    $em->persist($alert);
                }
            }

            //buscar alertas de solicitudes respondidas
            $resolutionAlerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketByEnabled($ticket, "Resolución");
            foreach ($resolutionAlerts as $alert) {
                if ($ticket->getTicketended()) {
                    $alert->setOpen(false);
                    $em->persist($alert);
                }
            }

            $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketNull();
            foreach ($alerts as $alert) {
                $alert->setOpen(false);
                $em->persist($alert);
            }

            if (!$ticket->getFirstanswer()) {
                $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
                foreach ($messages as $message) {
                    if ($message->getSupportFirstAnswer()) {
                        $ticket->setFirstanswer(true);
                        $em->persist($ticket);
                        $em->flush();
                        break;
                    }
                }
            }
        }

        $em->flush();
        $this->deteleNoRegisterTicketsMessagesAndAlerts($em);
        $this->generarDiasDeLaSemana($em);
        $this->recalculateStartDate($em);
        $this->deteleAlertsWhitoutTickets($em);
        $this->closePendingNoRegisterTickets($em);

//        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findSolvedBySupportMemberAndNotClientSatisfaction();
//        foreach ($tickets as $ticket) {
//            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
//            $supportId = null;
//            $textToAnalisis = "";
//            foreach ($messages as $message) {
//                $supportId = $message->getSupportMember();
//                if($message->getClientMember()){
//                    //analizar sentimiento uno a uno o todos
//                    $textToAnalisis = $textToAnalisis." ".$message->getStrmenssagetext();
//                }
//            }
//            //realizar analisis $textToAnalisis
//            
//            $ticket->setSatisfactiondDescritpion("respuesta");
//            $em->persist($ticket);
//        }
//        $em->flush();
    }

    private function fixMessagesPhantomTicket($em) {
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->ticketsByNotFirstAnswerNotEnded();
//        print_r($tickets);
        foreach ($tickets as $ticket) {

            $idGroup = $ticket->getWhatsappGroup();
            if (!$idGroup)
                continue;
            $anteriorTicket = $em->getRepository('WhatsappBundle:Ticket')->ticketAnteriorToThis($ticket->getId(), $idGroup);
            if (count($anteriorTicket) > 0)
                $anteriorTicket = $anteriorTicket[0];
            else
                continue;
            $endDateAnteriorTicket = $anteriorTicket->getEndDate();
            if($endDateAnteriorTicket != null){
                $hours = 0;
                $since_start = $ticket->getEndDate()->diff($endDateAnteriorTicket);
                $hours = $since_start->days * 24;
                $hours += $since_start->h;
                if ($hours < 1) {
                    $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
                    foreach ($messages as $message) {
                        $message->setTicket($anteriorTicket);
                        $em->persist($message);
                    }
                    $em->flush();

                    $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicket($ticket);
                    foreach ($alerts as $alert) {
                        $em->remove($alert);
                    }
                    $em->flush();
                    $em->remove($ticket);
                    $em->flush();
                }
            }
        }
    }

    private function deteleNoRegisterTicketsMessagesAndAlerts($em) {
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findNoRegisterTickets5HoursAgo();
//        print_r($tickets);
        foreach ($tickets as $ticket) {
            $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
            foreach ($messages as $message) {
                $message->setTicket(null);
                $em->persist($message);
            }
            $em->flush();

            $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicket($ticket);
            foreach ($alerts as $alert) {
                $em->remove($alert);
            }
            $em->flush();
            $this->copyTicketOnDelete($ticket->getId(), "Eliminado luego de 5 horas");
            $em->remove($ticket);
        }
        $em->flush();
    }

    private function deteleAlertsWhitoutTickets($em) {
        $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicket(null);
        foreach ($alerts as $alert) {
            $em->remove($alert);
            $em->flush();
        }
        $em->flush();
    }

    private function generarDiasDeLaSemana($em) {
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findTicketsweeks();

//        print_r($tickets);
        foreach ($tickets as $ticket) {
            if ($ticket->getStartDate()) {
                $ticket->setWeekday($this->SpanishDate($this->getDayOfWeek($ticket->getStartDate())));
                $em->persist($ticket);
            }
        }
        $em->flush();
    }

    public function getDayOfWeek($date) {
        if ($date)
            return date('l', strtotime($date->format('Y-m-d')));
        return null;
    }

    function SpanishDate($weekDay) {
        $diassemanaN = array("Sunday" => "Domingo", "Monday" => "Lunes", "Tuesday" => "Martes", "Wednesday" => "Miércoles",
            "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado");
        return $diassemanaN[$weekDay];
    }

    function recalculateStartDate($em) {
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findTicketsweeks();
        foreach ($tickets as $ticket) {
            $ticket->recalculateStartDate();
            $ticket->findAndRecalculeFirstAnswer();
            $ticket->recalculeValidation();
            $em->persist($ticket);
        }
        $em->flush();
    }

    function closePendingNoRegisterTickets($em) {
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findNoRegisterTicketsOpen30MinsAgo();
        foreach ($tickets as $ticket) {
            $ticket->setTicketended(true);
            $em->persist($ticket);
        }
        $em->flush();
    }

    public function changeGroupConfiguration($group, $configuration) {
        $configuration = $this->em->getRepository('WhatsappBundle:Configuration')->find($configuration);
        $group = $this->em->getRepository('WhatsappBundle:WhatsappGroup')->find($group);
        if ($group) {
            $group->setConfiguration($configuration);
            //Buscar todos los tickets para ponerles la nueva configuracion
            $alerts = $group->getAlerts();
            foreach ($alerts as $value) {
                $value->setConfiguration($configuration);
                $this->em->persist($value);
            }
            $this->em->flush();
            $tickets = $group->getTickets();
            foreach ($tickets as $value) {
                $value->setConfiguration($configuration);
                $this->em->persist($value);
                $messages = $value->getMessages();
                foreach ($messages as $message) {
                    $message->setConfiguration($configuration);
                    $this->em->persist($message);
                }
            }
            $this->em->flush();
        }
    }

    public function forceChangeGroupConfiguration() {
        $configuration = $this->em->getRepository('WhatsappBundle:Configuration')->find(1);
        $groups = $this->em->getRepository('WhatsappBundle:WhatsappGroup')->findAll();
        foreach ($groups as $group) {
            if ($group->getConfiguration() == null) {
//                dump("null");
//                die;
                $group->setConfiguration($configuration);
                //Buscar todos los tickets para ponerles la nueva configuracion
                $alerts = $group->getAlerts();
                foreach ($alerts as $value) {
                    $value->setConfiguration($configuration);
                    $this->em->persist($value);
                }
                $this->em->flush();
                $tickets = $group->getTickets();
                foreach ($tickets as $value) {
                    $value->setConfiguration($configuration);
                    $this->em->persist($value);
                    $messages = $value->getMessages();
                    foreach ($messages as $message) {
                        $message->setConfiguration($configuration);
                        $this->em->persist($message);
                    }
                }
                $this->em->flush();
            }
        }
    }

    public function changeResourceFromConfiguration($oldResource, $newResource, $configuration) {
        $configuration = $this->em->getRepository('WhatsappBundle:Configuration')->find($configuration);
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findByConfiguration($configuration);
        $newResource = $this->em->getRepository('WhatsappBundle:SupportMember')->find($newResource);
        foreach ($tickets as $ticket) {
            if ($ticket->getSolvedBySupportMember()) {
                if ($ticket->getSolvedBySupportMember()->getId() == $oldResource) {
                    $ticket->setSolvedBySupportMember($newResource);
                    $this->em->persist($ticket);
                }
            }
        }

        $this->em->flush();
    }

    public function changeTicketTypeFromConfiguration($oldTicketType, $newTicketType, $configuration) {
        $configuration = $this->em->getRepository('WhatsappBundle:Configuration')->find($configuration);
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findByConfiguration($configuration);
        $newTicketType = $this->em->getRepository('WhatsappBundle:TicketType')->find($newTicketType);
        foreach ($tickets as $ticket) {
            if ($ticket->getTicketType()) {
                if ($ticket->getTicketType()->getId() == $oldTicketType) {
                    $ticket->setTicketType($newTicketType);
                    $this->em->persist($ticket);
                }
            }
        }

        $this->em->flush();
    }

    public function transformMexicoTimeToUTC() {
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findAll();
        foreach ($tickets as $ticket) {
                $time = $ticket->getStartDate();
                if($time){
                    $time->add(new \DateInterval("PT1H"));
                    $ticket->setStartDate(clone $time);
                    $this->em->persist($ticket);
                }
                
                $time = $ticket->getEndDate();
                if($time){
//                    $userTimezone = new \DateTimeZone("UTC");
//                    $time->setTimezone($userTimezone);
                    $time->add(new \DateInterval("PT1H"));
                    $ticket->setEndDate(clone $time);
                }
                
                $time = $ticket->getStartTime();
                if($time){
//                    $userTimezone = new \DateTimeZone("UTC");
//                    $time->setTimezone($userTimezone);
                    $time->add(new \DateInterval("PT1H"));
                    $ticket->setStartTime(clone $time);
                }
                $this->em->persist($ticket);
                foreach ($ticket->getMessages() as $message) {
                    $time = $message->getDtmmessage();
                    if($time){
//                        $userTimezone = new \DateTimeZone("UTC");
//                        $time->setTimezone($userTimezone);
                        $time->add(new \DateInterval("PT1H"));
                        $message->setDtmmessage(clone $time);
                        $this->em->persist($message);
                    }
                }
                foreach ($ticket->getAlerts() as $alert) {
                    $time = $alert->getSendDate();
                    if($time){
//                        $userTimezone = new \DateTimeZone("UTC");
//                        $time->setTimezone($userTimezone);
                        $time->add(new \DateInterval("PT1H"));
                        $alert->setSendDate(clone $time);
                        $this->em->persist($alert);
                    }
                }
//                die;
//                    $ticket->setTicketType($newTicketType);
//                    $this->em->persist($ticket);
        }

        $this->em->flush();
    }
    
     public function correctDayOfWeek() {
        //visitar http://localhost/sacspro/normalize_questions
        
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findAll();
        foreach ($tickets as $ticket) {
            if($ticket->getStartDate())
                $startDate = clone $ticket->getStartDate();
            if($ticket->getConfiguration()){
                $timezone = new \DateTimeZone($ticket->getConfiguration()->getTimezone());
                $startDate->setTimezone($timezone);
                $dayOfWeek = $ticket->SpanishDate($ticket->getDayOfWeek($startDate));
                $ticket->setWeekday($dayOfWeek);
                $this->em->persist($ticket);
            }
            
        }
        $this->em->flush();
    }
    
     public function correctDevelopTime() {
        //visitar http://localhost/sacspro/normalize_questions
        
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findAll();
        foreach ($tickets as $ticket) {
            if($ticket->getValidationCount() == 0){
                if($ticket->getMinutesValidationWaitTime() > 0 && $ticket->getMinutesDevTime() == 0){
                    $minutes = $ticket->getMinutesValidationWaitTime();
                    dump($minutes);
                    $ticket->setMinutesDevTime($minutes);
                    $ticket->setMinutesValidationWaitTime(0);
                    dump($ticket);
                }
            }
            $this->em->persist($ticket);
        }
        $this->em->flush();
    }
    
    private function postCurlUrl($url, $data){
        //Initiate cURL.
        $ch = curl_init($url);
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }

        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($data);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 

        //Execute the request
        $result = curl_exec($ch);
        return $result;
    }
    
    public function calcSentimentMessages() {
        //visitar http://localhost/sacspro/normalize_questions
        $ini = new \DateTime("-1 weeks");
        $fin = new \DateTime("now");
        $configurationId = 14;
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->ticketsByDates($ini, $fin, $configurationId);
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findAllSortDate(50);
//        dump(count($tickets));
        foreach ($tickets as $ticket) {
            dump($ticket->getStartDate());
            $messages = $ticket->getMessages();
            foreach ($messages as $message) {

                $jsonData = new \stdClass();
                $jsonData->messages = array();
                $mess = new \stdClass();
                $mess->message =  $message->getStrmenssagetext();
                $jsonData->messages[] = $mess;
                if($mess->message != null && $mess->message != ""){
//                    if($message->getSentimentNumber() == null){
                        $url = "http://127.0.0.1:9002/sentimentvader";
                        
                        $result = $this->postCurlUrl($url, $jsonData);
                        $result = json_decode($result);
                        $message->setSentimentVader($result->result[0]->sentiment);
                        
                        $url = "http://127.0.0.1:9002/sentimenttextblob";
                        $result = $this->postCurlUrl($url, $jsonData);
                        $result = json_decode($result);
                        $message->setSentimentTextblob($result->result[0]->sentiment);
                        
                        $url = "http://127.0.0.1:9002/sentimentspanish";
                        $result = $this->postCurlUrl($url, $jsonData);
                        $result = json_decode($result);
                        $message->setSentimentSpahish($result->result[0]->sentiment);
                        
                        
                        $url = "http://127.0.0.1:9002/sentimentasure";
                        $result = $this->postCurlUrl($url, $jsonData);
                        $result = json_decode($result);
                        $message->setSentimentAsure($result->result[0]->sentiment);
                        
                        $this->em->persist($message);
//                    }
                }

            }
            $this->em->flush();
//            die;
        }
        
    }
    
    public function calcSentimentTickets() {
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findNoSentimetToday();
        
        foreach ($tickets as $ticket) {
            $keywords = array();
            $messageconcat = "";
            $messages = $ticket->getMessages();
            foreach ($messages as $message) {
                $messageconcat = $messageconcat.". ".$message->getStrmenssagetext();
            }
            $mess = new \stdClass();
            $mess->message =  $messageconcat;
            if(trim($messageconcat) == "")
                continue;
            $url = "http://127.0.0.1:9002/sentiment";
            $result = $this->postCurlUrl($url, $mess);
            $result = json_decode($result);
            
//            dump($result);
            $ticket->setSentimentVaderAllMessages($result->vader_sentiment);
            $ticket->setSentimentSpahishAllMessages($result->spanish_sentiment);
            $ticket->setSentimentTextblobAllMessages($result->textblob_sentiment);
            $ticket->setSentimentAsureAllMessages(round($result->asure_sentiment*100,0));
//             dump($ticket);
            $this->em->persist($ticket);
            $this->em->flush();
            
            $url = "http://127.0.0.1:9002/keywords";
            $result = $this->postCurlUrl($url, $mess);
            $result = json_decode($result);
            $keywords = $result->azure_keywords;
//            dump($keywords);
            foreach ($keywords as $value) {
                $repitlyKeyword = new RepitlyKeyword();
                $repitlyKeywords = $this->em->getRepository('WhatsappBundle:RepitlyKeyword')->findByConfigurationByKeyword($ticket->getConfiguration(), $value);
                
                if(count($repitlyKeywords) > 0){
                    $repitlyKeyword = $repitlyKeywords[0];
                }
                else{
                    $repitlyKeyword->setKeyword($value);
                    $repitlyKeyword->setEnabled(true);
                    $repitlyKeyword->setConfiguration($ticket->getConfiguration());
                    $this->em->persist($repitlyKeyword);
                    $this->em->flush();
                }
                $ticketRepitlyKeywordGroup = new TicketRepitlyKeywordGroup();
                $ticketRepitlyKeywordGroup->setRepitlyKeyword($repitlyKeyword);
                $ticketRepitlyKeywordGroup->setTicket($ticket);
                $ticketRepitlyKeywordGroup->setWhatsappGroup($ticket->getWhatsappGroup());
                $ticketRepitlyKeywordGroup->setStartDate($ticket->getStartDate());
                $ticketRepitlyKeywordGroup->setConfiguration($ticket->getConfiguration());
                $this->em->persist($ticketRepitlyKeywordGroup);
                $this->em->flush();
            }
            
        }
//        foreach ($tickets as $ticket) {
//            $messageconcat = "";
//            $messages = $ticket->getMessages();
//            foreach ($messages as $message) {
//                if($message->getSupportMember() != null)
//                    $messageconcat = $messageconcat.". ".$message->getStrmenssagetext();
//            }
//            $mess = new \stdClass();
//            $mess->message =  $messageconcat;
//            if(trim($messageconcat) == "")
//                continue;
//            $url = "http://127.0.0.1:9002/sentiment";
//            $result = $this->postCurlUrl($url, $mess);
//            $result = json_decode($result);
//            $ticket->setSentimentVaderSupportMessages($result->vader_sentiment);
//            $ticket->setSentimentSpahishSupportMessages($result->spanish_sentiment);
//            $ticket->setSentimentTextblobSupportMessages($result->textblob_sentiment);
//            $ticket->setSentimentasureSupportMessages(round($result->asure_sentiment*100,0));
//            $this->em->persist($ticket);
//            $this->em->flush();
//        }
//        foreach ($tickets as $ticket) {
//            $messageconcat = "";
//            $messages = $ticket->getMessages();
//            foreach ($messages as $message) {
//                if($message->getClientMember() != null)
//                    $messageconcat = $messageconcat.". ".$message->getStrmenssagetext();
//            }
//            $mess = new \stdClass();
//            $mess->message =  $messageconcat;
//            $url = "http://127.0.0.1:9002/sentiment";
//            if(trim($messageconcat) == "")
//                continue;
//            $result = $this->postCurlUrl($url, $mess);
//            $result = json_decode($result);
////            dump($result);
////            dump($mess);
//            $ticket->setSentimentVaderClientMessages($result->vader_sentiment);
//            $ticket->setSentimentSpahishClientMessages($result->spanish_sentiment);
//            $ticket->setSentimentTextblobClientMessages($result->textblob_sentiment);
//            $ticket->setSentimentasureClientMessages(round($result->asure_sentiment*100,0));
//            $this->em->persist($ticket);
//            $this->em->flush();
//        }
        
        
    }
    
    public function convertTicketTypeToMany() {
        $tickets = $this->em->getRepository('WhatsappBundle:Ticket')->findAll();
        foreach ($tickets as $ticket) {
            $client = $ticket->getSentimentasureClientMessages();
            $support = $ticket->getSentimentasureSupportMessages();
            $all = $ticket->getSentimentAsureAllMessages();
            if($client != null){
                $ticket->setSentimentasureClientMessages(round($client*100,0));
            }
            if($support != null){
                $ticket->setSentimentasureSupportMessages(round($support*100,0));
            }
            if($all != null){
                $ticket->setSentimentAsureAllMessages(round($all*100,0));
            }
            
            $this->em->persist($ticket);
               
        }
         $this->em->flush();
        
    }
    
    
    public function sendMessageTicketDeleteOrNoFollow($ticketId, $isDeleted, $username) {
        $ticket = $this->em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
        $message = "Se ha eliminado la petición ".$ticket->getName(). "Operación realizada por el usuario ".$username;
        $action = "Petición eliminada";
        if($isDeleted == false){
            $message = "Se ha marcado como aviso la petición ".$ticket->getName(). "Operación realizada por el usuario ".$username;
            $action = "Petición marcada de aviso";
        }
        $url = "http://soporteweb.azurewebsites.net/alertwhatsapp.aspx";
        $date = new \DateTime("now");
        $timezone = new \DateTimeZone("America/Mexico_city");
        $date->setTimezone($timezone);
        $jsonData = array(
          'grupo'=> $ticket->getWhatsappGroup()->getName(),
          'fecha'=> $date->format('Y-m-d'),
          'hora'=> $date->format('H:i:s'),
          'mensaje'=> $message,
          'tipo_alerta'=> $action,
          'send_email'=> 0,
          'send_call'=> 0,
          'send_sms'=> 0,
          'emails'=> array(),
          'phones'=> array(),
          'company_name'=> $ticket->getConfiguration()->getCompany()
        );
//        dump($jsonData);die;
         $this->postCurlUrl($url, $jsonData);
        
    }
    
    
    public function copyTicketOnDelete($ticketId, $deletedByUsername) {
        $ticket = $this->em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
        $ticketLog = new TicketLog();
        $ticketLog->setOldId($ticket->getId());
        $ticketLog->setConfiguration($ticket->getConfiguration());
        $ticketLog->setName($ticket->getName());
        $ticketLog->setStartDate($ticket->getStartDate());
        $ticketLog->setWeekday($ticket->getWeekday());
        $ticketLog->setResolutionDate($ticket->getResolutionDate());
        $ticketLog->setMinutesAnswerTime($ticket->getMinutesAnswerTime());
        if($ticket->getSolvedBySupportMember() != null){
            $ticketLog->setSolvedBySupportMemberName($ticket->getSolvedBySupportMember()->getName());
        }
        $categories = $ticket->getTicketTypes();
        $cat = array();
        foreach ($categories as $value) {
            $cat[] = $value->getName();
        }
        if(count($cat) > 0){
            $ticketTypes = join(", ", $cat);
            $ticketLog->setTicketTypes($ticketTypes);
        }
        $ticketLog->setEndDate($ticket->getEndDate());
        $ticketLog->setValidationCount($ticket->getValidationCount());
        $ticketLog->setMinutesDevTime($ticket->getMinutesDevTime());
        $ticketLog->setMinutesValidationWaitTime($ticket->getMinutesValidationWaitTime());
        $ticketLog->setMinutesSolutionTime($ticket->getMinutesSolutionTime());
        $ticketLog->setFirstanswer($ticket->getFirstanswer());
        $ticketLog->setSendalert($ticket->getSendalert());
        $ticketLog->setTicketended($ticket->getTicketended());
        $ticketLog->setNofollow($ticket->getNofollow());
        if($ticket->getSolutionType() != null)
            $ticketLog->setSolutionTypeName($ticket->getSolutionType()->getName());
        $ticketLog->setSentimentAsureAllMessages($ticket->getSentimentAsureAllMessages());
        $ticketLog->setSatisfaction($ticket->getSatisfaction());
        $ticketLog->setDeletedByUsername($deletedByUsername);
        $ticketLog->setDeletedDate(new \DateTime("now"));
        $losg = $this->em->getRepository('WhatsappBundle:TicketLog')->findByOldId($ticket->getId());
        if (count($losg) == 0){
            $this->em->persist($ticketLog);
            $this->em->flush();
        }
    }

}
