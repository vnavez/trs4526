<?php

namespace FrontBundle\Controller;

use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Torrent $torrent
     * @Route ("/delete/{id}", name="torrent_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Torrent $torrent)
    {
        $em = $this->getDoctrine()->getManager();
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
     * @Route ("/changestate/{id}", name="torrent_change_sate")
     * @Method("GET")
     */
    public function ChangeStateAction(Request $request, Torrent $torrent)
    {

    }
}
