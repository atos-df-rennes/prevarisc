<?php

class Service_Utils_TempsRestant
{
    public const SEUIL_OK = 6;
    public const SEUIL_WARN = 3;

    public static function calculate(string $limitDate): string
    {
        $limitDate = new DateTime($limitDate);
        $now = new DateTime();

        $diff = $limitDate->diff($now);

        $readableDiff = '';

        if ($diff->y > 0) {
            $ans = $diff->y > 1 ? 'ans' : 'an';
            $readableDiff .= "{$diff->y} {$ans}";
        }

        if ($diff->m > 0) {
            if ($readableDiff !== '') {
                $readableDiff .= ' et ';
            }
            $readableDiff .= "{$diff->m} mois";
        }

        if ($diff->d > 0) {
            if ($readableDiff !== '') {
                $readableDiff .= ' et ';
            }
            $readableDiff .= "{$diff->d} jours";
        }

        return $readableDiff;
    }

    public static function getCouleurTempsRestant(string $limitDate): string
    {
        $limitDate = new DateTime($limitDate);
        $now = new DateTime();

        $diff = $limitDate->diff($now);

        if ($diff->y > 0 || $diff->m > self::SEUIL_OK) {
            return 'success';
        }

        if ($diff->m > self::SEUIL_WARN) {
            return 'warning';
        }

        return 'important';
    }
}
