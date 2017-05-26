<?php

namespace FrontBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FrontBundle\Entity\Torrent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TorrentSubscriber implements EventSubscriber {

    /** @var ContainerInterface $container */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->sendMessage($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->sendMessage($args);
    }

    public function sendMessage(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Torrent) {
            return;
        }

        $response[$entity->getId()] = $this->container->get('templating')->render('FrontBundle:torrent:torrent_line.html.twig', array('torrent' => $entity));

        $pusher = $this->container->get('gos_web_socket.wamp.pusher');
        $pusher->push($response, 'torrent_update');

    }

}