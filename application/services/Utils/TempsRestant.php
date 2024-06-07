<?php

class Service_Utils_TempsRestant
{
    public static function getCouleurTempsRestant($temps_restant):string {
  
        if (empty($temps_restant)) {
            return "";
        }

        $temps = explode(' et ', $temps_restant);
        $mois = 0;

        foreach ($temps as $part){
            if(strpos($part,'mois')!== false){
                $mois =(int) explode(' ', $part)[0];
            }
        }
        $couleur = "";
        if ($mois > 6) {
            $couleur = "success"; // une couleur verte vu qu'il reste plus que 6 mois encore
        } elseif ($mois <= 6 && $mois > 3) {
            $couleur = "warning"; // une couleur orange vu qu'il reste plus que 3 mois 
        } else {
            $couleur = "important"; // une couleur rouge Attention : il reste au moins 3 mois
        }
        return  $couleur;
    }

}