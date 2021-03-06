<?php

namespace FrontBundle\Controller;

use FrontBundle\Exception\ApiException;
use FrontBundle\FrontBundle;
use FrontBundle\Service\Api;
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
     */
    public function sendAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $url = $request->get('url');
        $token = $request->get('token');
        $id = intval($request->get('id'));
        $type = $request->get('type');
        /** @var Api $api */
        $api = $this->get('api');
        $html = $this->get('html');
        $user = $this->get('user');
        $status = $this->get('status');

        if (!$token)
            return new JsonResponse(array('error' => 'Please use your token'));

        if (!in_array($type, array('t411', 't9')))
            return new JsonResponse(array('error' => 'No support type'));

        $user = $user->isAuthorizedToken($token);
        if (!$user)
            return new JsonResponse(array('error' => 'Please use your real token'));

        $apiState = true;
        if ($type == 't411') {
            try {
                $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
            } catch (ApiException $e) {
                $apiState = false;
            }
        }

        if (!$id && $type == 't411')
            $id = $html->getTorrentId($url);
        elseif (!$id)
            $id = uniqid();

        if ($em->getRepository('FrontBundle:Torrent')->findOneBy(array('idT411' => $id))) {
            return new JsonResponse(array('error' => 'Torrent already added'));
        }

        if ($type == 't411') {
            $data = $api->download($id, $apiState, $this->getParameter('torrent_directory').'/cookie');
            if ($apiState)
                $details = $api->getDetails($id);
        } else {
            $data = $api->downloadOther($url);
        }

        $fs = new Filesystem();
        $fs->dumpFile($this->getParameter('torrent_directory') . '/' . $id . '.torrent', $data);

        $torrent = new Torrent($this->getParameter('torrent_directory') . '/' . $id . '.torrent');
        $name = $torrent->name();

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -a ' . $this->getParameter('torrent_directory') . '/' . $id . '.torrent');
        $process->run();

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -l');
        $process->run();
        $result = $process->getOutput();

        if (!preg_match('#([0-9]+).*'.preg_quote($name).'#', $result, $matches)) {
            return new JsonResponse(array('error' => 'Unable to get torrent in list'));
        }

        $torrent = new \FrontBundle\Entity\Torrent();
        $torrent->setIdT411($id);
        $torrent->setIdTransmission($matches[1]);
        $torrent->setName($name);
        $torrent->setStatus($status->getStatusByCode('new'));
        $torrent->setUser($user);
        $torrent->setCategory(isset($details) ? $details->categoryname : 'Unknown');
        $torrent->setDateAdd(new \DateTime('now'));
        $torrent->setDateUpd(new \DateTime('now'));
        $torrent->setFile($id.'.torrent');
        $em->persist($torrent);
        $em->flush();

        if($request->get('ajax')) {
            return new JsonResponse(array('success' => true));
        }

        return $this->redirectToRoute('torrent_index');
    }

}
