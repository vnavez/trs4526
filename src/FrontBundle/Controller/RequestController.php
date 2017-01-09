<?php

namespace FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class RequestController extends Controller
{
    /**
     * @Route("/send", name="request_send")
     * @Method("GET")
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
        $fs->mkdir($this->getParameter('torrent_directory'));
        $fs->dumpFile($this->getParameter('torrent_directory').'/'.$id.'.torrent', $data);

        $process = new Process('transmission-remote '.$this->getParameter('transmission_host').':'.$this->getParameter('transmission_port').' -n '.$this->getParameter('transmission_login').':'.$this->getParameter('transmission_password').' -a '.$this->getParameter('torrent_directory').'/'.$id.'.torrent');
        $process->run();
    }

}
