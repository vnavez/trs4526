<?php

namespace FrontBundle\Controller;

use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * Torrent controller.
 *
 * @Route("torrent")
 */
class TorrentController extends Controller
{
    /**
     * Lists all torrent entities.
     *
     * @Route("/", name="torrent_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $torrents = $em->getRepository('FrontBundle:Torrent')->findAll();

        return $this->render('FrontBundle:torrent:index.html.twig', array(
            'torrents' => $torrents,
        ));
    }

    /**
     * @Route("/refresh", name="torrent_refresh")
     * @Method("GET")
     */
    public function refreshAction()
    {
        die("ok");
    }

    /**
     * @param Torrent $torrent
     * @Route ("/delete/{id}", name="torrent_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Torrent $torrent)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$torrent->getIdTransmission())
            die(json_encode(array('error' => 'Impossible de trouver l\'ID transmission associée à ce torren')));

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -t '.$torrent->getIdTransmission().' --remove-and-delete');
        $process->run();

        $em->remove($torrent);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            die(json_encode(array('success' => 'Torrent supprimé avec succès')));
        }

        $this->redirectToRoute('torrent_index');
    }

    /**
     * @param Request $request
     * @param Torrent $torrent
     * @Route ("/changestate/{id}", name="torrent_change_state")
     * @Method("GET")
     */
    public function ChangeStateAction(Request $request, Torrent $torrent)
    {
    }
}
