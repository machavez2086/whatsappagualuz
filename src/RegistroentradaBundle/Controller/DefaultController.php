<?php

namespace RegistroentradaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('RegistroentradaBundle:Default:index.html.twig');
    }
}
