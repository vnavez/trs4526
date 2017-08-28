<?php

namespace FrontBundle\Controller;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FrontBundle\Entity\Server;
use FrontBundle\Entity\Torrent;
use FrontBundle\Form\ServerForm;
use FrontBundle\Form\ServerType;
use FrontBundle\Service\Api;
use Ratchet\Wamp\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Torrent controller.
 *
 * @Route("/server")
 */
class ServerController extends Controller
{

    /**
     * Lists all torrent entities.
     *
     * @Route("/edit", name="server_edit")
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->getDoctrine()->getManager();

        /** @var Server $server */
        $server = $em->getRepository('FrontBundle:Server')->findOneBy(array('user' => $user));
        if (!$server)
            $server = new Server();

        $form = $this->createForm(ServerType::class, $server);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $server->setUser($user);
                $em->persist($server);
                $em->flush();

                $this->addFlash('success', 'Paramètres sauvegardés');

            } else {
                $this->addFlash('error', 'Veuillez vérifier tous vos champs');
            }

            return $this->redirect($this->generateUrl('server_edit'));
        }

        return $this->render('FrontBundle:server:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/test", name="server_test")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function testAction(Request $request)
    {

        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $em = $this->getDoctrine()->getManager();

        /** @var Server $server */
        $server = $em->getRepository('FrontBundle:Server')->findOneBy(array('user' => $user));
        if (!$server)
            throw new Exception('Please save configuration before test');

        $ssh = $this->get('ssh');

        try {
            $connection = $ssh->connect($server);

            $ssh->runCommand($connection, 'touch ' . $server->getDefaultDirectory() . '/test.file && rm ' . $server->getDefaultDirectory() . '/test.file');
            $ssh->runCommand($connection, 'scp -r vnavez@lw321.ultraseedbox.com:"files/DONTREMOVE" '.$server->getDefaultDirectory().'/DONTREMOVE');

        } catch (\Exception $e) {
            $server->setActive(0);
            $em->persist($server);
            $em->flush();
            return new JsonResponse(array('error' => $e->getMessage()));
        }

        $server->setActive(1);
        $em->persist($server);
        $em->flush();

        return new JsonResponse(array('success' => true));


    }

}