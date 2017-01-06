<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RequestController extends Controller
{
    /**
     * @Route("/send")
     */
    public function sendAction()
    {
        return $this->render('FrontBundle:RequestController:send.html.twig', array(
            // ...
        ));
    }

}
