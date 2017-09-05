<?php

namespace FrontBundle\Service;

use FrontBundle\Entity\Server;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class SSH
{

    /**
     * @param Server $server
     * @return SSH2
     * @throws \Exception
     */
    public function connect($server) {
        $connection = new SSH2($server->getHost(), $server->getPort());

        $key = new RSA();
        $key->loadKey(file_get_contents('/var/www/trs/current/app/id_rsa'));
        if (!$connection->login($server->getUsername(), $key))
            throw new \Exception('Unable to connect to server');

        return $connection;
    }

    /**
     * @param SSH2 $connection
     * @param $command
     * @return string
     * @throws \Exception
     */
    public function runCommand($connection, $command)
    {
        $connection->enableQuietMode();
        $result_dio = $connection->exec($command);
        $result_err = $connection->getStdError();
        $connection->disableQuietMode();

        if ($result_err)
            throw new \Exception(trim($result_err));

        return trim($result_dio);
    }

}