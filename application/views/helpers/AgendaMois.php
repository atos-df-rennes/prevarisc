<?php

class View_Helper_AgendaMois
{
    public function agendaMois($donnees, $mois = null, $annee = null)
    {
        //S'il y à des parametres on les prend sinon on prend le mois et l'année actuel.
        if (!$mois) {
            $num_mois = date('n');
        } else {
            $num_mois = $mois;
        }

        if (!$annee) {
            $num_an = date('Y');
        } else {
            $num_an = $annee;
        }

        if ($num_mois < 1) {
            $num_mois = 12;
            $num_an = $num_an - 1;
        } elseif ($num_mois > 12) {
            $num_mois = 1;
            $num_an = $num_an + 1;
        }

        // nombre de jours dans le mois et numero du premier jour du mois
        $int_nbj = date('t', mktime(0, 0, 0, $num_mois, 1, $num_an));
        $int_premj = date('w', mktime(0, 0, 0, $num_mois, 1, $num_an));

        // tableau des jours, tableau des mois...
        $tab_jours = ['', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'];

        $int_nbjAV = date('t', mktime(0, 0, 0, ($num_mois - 1 < 1) ? 12 : $num_mois - 1, 1, $num_an)); // nb de jours du moi d'avant

        // on affiche les jours du mois et aussi les jours du mois avant/apres, on les indique par une * a l'affichage on modifie l'apparence des chiffres *
        $tab_cal = [[], [], [], [], [], []]; // tab_cal[Semaine][Jour de la semaine]
        $int_premj = ($int_premj == 0) ? 7 : $int_premj;
        $t = 1;
        $p = '';
        for ($i = 0; $i < 6; ++$i) {
            for ($j = 0; $j < 7; ++$j) {
                if ($j + 1 == $int_premj && $t == 1) {
                    $tab_cal[$i][$j] = $t;
                    ++$t;
                } // on stocke le premier jour du mois
                elseif ($t > 1 && $t <= $int_nbj) {
                    $tab_cal[$i][$j] = $p.$t;
                    ++$t;
                } // on incremente a chaque fois...
                elseif ($t > $int_nbj) {
                    $p = '*';
                    $tab_cal[$i][$j] = $p.'1';
                    $t = 2;
                } // on a mis tout les numeros de ce mois, on commence a mettre ceux du suivant
                elseif ($t == 1) {
                    $tab_cal[$i][$j] = '*'.($int_nbjAV - ($int_premj - ($j + 1)) + 1);
                } // on a pas encore mis les num du mois, on met ceux de celui d'avant
            }
        }

        //Generation select pour le choix des mois
        $result = '';

        echo '
            </form>
            <table class="agendaMois">
                <tr class="jours_agenda">
        ';
        for ($i = 1; $i <= 7; ++$i) {
            $result .= '<td>'.$tab_jours[$i].'</td>';
        }
        $result .= '</tr>';

        for ($i = 0; $i < 6; ++$i) {
            $result .= '<tr class="semaine_agenda" id="semaine_agenda_'.$i.'">';
            for ($j = 0; $j < 7; ++$j) {
                //Cette condition permet de définir si le jour fait parti du mois en cours ou pas.
                //Si le jour ne fait pas parti du mois il est affiché en gris clair sinon en noir.
                if (strpos($tab_cal[$i][$j], '*') !== false) {
                    $val = '<font color="#aaaaaa">'.str_replace('*', '', $tab_cal[$i][$j]).'</font>';
                    $id = 0;
                } else {
                    $val = $tab_cal[$i][$j];
                    $id = $val;
                }
                $type = '';
                $img = '';
                //Permet d'afficher le jour d'aujourd'hui en gris clair
                if ($num_mois == date('n') && $num_an == date('Y') && $tab_cal[$i][$j] == date('j')) {
                    $type = ' today';
                    foreach ($donnees as $boucle1 => $boucle2) {
                        foreach ($boucle2 as $libelle => $value) {
                            if ($libelle == 'DATE_COMMISSION') {
                                $date = new Zend_Date($value, Zend_Date::DATES);
                                if ($date->get(Zend_Date::DAY) == $val) {
                                    //Si un évenement du tableau correspond à la date alors on affiche qqch.
                                    $type .= ' commission';
                                }
                            }
                        }
                    }
                } else {
                    foreach ($donnees as $boucle1 => $boucle2) {
                        foreach ($boucle2 as $libelle => $value) {
                            if ($libelle == 'DATE_COMMISSION') {
                                $date = new Zend_Date($value, Zend_Date::DATES);
                                if ($date->get(Zend_Date::DAY) == $val) {
                                    //Si un évenement du tableau correspond à la date alors on affiche qqch.
                                    $type = ' commission';
                                }
                            }
                        }
                    }
                }
                $result .= "<td class='case_agenda ".$type."'  id='".$id."'>".$val.'<br/>'.$img.'</td>';
            }
            $result .= '</tr>';
        }
        $result .= '
            </table>
        ';
        echo  $result;
    }
}
