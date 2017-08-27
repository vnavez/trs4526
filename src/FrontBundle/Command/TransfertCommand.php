<?php

namespace FrontBundle\Command;

use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Server;
use FrontBundle\Entity\Torrent;
use FrontBundle\Entity\Transfer;
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
        $ssh = $this->getContainer()->get('ssh');

        /** @var Server $server */
        $servers = $em->getRepository('FrontBundle:Server')->findBy(array('active' => 1));
        foreach ($servers as $server) {

            $connection = $ssh->connect($server);
            $output = $ssh->runCommand($connection, 'ps -ax | grep "scp"');
            $output = trim($output);
            if (count(explode("\n", $output)) > 2)
                return;

            $transfer = $em->getRepository('FrontBundle:Transfer')->findOneBy(array('status' => $status->getStatusByCode('transfert'), 'user' => $server->getUser()->getId()));
            if ($transfer) {
                $transfer->setStatus($status->getStatusByCode('error'));
                $em->persist($transfer);
                $em->flush();
            }

            /** @var Transfer $transfer */
            $transfer = $em->getRepository('FrontBundle:Transfer')->findOneBy(array('status' => $status->getStatusByCode('waiting'), 'user' => $server->getUser()->getId()));
            if ($transfer) {
                $transfer->setStatus($status->getStatusByCode('transfert'));
                $em->persist($transfer);
                $em->flush();

                $name = preg_replace('/([^a-zA-Z0-9])/', '\\\$1', $transfer->getTorrent()->getName());
                $name = preg_replace('/\'/', '*', $name);

                $source_file = preg_replace('/([^a-zA-Z0-9])/', '*', $transfer->getTorrent()->getName());

                $ssh->runCommand($connection, 'scp -r vnavez@lw321.ultraseedbox.com:"files/' . $source_file . '" '.$server->getDefaultDirectory().'/' . $name);

                $transfer->setStatus($status->getStatusByCode('done'));
                $em->persist($transfer);
                $em->flush();
            }
        }
    }

}