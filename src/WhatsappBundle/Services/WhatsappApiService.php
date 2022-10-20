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

class WhatsappApiService {

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
                         
    public function sendMessageChatid($chatId, $body) {
        $uri = "https://eu41.chat-api.com/instance56134/sendMessage?token=worr7xsx6ul21r1f";
        $jsonData = array(
          'chatId'=> $chatId,
          'body'=> $body,
        );
        $this->postCurlUrl($uri, $jsonData);
    }
    
    public function sendMultimediaChatid($chatId, $caption, $body) {
        $uri = "https://eu41.chat-api.com/instance56134/sendFile?token=worr7xsx6ul21r1f";
        $jsonData = array(
                'chatId'=> $chatId,
                'body'=> $body,
                'filename'=> "publicación.jpg",
                'caption'=> $caption
            );
        $this->postCurlUrl($uri, $jsonData);
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

}
