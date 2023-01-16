<?php

class Service_Platau
{
    private const HEALTHCHECK_ENDPOINT = '/healthcheck';
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
     *
     * @return null|false|string
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

    private function getPisteCredentials()
    {
        $platauConfigContentAsString = file_get_contents($this->platauConfigFilePath);
        $decodedContent = json_decode($platauConfigContentAsString, true);

        $platauOptions = $decodedContent['platau.options'];

        return [$platauOptions['PISTE_CLIENT_ID'], $platauOptions['PISTE_CLIENT_SECRET']];
    }

    /**
     * Récupère le token PISTE.
     *
     * @return null|bool|string
     */
    private function requestPisteToken()
    {
        $url = $this->getConstInFile($this->platauServiceFilePath, 'PISTE_ACCESS_TOKEN_URL');

        $curlHandle = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => sprintf('scope=openid&grant_type=client_credentials&client_id=%s&client_secret=%s', $this->pisteClientId, $this->pisteClientSecret),
            CURLOPT_RETURNTRANSFER => 1,
        ];
        curl_setopt_array($curlHandle, $options);

        $data = curl_exec($curlHandle);

        if (false === $data) {
            error_log('Une erreur s\'est produite lors de la récupération du token PISTE.');
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
            error_log('Une erreur s\'est produite lors du healthcheck Plat\'AU.');
        }

        curl_close($curlHandle);

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
