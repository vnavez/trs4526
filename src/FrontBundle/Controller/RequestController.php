<?php

namespace FrontBundle\Controller;

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
        $url = $request->get('url');
        $api = $this->get('api');
        $html = $this->get('html');

        $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
        $id = $html->getTorrentId($url);
        $data = $api->download($id);

        $fs = new Filesystem();
        $fs->dumpFile($this->getParameter('torrent_directory') . '/' . $id . '.torrent', $data);

        $torrent = new Torrent($this->getParameter('torrent_directory') . '/' . $id . '.torrent');
        $name = $torrent->name();

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -l');
        $process->run();
        $torrent_list = $process->getOutput();
        $lines = explode("\n", $torrent_list);
        $torrent_id = null;
        foreach ($lines as $line) {
            if (preg_match('#([0-9]+).*' . preg_quote($name) . '#', $line, $matches)) {
                $torrent_id = $matches[1];
            }
        }

        if ($torrent_id)
            $response = new JsonResponse(array('error' => 'Torrent already added'));

        else {
            $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -a ' . $this->getParameter('torrent_directory') . '/' . $id . '.torrent');
            $process->run();
            $response = new JsonResponse(array('success' => true));
        }

        return $response;
    }

}
