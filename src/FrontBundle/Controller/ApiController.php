<?php

namespace FrontBundle\Controller;

use FrontBundle\Exception\ApiException;
use FrontBundle\FrontBundle;
use FrontBundle\Service\Api;
use FrontBundle\Service\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get; // N'oublons pas d'inclure Get
use FOS\RestBundle\Controller\Annotations\Post; // N'oublons pas d'inclure Get

class ApiController extends Controller
{

    /**
     * @Get("/places")
     */
    public function getTestsAction(Request $request) {
        return new JsonResponse([]);
    }

    /**
     * @Get("/place/id")
     */
    public function getTestAction(Request $request) {
        return new JsonResponse([]);
    }

}