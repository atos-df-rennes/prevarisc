<?php

class Service_Notification
{
    public const DASHBOARD_DOSSIER_SESSION_NAMESPACE = 'DERNIERE_VISITE_DASHBOARD';

    public const DOSSIER_PIECES_SESSION_NAMESPACE = 'DERNIERE_VISITE_PIECES_DOSSIER';

//    private const NAMESPACE_MAP = [
//        self::DASHBOARD_DOSSIER_SESSION_NAMESPACE => 'DERNIERE_VISITE_DASHBOARD',
//        self::DOSSIER_PIECES_SESSION_NAMESPACE => 'DERNIERE_VISITE_PIECES_DOSSIER',
//    ];

    /** @var \Zend_Db_Table_Row_Abstract|null */
    private $utilisateur;

    public function __construct() {
        $modelUtilisateur = new Model_DbTable_Utilisateur();
        $this->utilisateur = $modelUtilisateur->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'])->current();
    }

    public function getLastPageVisitDate(string $sessionNamespace): string
    {
        return $this->utilisateur[$sessionNamespace];
    }

    public function setLastPageVisitDate(string $sessionNamespace): void
    {
//        $derniereDateVisitePageSession = new Zend_Session_Namespace($sessionNamespace);
//        $derniereDateVisitePageSession->date = date('Y-m-d H:i:s');

        $this->utilisateur->{$sessionNamespace} = date('Y-m-d H:i:s');
        $this->utilisateur->save();
    }

    /**
     * Vérifie si un élément Plat'AU est nouveau. (i.e. Ajouté via une notification sans que l'utilisateur ne l'ait consulté).
     */
    public function isNew(array $element, string $elementSessionNamespace): bool
    {
        if (null === $element['DATE_NOTIFICATION']) {
            return false;
        }

        return $element['DATE_NOTIFICATION'] >= $this->getLastPageVisitDate($elementSessionNamespace);
    }

//    public function updateUserLastPageVisitDates(): void
//    {
//        $modelUtilisateur = new Model_DbTable_Utilisateur();
//
//        $idUtilisateur = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];
//        $utilisateur = $modelUtilisateur->find($idUtilisateur)->current();
//
//        foreach (self::NAMESPACE_MAP as $namespace => $sqlCol) {
//            $utilisateur->{$sqlCol} = $this->getLastPageVisitDate($namespace);
//        }
//
//        $utilisateur->save();
//    }

//    private function initLastPageVisitDate(string $sessionNamespace): string
//    {
//        $modelUtilisateur = new Model_DbTable_Utilisateur();
//
//        $idUtilisateur = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];
//        $utilisateur = $modelUtilisateur->find($idUtilisateur)->current()->toArray();
//
//        return $utilisateur[self::NAMESPACE_MAP[$sessionNamespace]];
//    }
}
