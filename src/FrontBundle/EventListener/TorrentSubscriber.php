<?php

namespace FrontBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FrontBundle\Entity\Torrent;
use FrontBundle\Entity\Transfer;
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
            'preRemove'
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->sendMessage($args, 'create');
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->sendMessage($args, 'update');
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->sendMessage($args, 'delete');
    }

    public function sendMessage(LifecycleEventArgs $args, $action)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Torrent && !$entity instanceof Transfer) {
            return;
        }

        if ($entity instanceof Transfer)
            $entity = $entity->getTorrent();

        $response = array(
            'id' => $entity->getId(),
            'id_user' => $entity->getUser()->getId(),
            'action' => $action,
            'html' => $action != 'delete' ? $this->container->get('templating')->render('FrontBundle:torrent:torrent_line.html.twig', array('torrent' => $entity, 'user_id' => $entity->getUser()->getId())) : ''
        );

        $pusher = $this->container->get('gos_web_socket.wamp.pusher');

        $pusher->push($response, 'torrent_update');
        $pusher->push($response, 'torrent_update_client', array('user_id' => $entity->getUser()->getId()));

    }

}