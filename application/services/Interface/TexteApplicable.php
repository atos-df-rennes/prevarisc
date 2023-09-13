<?php

interface Service_Interface_TexteApplicable
{
    public function getAllTextesApplicables(int $id): array;

    public function saveTextesApplicables($id_etablissement, array $textes_applicables);
}
