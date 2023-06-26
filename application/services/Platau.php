<?php

class Service_Platau
{
    private const HEALTHCHECK_ENDPOINT = 'healthcheck';
    private $platauServiceFilePath;
    private $platauConfigFilePath;
    private $pisteClientId;
    private $pisteClientSecret;

    public function __construct()
    {
        $this->platauServiceFilePath = realpath(PLATAU_PATH.DS.implode(DS, ['src', 'Service', 'PlatauAbstract.php']));
        $this->platauConfigFilePath = realpath(PLATAU_PATH.DS.'config.json');
        [$this->pisteClientId, $this->pisteClientSecret] = $this->getPisteCredentials();
    }

    /**
     * Vérifie la santé de Plat'AU.
     */
    public function executeHealthcheck(): bool
    {
        $pisteToken = $this->requestPisteToken();

        if (null === $pisteToken) {
            return false;
        }

        $platauHealth = $this->requestPlatauHealthcheck($pisteToken);
        $platauHealth = json_decode($platauHealth);

        if (null === $platauHealth) {
            return false;
        }

        if (true !== $platauHealth->etatGeneral) {
            return false;
        }

        if (true !== $platauHealth->etatBdd) {
            return false;
        }

        return true;
    }

    private function getPisteCredentials(): array
    {
        $platauConfigContentAsString = file_get_contents($this->platauConfigFilePath);
        $decodedContent = json_decode($platauConfigContentAsString, true);

        $platauOptions = $decodedContent['platau.options'];

        return [$platauOptions['PISTE_CLIENT_ID'], $platauOptions['PISTE_CLIENT_SECRET']];
    }

    /**
     * Récupère le token PISTE.
     */
    private function requestPisteToken(): ?string
    {
        $url = $this->getConstInFile($this->platauServiceFilePath, 'PISTE_ACCESS_TOKEN_URL');

        if (null === $url) {
            error_log("L'URL de PISTE n'a pas pu être récupérée correctement.");

            return null;
        }

        $platauClient = new Service_PlatauClient();
        $platauClient->addOption(CURLOPT_URL, $url);
        $platauClient->addOption(
            CURLOPT_POSTFIELDS,
            sprintf('scope=openid&grant_type=client_credentials&client_id=%s&client_secret=%s', $this->pisteClientId, $this->pisteClientSecret)
        );

        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $platauClient->getOptions());
        $data = curl_exec($curlHandle);

        if ('' !== ($error = curl_error($curlHandle))) {
            error_log($error);

            return null;
        }

        curl_close($curlHandle);

        $decodedData = json_decode($data, true);

        if (null === $decodedData) {
            return null;
        }

        if (array_key_exists('error', $decodedData)) {
            error_log(sprintf('Erreur lors de la récupération du token PISTE : %s', $decodedData['error']));

            return null;
        }

        return $decodedData['access_token'];
    }

    /**
     * Effectue le healthcheck sur l'API Plat'AU.
     *
     * @return bool|string
     */
    private function requestPlatauHealthcheck(string $pisteToken)
    {
        $url = $this->getConstInFile($this->platauServiceFilePath, 'PLATAU_URL');

        if (null === $url) {
            error_log("L'URL de Plat'AU n'a pas pu être récupérée correctement.");

            return false;
        }

        $url .= self::HEALTHCHECK_ENDPOINT;

        $platauClient = new Service_PlatauClient();
        $platauClient->addOption(CURLOPT_URL, $url);
        $platauClient->addOption(
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                sprintf('Authorization: Bearer %s', $pisteToken),
            ]
        );

        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $platauClient->getOptions());
        $data = curl_exec($curlHandle);

        if ('' !== ($error = curl_error($curlHandle))) {
            error_log($error);

            return false;
        }

        curl_close($curlHandle);

        if ('' === $data) {
            error_log("Le healthcheck n'a rien renvoyé. Vérifiez que l'URL de Plat'AU est correctement renseignée (environnement, version).");
        }

        return $data;
    }

    /**
     * Récupère la valeur d'une constante dans un fichier.
     * e.g. private const PATH = '/home/user/' renvoie /home/user/.
     */
    private function getConstInFile(string $filepath, string $const): ?string
    {
        $fileContent = file($filepath);

        foreach ($fileContent as $line) {
            if (false !== strpos($line, $const)) {
                return explode('\'', $line)[1];
            }
        }

        return null;
    }
}
