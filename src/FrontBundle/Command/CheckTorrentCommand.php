<?php

namespace FrontBundle\Command;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FrontBundle\Entity\Torrent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CheckTorrentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('torrent:check')
            ->setDescription('Check torrent status');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $status = $this->getContainer()->get('status');

        $process = new Process('transmission-remote ' . $this->getContainer()->getParameter('transmission_host') . ':' . $this->getContainer()->getParameter('transmission_port') . ' -n ' . $this->getContainer()->getParameter('transmission_login') . ':' . $this->getContainer()->getParameter('transmission_password') . ' -l');
        $process->run();

        $output = $process->getOutput();
        $lines = explode("\n", $output);
        $torrents = array();
        $k = 0;
        $headers = array();

        foreach ($lines as $line) {
            $line = trim($line);
            $data = preg_split('/[\ ]{2,}/', $line);
            foreach ($data as $y => $d)
                $data[$y] = trim($d);
            if (!$k)
                $headers = $data;
            elseif (preg_match('/^([0-9]+)/', $line, $matches)) {
                $extra = count($data) - 9;
                if ($extra) {
                    for ($i = 0; $i < $extra; $i++) {
                        $data[8] .= ' '.$data[8+$i+1];
                        unset($data[8+$i+1]);
                    }
                }
                foreach ($data as $x => $d) {
                    $torrents[$matches[1]][$headers[$x]] = $d;
                }
            }
            $k++;
        }

        $ids = array();
        foreach ($torrents as $line) {
            array_push($ids, $line['ID']);
            /** @var Torrent $torrent */
            $torrent = $em->getRepository('FrontBundle:Torrent')->findOneBy(array('idTransmission' => $line['ID']));
            if (!$torrent){
                $obj = new Torrent();
                $obj->setIdTransmission(intval($line['ID']));
                $obj->setName($line['Name']);
                $obj->setStatus($status->getStatusByCode('new'));
                $obj->setDateAdd(new \DateTime('now'));
                $obj->setDateUpd(new \DateTime('now'));
                $em->persist($obj);
            } else {

                if (in_array($torrent->getStatus(), array(
                    $status->getStatusByCode('new'),
                    $status->getStatusByCode('downloaded'),
                    $status->getStatusByCode('pause'),
                    $status->getStatusByCode('progressing'),
                ))) {
                    if ($torrent->getName() != $line['Name']) {
                        $torrent->setName($line['Name']);
                        $torrent->setStatus($status->getStatusByCode('new'));
                    } elseif ($line['Status'] == 'Stopped')
                        $torrent->setStatus($status->getStatusByCode('pause'));
                    elseif ($line['Done'] == '100%')
                        $torrent->setStatus($status->getStatusByCode('downloaded'));
                    else
                        $torrent->setStatus($status->getStatusByCode('progressing'));
                }
                $torrent->setRatio($line['Ratio']);
                $torrent->setPercent(floatval($line['Done']));
                $torrent->setDateUpd(new \DateTime('now'));
                $em->persist($torrent);
            }
        }

        $em->flush();

        $torrents = $em->getRepository('FrontBundle:Torrent')->getUnavailableTorrents($ids);
        foreach ($torrents as $torrent) {
            $em->remove($torrent);
        }
        $em->flush();

    }

}
