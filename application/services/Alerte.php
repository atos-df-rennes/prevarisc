<?php

class Service_Alerte
{
    public const ALERTE_LINK = '<a data-value="%s"%s class="pull-right alerte-link"><i class="icon-bell icon-black"></i>Alerter</a>';

    public function getLink($idTypeChangement, $idEtablissement = null): string
    {
        $etabData = '';
        if ($idEtablissement) {
            $etabData = sprintf(' data-ets="%s"', $idEtablissement);
        }

        return sprintf(self::ALERTE_LINK, $idTypeChangement, $etabData);
    }
}
