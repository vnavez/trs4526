<?php

namespace FrontBundle\Command;

use Doctrine\ORM\EntityManager;
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

        // Check if curl is running on remote server
        $process = new Process('ssh admin@192.168.0.16 \'ps -ax | grep "scp"\'');
        $process->run();

        $output = trim($process->getOutput());
        if (count(explode("\n", $output)) > 2)
            return;

        $transfer = $em->getRepository('FrontBundle:Transfer')->findOneBy(array('status' => $status->getStatusByCode('transfert')));
        if ($transfer) {
            $transfer->setStatus($status->getStatusByCode('error'));
            $em->persist($transfer);
            $em->flush();
        }

        /** @var Transfer $transfer */
        $transfer = $em->getRepository('FrontBundle:Transfer')->findOneBy(array('status' => $status->getStatusByCode('waiting')));
        $transfer->setStatus($status->getStatusByCode('transfert'));
        $em->persist($transfer);
        $em->flush();

        $name = preg_replace('/([^a-zA-Z0-9])/', '\\\$1', $transfer->getTorrent()->getName());
        $name = preg_replace('/\'/', '*', $name);

        $source_file = preg_replace('/([^a-zA-Z0-9])/', '*', $transfer->getTorrent()->getName());

        $process = new Process('ssh admin@192.168.0.16 \'scp -r vnavez@lw321.ultraseedbox.com:"files/'.$source_file.'" /volume2/Transfert/'.$name.'\'');
        $process->setTimeout(7200);
        $process->run();

        $transfer->setStatus($status->getStatusByCode('done'));
        $em->persist($transfer);
        $em->flush();

    }

}