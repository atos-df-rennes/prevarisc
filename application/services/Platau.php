<?php

class Service_Platau
{
    /**
     * @return null|false|string
     */
    public function executeHealthcheck()
    {
        $currentPath = readlink('/home/prv/current');
        $command = "/usr/bin/php-platau {$currentPath}/prevarisc-passerelle-platau/bin/platau --config=../../prevarisc-passerelle-platau/config.json healthcheck";
        $escapedCommand = escapeshellcmd($command);

        return shell_exec($escapedCommand);
    }
}
