<?php

namespace FrontBundle\Controller;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Torrent;
use FrontBundle\Entity\Transfer;
use FrontBundle\Form\TorrentUpload;
use FrontBundle\Service\Api;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * Torrent controller.
 *
 * @Route("")
 */
class TorrentController extends Controller
{
    /**
     * Lists all torrent entities.
     *
     * @Route("/", name="torrent_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $torrent = new Torrent();
        $form = $this->createForm(TorrentUpload::class, $torrent);
        $form->handleRequest($request);

        $torrents = $em->getRepository('FrontBundle:Torrent')->findAll();

        return $this->render('FrontBundle:torrent:index.html.twig', array(
            'torrents' => $torrents,
            'user_id' => $this->getUser() ? $this->getUser()->getId() : null,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/search", name="torrent_search")
     * @Method("GET")
     */
    public function searchAction(Request $request)
    {
        $search = $request->get('search');
        /** @var Api $api */
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
     * @Route("/upload", name="torrent_upload")
     */
    public function uploadAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $torrent = new Torrent();
        $form = $this->createForm(TorrentUpload::class, $torrent);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $file = $torrent->getFile();

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('torrent_directory'),
                    $fileName
                );

                $t = new \FrontBundle\Service\Torrent($this->getParameter('torrent_directory') . '/' . $fileName);
                $name = $t->name();

                $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -a ' . $this->getParameter('torrent_directory') . '/' . $fileName);
                $process->run();

                $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -l');
                $process->run();
                $result = $process->getOutput();

                if (!preg_match('#([0-9]+).*' . preg_quote($name) . '#', $result, $matches)) {
                    $this->addFlash('error', 'Unable to get torrent in list');
                } else {
                    $status = $this->get('status');
                    $torrent = new Torrent();
                    $torrent->setIdTransmission($matches[1]);
                    $torrent->setName($name);
                    $torrent->setStatus($status->getStatusByCode('new'));
                    $torrent->setUser($this->getUser());
                    $torrent->setCategory(isset($details) ? $details->categoryname : 'Unknown');
                    $torrent->setDateAdd(new \DateTime('now'));
                    $torrent->setDateUpd(new \DateTime('now'));
                    $torrent->setFile($fileName);
                    $em->persist($torrent);
                    $em->flush();
                }
            } else {
                $this->addFlash('error', 'Please insert a correct torrent file');
            }

            return $this->redirect($this->generateUrl('torrent_index'));
        }

        if ($request->isXmlHttpRequest()) {
            $data = $this->renderView('FrontBundle:torrent:upload.html.twig', array('form' => $form->createView()));
            return new JsonResponse(array('success' => true, 'data' => $data), 200);
        }

        return $this->redirect($this->generateUrl('torrent_index'));

    }

    /**
     * @Route ("/top100", name="torrent_top100")
     * @Method("GET")
     */
    public function top100Action(Request $request)
    {
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
    public function todayAction(Request $request)
    {
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

        $process = new Process('transmission-remote ' . $this->getParameter('transmission_host') . ':' . $this->getParameter('transmission_port') . ' -n ' . $this->getParameter('transmission_login') . ':' . $this->getParameter('transmission_password') . ' -t ' . $torrent->getIdTransmission() . ' --remove-and-delete');
        $process->run();

        $em->remove($torrent);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            die(json_encode(array('success' => 'Torrent supprimé avec succès')));
        }

        return $this->redirectToRoute('torrent_index');
    }

    /**
     * @Route ("/popup-transfert/{id}", name="torrent_popup_transfert")
     * @Method("GET")
     */
    public function PopupTransferAction(Request $request, Torrent $torrent)
    {

        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getEntityManager();
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

        if (!$torrent->getTransfers()) {
            $transfer = new Transfer();
            $transfer->setStatus($status->getStatusByCode('waiting'));
            $transfer->setTorrent($torrent);
            $transfer->setUser($this->getUser());
            $em->persist($transfer);
        } else {
            /** @var Transfer $transfer */
            foreach ($torrent->getTransfers() as $transfer) {
                if ($transfer->getUser()->getId() == $this->getUser()->getId()) {
                    $transfer->setStatus($status->getStatusByCode('waiting'));
                    $em->persist($transfer);
                }
            }
        }

        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array('success' => true), 200);
        }

        return $this->redirectToRoute('torrent_index');
    }

    /**
     * @Route("/torrent_compress_files/{id}", name="torrent_compress_files")
     * @Method("GET")
     * @param Request $request
     * @param Torrent $torrent
     * @return string
     */
    public function CompressFilesAction(Request $request, Torrent $torrent) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getEntityManager();

        $torrent->setCompressState(1);
        $em->persist($torrent);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            die;
        }

        return $this->redirectToRoute('torrent_index');
    }


    public static function torrentSort($a, $b)
    {
        return ($a->seeders > $b->seeders) ? -1 : 1;
    }
}
