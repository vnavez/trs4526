<?php

namespace FrontBundle\Service;

use FrontBundle\Entity\Server;
use FrontBundle\Exception\ApiException;

class SSH
{

    /**
     * @param Server $server
     * @return resource
     */
    public function connect($server) {
        $connection = ssh2_connect($server->getHost(), $server->getPort());
        ssh2_auth_pubkey_file($connection, $server->getUsername(), '/app/id_rsa.pub', '/app/id_rsa');
        return $connection;
    }

    /**
     * @param $connection
     * @param $command
     * @return string
     * @throws \Exception
     */
    public function runCommand($connection, $command)
    {
        $stdout_stream = ssh2_exec($connection, $command);

        $err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
        $dio_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDIO);

        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);

        $result_err = stream_get_contents($err_stream);
        $result_dio = stream_get_contents($dio_stream);

        if ($result_err)
            throw new \Exception(trim($result_err));

        return trim($result_dio);
    }

}