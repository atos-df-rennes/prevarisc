<?php

class Service_Platau
{
    // TODO Séparer les actions dans des fonctions
    /**
     * @return null|false|string
     */
    public function executeHealthcheck()
    {
        // FIXME Utiliser Guzzle (v6.5.8)
        $platauServiceFile = realpath(PLATAU_PATH.DS.'src/Service/PlatauAbstract.php');
        $platauServiceFileContent = file($platauServiceFile);

        // URLs
        $pisteUrl = null;
        $pisteUrlFound = false;
        $platauUrl = null;
        $platauUrlFound = false;

        // TODO Récupérer les secrets (PISTE_CLIENT_ID, PISTE_CLIENT_SECRET)

        foreach ($platauServiceFileContent as $line) {
            if (!$pisteUrlFound && false !== strpos($line, 'PISTE_ACCESS_TOKEN_URL')) {
                $pisteUrl = $line;
                $pisteUrlFound = true;
            }

            if (!$platauUrlFound && false !== strpos($line, 'PLATAU_URL')) {
                $platauUrl = $line;
                $platauUrlFound = true;
            }
        }

        if (null !== $pisteUrl) {
            $pisteUrl = explode('\'', $pisteUrl)[1];
        }

        $pisteCurlHandle = curl_init();
        // FIXME Récupérer les secrets
        $options = [
            CURLOPT_URL => $pisteUrl,
            CURLOPT_POSTFIELDS => 'scope=openid&grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>',
        ];
        curl_setopt_array($pisteCurlHandle, $options);
        $pisteData = curl_exec($pisteCurlHandle);

        if (false === $pisteData) {
            throw new Exception('Une erreur s\'est produite lors de la récupération du token.');
        }
        curl_close($pisteCurlHandle);

        if (null !== $platauUrl) {
            $platauUrl = explode('\'', $platauUrl)[1];
        }
        
        $platauCurlHandle = curl_init();
        $options = [
            CURLOPT_URL => $platauUrl,
            // FIXME Mettre le token récupéré dans la requête à l'API Piste
            CURLOPT_XOAUTH2_BEARER => $pisteData,
        ];
        curl_setopt($platauCurlHandle, CURLOPT_URL, $platauUrl);
        $platauData = curl_exec($platauCurlHandle);

        if (false === $platauData) {
            throw new Exception('Une erreur s\'est produite lors du healthcheck Plat\'AU.');
        }
        curl_close($platauCurlHandle);
    }
}
