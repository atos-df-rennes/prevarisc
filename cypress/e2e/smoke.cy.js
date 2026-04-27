/**
 * Smoke tests — release/2.10
 *
 * Vérifie qu'aucune page migrée ne retourne une erreur 500 (ou 503).
 * Ces tests ne valident pas la logique fonctionnelle : ils constituent
 * un filet de sécurité minimal pour détecter les régressions évidentes
 * avant une livraison RC.
 *
 * Périmètre : pages GET accessibles à un utilisateur authentifié.
 * Pages nécessitant un ID en base (dossier/{id}, établissement/{id}…)
 * sont intentionnellement exclues — elles relèvent des tests fonctionnels.
 */

const pages = [
    // Accueil & navigation
    { label: 'Accueil', url: '/accueil' },
    { label: 'Changelog', url: '/changelog' },

    // Recherche
    { label: 'Recherche — dossiers', url: '/rechercher/dossier' },
    { label: 'Recherche — établissements', url: '/rechercher/etablissement' },

    // Dossier
    { label: 'Ajout dossier', url: '/dossier/ajouter' },

    // Administration — général
    { label: 'Admin — tableau de bord', url: '/admin/' },
    { label: 'Admin — messages de changement de statut', url: '/admin/changement' },
    { label: 'Admin — tableau des périodicités', url: '/admin/tableau-des-periodicites' },
    { label: 'Admin — éléments supprimés', url: '/admin/elements-supprimes' },

    // Administration — utilisateurs & groupes
    { label: 'Admin — liste des utilisateurs', url: '/admin/utilisateurs/' },
    { label: 'Admin — ajout utilisateur', url: '/admin/utilisateurs/ajouter' },
    { label: 'Admin — matrice des droits', url: '/admin/groupes/matrice-des-droits' },
    { label: 'Admin — ressources spécialisées', url: '/admin/groupes/ressources-specialisees' },
    { label: 'Admin — ajout groupe', url: '/admin/groupes/ajouter' },

    // Administration — géographie
    { label: 'Admin — communes', url: '/admin/communes' },
    { label: 'Admin — fusion des communes', url: '/admin/fusion-des-communes' },
    { label: 'Admin — groupements de communes', url: '/admin/groupements' },
    { label: 'Admin — créer groupement', url: '/admin/groupements/creer' },

    // Administration — commissions
    { label: 'Admin — liste des commissions', url: '/admin/commissions' },
    { label: 'Admin — gérer commissions', url: '/admin/commissions/gerer' },

    // Administration — cartographie
    { label: 'Admin — couches cartographiques', url: '/admin/couches-cartographiques' },
    { label: 'Admin — ajouter couche perso', url: '/admin/couches-cartographiques/ajouter' },
    { label: 'Admin — ajouter couche IGN', url: '/admin/couches-cartographiques/ajouter-ign' },

    // Administration — documents & formulaires
    { label: 'Admin — documents types', url: '/admin/documents' },
    { label: 'Admin — ajouter document type', url: '/admin/documents/ajouter' },
    { label: 'Admin — formulaires descriptifs', url: '/admin/formulaires' },

    // Administration — prescriptions & textes
    { label: 'Admin — prescriptions', url: '/admin/prescriptions' },
    { label: 'Admin — textes applicables', url: '/admin/textes-applicables' },
];

describe('Smoke — pages migrées (release/2.10)', () => {
    before(() => {
        cy.login();
        // Établit la session dans le navigateur afin que cy.request()
        // puisse réutiliser les cookies de session.
        cy.visit('/accueil');
    });

    pages.forEach(({ label, url }) => {
        it(label, () => {
            cy.request({ url, failOnStatusCode: false }).then((response) => {
                expect(
                    response.status,
                    `HTTP ${response.status} reçu sur ${url}`
                ).to.be.lessThan(500);
            });
        });
    });
});
