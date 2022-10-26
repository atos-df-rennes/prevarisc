<?php

class Service_Platau
{
    private $platauServiceFilePath;
    private const HEALTHCHECK_ENDPOINT = '/healthcheck';

    // FIXME Récupérer les secrets
    public function __construct()
    {
        $this->platauServiceFilePath = realpath(PLATAU_PATH.DS.'src/Service/PlatauAbstract.php');
    }

    /**
     * Vérifie la santé de Plat'AU
     * 
     * @return null|false|string
     */
    public function executeHealthcheck()
    {
        $pisteToken = $this->requestPisteToken();
        $platauHealth = $this->requestPlatauHealthcheck($pisteToken);
    }

    // FIXME Utiliser Guzzle (v6.5.8) ?
    /**
     * Récupère le token PISTE.
     *
     * @return string|bool
     */
    private function requestPisteToken()
    {
        $url = $this->getConstInFile($this->platauServiceFilePath, 'PISTE_ACCESS_TOKEN_URL');

        $curlHandle = curl_init();
        // FIXME Utiliser les secrets PISTE en variable
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => 'scope=openid&grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>',
            CURLOPT_RETURNTRANSFER => 1,
        ];
        curl_setopt_array($curlHandle, $options);

        $data = curl_exec($curlHandle);

        if (false === $data) {
            throw new Exception('Une erreur s\'est produite lors de la récupération du token PISTE.');
        }

        curl_close($curlHandle);

        $decodedData = json_decode($data);

        return $decodedData->access_token;
    }

    /**
     * Effectue le healthcheck sur l'API Plat'AU.
     *
     * @return string|bool
     */
    private function requestPlatauHealthcheck(string $pisteToken)
    {
        $url = $this->getConstInFile($this->platauServiceFilePath, 'PLATAU_URL').self::HEALTHCHECK_ENDPOINT;

        $curlHandle = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                sprintf('Authorization: Bearer %s', $pisteToken),
            ],
            CURLOPT_RETURNTRANSFER => 1,
        ];
        curl_setopt_array($curlHandle, $options);

        $data = curl_exec($curlHandle);
        
        if (false === $data) {
            throw new Exception('Une erreur s\'est produite lors du healthcheck Plat\'AU.');
        }

        curl_close($curlHandle);

        return $data;
    }

    /**
     * Récupère la valeur d'une constante dans un fichier.
     * e.g. private const PATH = '/home/user/' renvoie /home/user/
     */
    private function getConstInFile(string $filepath, string $const): ?string
    {
        $fileContent = file($filepath);

        foreach ($fileContent as $line) {
            if (false !== strpos($line, $const)) {
                $url = explode('\'', $line)[1];

                return $url;
            }
        }

        return null;
    }
}
