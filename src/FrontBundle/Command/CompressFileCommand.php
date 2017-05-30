<?php

namespace FrontBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Torrent;
use Ratchet\Wamp\Topic;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Constraints\Date;

class CompressFileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('torrent:compress')
            ->setDescription('Compress torrent');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 0);

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        /** @var Torrent $torrent */
        $torrent = $em->getRepository('FrontBundle:Torrent')->findOneBy(array('compress_state' => 1));

        if (!$torrent)
            return;

        $torrent->setCompressState(2);
        $em->persist($torrent);
        $em->flush();

        $realName = preg_replace('#([^a-zA-Z0-9\.])#', "\\\\\\1", $torrent->getName());
        $process = new Process('ssh vnavez@lw321.ultraseedbox.com \'tar -cf files/torrent-'.$torrent->getId().'.tar files/'.$realName.'\'');
        $process->setTimeout(7200);
        $process->run();

        $now = new \DateTime('now');
        $torrent->setCompressState(0);
        $torrent->setDateGenerated($now);
        $torrent->setLinkGenerated('https://' . $this->getContainer()->getParameter('transmission_host') . '/~vnavez/files/torrent-' . $torrent->getId().'.tar');
        $em->persist($torrent);
        $em->flush();

    }

}