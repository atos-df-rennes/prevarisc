<?php

    require './Writer.php';
    $writer = new Writer();
    $inputFileName = './fichier.json';
    $urlPrevarisc = 'http://192.168.7.53/';

    $writer->log("Lecture fichier de configuration '".$inputFileName."' JSON");

        //Recuperation fichier
        if (file_get_contents($inputFileName)) {
            $jsonInput = json_decode(file_get_contents($inputFileName));
            $writer->success('Lecture fichier json OK');

            //Montre a l utilisateur ce qu il a saisie
            $writer->log('Contenu du fichier json :');
            foreach ($jsonInput as $uneFusion) {
                $writer->important('Commune résultante de la fusion : '.$uneFusion->nomCommune.' #  INSEE :'.$uneFusion->numINSEE);
                $writer->important('Commune(s) saisies pour la fusion :');

                $mask = "|%50.50s |%50.50s ||\n";

                $writer->tableLog($mask, 'Nom commune', 'N°INSEE');

                foreach ($uneFusion->listeCommune as $villeAFusionner) {
                    $writer->tableLog($mask, $villeAFusionner->nomCommune, $villeAFusionner->numINSEE);
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://192.168.7.53/fusion-command/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);

            var_dump($response);
            $writer->log($response);
        } else {
            $writer->error('Fichier json introuvable');
        }
