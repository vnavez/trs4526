<?php

namespace FrontBundle\Controller;

use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/search", name="torrent_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $search = $request->get('search');
        $api = $this->get('api');
        $torrents = array();
        $count = 0;

        if ($search) {
            $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
            $results = $api->search($search);
            $torrents = $results->torrents;
            $count = $results->total;

            usort($torrents, array($this, 'torrentSort'));
        }

        return $this->render('FrontBundle:torrent:search.html.twig', array(
            'search' => $search,
            'count_search' => $count,
            'torrents' => $torrents
        ));
    }

    /**
     * @Route ("/top100", name="torrent_top100")
     * @Method("GET")
     */
    public function top100Action(Request $request) {
        $api = $this->get('api');
        $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
        $torrents = $api->top100();

        return $this->render('FrontBundle:torrent:top.html.twig', array(
            'torrents' => $torrents
        ));
    }

    /**
     * @Route ("/today", name="torrent_today")
     * @Method("GET")
     */
    public function todayAction(Request $request) {
        $api = $this->get('api');
        $api->auth($this->getParameter('api_login'), $this->getParameter('api_password'));
        $torrents = $api->today();

        return $this->render('FrontBundle:torrent:top.html.twig', array(
            'torrents' => $torrents
        ));
    }

    /**
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

        return $this->redirectToRoute('torrent_index');
    }

    /**
     * @Route ("/enable-transfert/{id}", name="torrent_enable_transfert")
     * @Method("GET")
     */
    public function ChangeStateAction(Request $request, Torrent $torrent)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getEntityManager();

        $status = $this->get('status');
        $torrent->setStatus($status->getStatusByCode('waiting'));
        $em->persist($torrent);
        $em->flush();

        if($request->isXmlHttpRequest()) {
            $data = $this->renderView('FrontBundle:torrent:torrent_line.html.twig', array('torrent' => $torrent));
            return new JsonResponse(array('success' => true, 'data' => $data), 200);
        }

        return $this->redirectToRoute('torrent_index');
    }


    public static function torrentSort($a, $b) {
        return ($a->seeders > $b->seeders) ? -1 : 1;
    }
}
