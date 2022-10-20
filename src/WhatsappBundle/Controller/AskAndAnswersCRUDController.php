<?php

namespace WhatsappBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AskAndAnswersCRUDController extends Controller {

    public function askprocessAction(Request $request) {
        //visitar http://localhost/sacspro/normalize_questions
        $client = new \Buzz\Client\Curl();
        $client->setTimeout(3600);
        $browser = new \Buzz\Browser($client);
        $uri = "http://localhost/sacspro/normalize_questions";
        $packagistResponse = $browser->get($uri);
        $packages = $packagistResponse->getContent();
//        $pythonScript = "cd ".$this->get('kernel')->getRootDir()."/../bin/;python3 normalize_questions.py";
//        $pythonScript = "".$this->get('kernel')->getRootDir()."/../bin/normalize.sh 2> salida.txt&";
//        shell_exec($pythonScript);
        $this->addFlash('sonata_flash_success', 'Elementos normalizados');
        return new RedirectResponse($this->admin->generateUrl(
            'list',
            array('filter' => $this->admin->getFilterParameters())
        ));
    }

}
