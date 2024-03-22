<?php

class Service_Utils_DossiersMaxNumber
{
    private const DEFAULT = 5;

    public static function value(): int
    {
        $envValue = filter_var(getenv('PREVARISC_DOSSIERS_MAX_A_AFFICHER'), FILTER_VALIDATE_INT);

        if (!is_int($envValue)) {
            return self::DEFAULT;
        }

        return $envValue;
    }
}
