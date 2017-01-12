<?php

namespace FrontBundle\Controller;

use FrontBundle\FrontBundle;
use FrontBundle\Service\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class RequestController extends Controller
{
    /**
     * @Route("/send", name="request_send")
     * @Method("POST")
     */
    public function sendAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $url = $request->get('url');
        $api = $this->get('api');
        $html = $this->get('html');

        $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
        $id = $html->getTorrentId($url);

        if ($em->getRepository('FrontBundle:Torrent')->findOneBy(array('idT411' => $id))) {
            return new JsonResponse(array('error' => 'Torrent already added'));
        }

        $data = $api->download($id);

        $fs = new Filesystem();
        $fs->dumpFile($this->getParameter('torrent_directory') . '/' . $id . '.torrent', $data);

        $torrent = new Torrent($this->getParameter('torrent_directory') . '/' . $id . '.torrent');
        $name = $torrent->name();

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -a ' . $this->getParameter('torrent_directory') . '/' . $id . '.torrent');
        $process->run();

        $torrent = new \FrontBundle\Entity\Torrent();
        $torrent->setIdT411($id);
        $torrent->setName($name);
        $torrent->setStatus(1);
        $em->persist($torrent);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

}
