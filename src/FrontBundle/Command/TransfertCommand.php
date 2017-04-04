<?php

namespace FrontBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TransfertCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('torrent:transfert')
            ->setDescription('Process to transfert files');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 0);

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $status = $this->getContainer()->get('status');

        // Check if curl is running on remote server
        $process = new Process('ssh admin@192.168.0.16 \'ps -ax | grep "scp"\'');
        $process->run();

        $output = trim($process->getOutput());
        if (count(explode("\n", $output)) > 2)
            return;

        $torrent = $em->getRepository('FrontBundle:Torrent')->findOneBy(array('status' => $status->getStatusByCode('transfert')));
        if ($torrent) {
            $torrent->setStatus($status->getStatusByCode('error'));
            $em->persist($torrent);
            $em->flush();
        }

        /** @var Torrent $torrent */
        $torrent = $em->getRepository('FrontBundle:Torrent')->findOneBy(array('status' => $status->getStatusByCode('downloaded')));
        $torrent->setStatus($status->getStatusByCode('transfert'));
        $em->persist($torrent);
        $em->flush();

        $name = preg_replace('/([^a-zA-Z0-9])/', '\\\$1', $torrent->getName());
        $name = preg_replace('/\'/', '*', $name);

        $process = new Process('ssh admin@192.168.0.16 \'scp -r vnavez@lw321.ultraseedbox.com:"files/'.$name.'" /volume2/Transfert/'.$name.'\'');
        $process->run();

        $torrent->setStatus($status->getStatusByCode('done'));
        $em->persist($torrent);
        $em->flush();

    }

}