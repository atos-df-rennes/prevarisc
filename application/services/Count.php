<?php
    class Service_Count{

        public function getNextCommission($idsCommission, $date, $next_date){

            $DB_dateCommission = new Model_DbTable_DateCommission();

            $ids = (array) $idsCommission;
            $select = "SELECT COUNT(*)
                FROM datecommission d
                LEFT JOIN commission c ON d.COMMISSION_CONCERNE = c.ID_COMMISSION
                WHERE DATE_COMMISSION BETWEEN '".date('Y-m-d', $date)."' AND '".date('Y-m-d', $next_date)."'
                ".([] !== $ids ? 'AND d.COMMISSION_CONCERNE IN ('.implode(',', $ids).')' : '').'
                ORDER BY DATE_COMMISSION, HEUREDEB_COMMISSION';

            $res = $DB_dateCommission->getAdapter()->fetchAll($select);
            return $res;
        }



        /*
        TODO TOPO Lundi
        */
        public function getERPSuivis($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPSuivis($user, true);
        }

        public function getERPOuvertsSousAvisDefavorable($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPOuvertsSousAvisDefavorable($user, true);
        }

        public function getERPOuvertsSousAvisDefavorableSuivis($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPOuvertsSousAvisDefavorableSuivis($user, true);
        }

        public function getERPOuvertsSousAvisDefavorableSurCommune($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPOuvertsSousAvisDefavorableSurCommune($user, true);
        }

        public function getERPSansPreventionniste($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPSansPreventionniste($user, true);
        }

        public function getERPOuvertsSansProchainesVisitePeriodiques($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getERPOuvertsSansProchainesVisitePeriodiques($user, true);
        }

        public function getDossiersSuivisSansAvis($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getDossiersSuivisSansAvis($user, true);
        }

        public function getDossiersSuivisNonVerrouilles($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getDossiersSuivisNonVerrouilles($user, true);
        }

        public function getDossierDateCommissionEchu($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getDossierDateCommissionEchu($user, true);
        }

        public function getDossierAvecAvisDifferer($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getDossierAvecAvisDiffere($user, true);
        }

        public function getCourrierSansReponse($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getCourrierSansReponse($user, true);
        }

        public function getFeeds($user){
            $serviceFeed = new Service_Feed();
            return $serviceFeed->getFeeds($user, null, true);
        }

        public function getLeveePresc($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getLeveePresc($user, true);
        }

        public function getAbsenceQuorum($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getAbsenceQuorum($user, true);
        }

        public function getNpsp($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getNpsp($user, true);
        }

        public function getDossiersPlatAUSansEtablissement($user){
            $serviceDashboard = new Service_Dashboard();
            return $serviceDashboard->getDossiersPlatAUSansEtablissement($user, true);
        }
    }
?>
