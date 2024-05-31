<?php

class Service_Utils_Date
{
    public static function convertToMySQL(string $date): ?string
    {
        if ('' === $date || '0' === $date) {
            return null;
        }
        [$jour, $mois, $annee] = explode('/', $date);

        return implode('-', [$annee, $mois, $jour]);
    }

    public static function convertFromMySQL(string $date): ?string
    {
        if ('' === $date || '0' === $date) {
            return null;
        }
        [$jour, $mois, $annee] = explode('-', $date);

        return implode('/', [$annee, $mois, $jour]);
    }

    public static function formatDateWithDayName(string $date): ?string
    {
        if ('' === $date || '0' === $date) {
            return null;
        }
        $zendDate = new Zend_Date($date, 'dd/MM/yyyy');

        return $zendDate->get(Zend_Date::WEEKDAY.' '.Zend_Date::DAY.' '.Zend_Date::MONTH_NAME.' '.Zend_Date::YEAR);
    }
}
