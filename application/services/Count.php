<?php
    class Service_Count extends Service_Dashboard{
        public function getNextCommissionCount($user){
            return 0;
        }

        public function getERPSuivisCount($user){
            return $this->getERPSuivis($user, true);
        }

        public function getERPOuvertsSousAvisDefavorableCount($user){
            return $this->getERPOuvertsSousAvisDefavorable($user, true);
        }

        public function getERPOuvertsSousAvisDefavorableSuivisCount($user){
            return $this->getERPOuvertsSousAvisDefavorableSuivis($user, true);
        }

        public function getERPOuvertsSousAvisDefavorableSurCommuneCount($user){
            return $this->getERPOuvertsSousAvisDefavorableSurCommune($user, true);
        }

        public function getERPSansPreventionnisteCount($user){
            return $this->getERPSansPreventionniste($user, true);
        }

        public function getERPOuvertsSansProchainesVisitePeriodiquesCount($user){
            return $this->getERPOuvertsSansProchainesVisitePeriodiques($user, true);
        }

        public function getDossiersSuivisSansAvisCount($user){
            return $this->getDossiersSuivisSansAvis($user, true);
        }

        public function getDossiersSuivisNonVerrouillesCount($user){
            return $this->getDossiersSuivisNonVerrouilles($user, true);
        }

        public function getDossierDateCommissionEchuCount($user){
            return $this->getDossierDateCommissionEchu($user, true);
        }

        public function getDossierAvecAvisDiffererCount($user){
            return $this->getDossierAvecAvisDiffere($user, true);
        }

        public function getCourrierSansReponseCount($user){
            return $this->getCourrierSansReponse($user, true);
        }

        public function getFeedsCount($user){
            $serviceFeed = new Service_Feed();
            return $serviceFeed->getFeeds($user, null, true);
        }

        public function getLeveePrescCount($user){
            return $this->getLeveePresc($user, true);
        }

        public function getAbsenceQuorumCount($user){
            return $this->getAbsenceQuorum($user, true);
        }

        public function getNpspCount($user){
            return $this->getNpsp($user, true);
        }

        public function getDossiersPlatAUSansEtablissementCount($user){
            return $this->getDossiersPlatAUSansEtablissement($user, true);
        }

        public function getDossierAvecAvisDiffereCount($user)
        {
            return 0;
        }
    }
?>
