<?php

class Service_Formulaire
{
    public function getAllCapsulesRubrique(): array
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();

        return $modelCapsuleRubrique->fetchAll()->toArray();
    }
}