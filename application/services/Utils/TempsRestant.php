<?php

class Service_Utils_TempsRestant
{
    public const SEUIL_1 = 6;
    public const SEUIL_2 = 3;

    public static function getCouleurTempsRestant($temps_restant): string
    {
        if (empty($temps_restant)) {
            return '';
        }

        $temps = explode(' et ', $temps_restant);
        $mois = 0;

        foreach ($temps as $part) {
            if (false !== strpos($part, 'mois')) {
                $mois = (int) explode(' ', $part)[0];
            }
        }
        $couleur = '';
        if ($mois > self::SEUIL_1) {
            $couleur = 'success'; // une couleur verte vu qu'il reste plus que 6 mois encore
        } elseif ($mois > self::SEUIL_2) {
            $couleur = 'warning'; // une couleur orange vu qu'il reste plus que 3 mois
        } else {
            $couleur = 'important'; // une couleur rouge Attention : il reste au moins 3 mois
        }

        return $couleur;
    }
}
