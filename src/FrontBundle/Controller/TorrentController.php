<?php

namespace FrontBundle\Controller;

use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * Finds and displays a torrent entity.
     *
     * @Route("/{id}", name="torrent_show")
     * @Method("GET")
     */
    public function showAction(Torrent $torrent)
    {

        return $this->render('FrontBundle:torrent:show.html.twig', array(
            'torrent' => $torrent,
        ));
    }
}
