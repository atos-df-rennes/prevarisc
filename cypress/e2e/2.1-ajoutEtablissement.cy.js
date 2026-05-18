// Tests fonctionnels — Ajout d'un établissement (un test par genre)
// URL migrée : /etablissement/ajouter
describe('Établissement — ajout par genre', () => {
    beforeEach(() => {
        cy.login()
    })

    /**
     * Helper : ajoute une adresse via la modale.
     * On remplit les champs manuellement sans passer par la géolocalisation
     * (Nominatim/IGN pouvant être indisponibles en environnement de test).
     */
    function ajouterAdresse(commune, voie, numero, complement) {
        cy.contains('Ajouter une adresse').click()
        cy.get('#adresse-modal-ajout').should('be.visible')

        // Attendre que le champ commune soit actif
        cy.get('#adresse-modal-ajout input[name="commune_ac"]').should('not.be.disabled')
        cy.get('#adresse-modal-ajout input[name="commune_ac"]').clear().type(commune)

        // Sélectionner la première suggestion de commune
        cy.get('.typeahead.dropdown-menu').should('be.visible')
        cy.get('.typeahead.dropdown-menu li:first').click()

        // Voie
        cy.get('#adresse-modal-ajout input[name="voie_ac"]').should('not.be.disabled')
        cy.get('#adresse-modal-ajout input[name="voie_ac"]').clear().type(voie)
        cy.get('.typeahead.dropdown-menu').should('be.visible')
        cy.get('.typeahead.dropdown-menu li:first').click()

        // Numéro
        cy.get('#adresse-modal-ajout input[name="numero"]').should('not.be.disabled')
        cy.get('#adresse-modal-ajout input[name="numero"]').clear().type(numero)

        if (complement) {
            cy.get('#adresse-modal-ajout input[name="complement"]').clear().type(complement)
        }

        // Coordonnées manuelles (bypass géolocalisation)
        cy.get('#adresse-modal-ajout input[name="lon"]').invoke('val', '-1.6778')
        cy.get('#adresse-modal-ajout input[name="lat"]').invoke('val', '48.1172')

        // Sauvegarder l'adresse
        cy.get('#adresse-modal-ajout').contains('Sauvegarder').click()
        cy.get('#adresse-modal-ajout').should('not.be.visible')
    }

    /**
     * Helper : soumet le formulaire et vérifie la création.
     */
    function soumettreEtVerifier(libelle) {
        cy.contains("Ajouter l'établissement").click()

        // Si la modale de confirmation apparaît, la valider
        cy.get('body').then($body => {
            if ($body.find('#confirm-modal:visible').length > 0) {
                cy.contains('Confirmer et sauvegarder les changements').click()
            }
        })

        // Vérification : on arrive sur la fiche de l'établissement créé
        cy.url().should('match', /\/etablissement\/\d+/)
        cy.get('h2.page-header, h2').should('contain', libelle)
    }

    // ——————————————————————————————————————————————————————————
    // Genre 1 : Site
    // ——————————————————————————————————————————————————————————
    it('Genre Site — création avec établissements enfants', () => {
        const libelle = `Site E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('SITE-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299000001')
        cy.get('input[name="FAX_ETABLISSEMENT"]').type('0299000002')
        cy.get('input[name="COURRIEL_ETABLISSEMENT"]').type('site@test.fr')

        // Genre = Site (1)
        cy.get('select[name="ID_GENRE"]').select('1')

        // Un Site n'a pas de champ père (caché)
        cy.get('.etablissement_pere').should('not.be.visible')

        soumettreEtVerifier(libelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 2 : ERP (Établissement Recevant du Public)
    // ——————————————————————————————————————————————————————————
    it('Genre ERP — création complète avec types, effectifs, plans, adresse, commission', () => {
        const libelle = `ERP E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('ERP-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299100001')
        cy.get('input[name="COURRIEL_ETABLISSEMENT"]').type('erp@test.fr')

        // Genre = ERP (2)
        cy.get('select[name="ID_GENRE"]').select('2')

        // Catégorie
        cy.get('select[name="ID_CATEGORIE"]').should('be.visible').select(1)

        // Périodicité
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').clear().type('36')

        // Type principal & Activité
        cy.get('select[name="ID_TYPE"]').should('be.visible').select(1)
        cy.get('select[name="ID_TYPEACTIVITE"]').should('be.visible').select(1)

        // Ajouter une activité secondaire
        cy.get('#add_activite_secondaire').click()
        cy.get('#activite_ul li:not(.hide):not(:first)').last().within(() => {
            cy.get('select.type_select').select(1)
            cy.get('select.activite_select').select(1)
        })

        // R143-20
        cy.get('input[name="R14320_ETABLISSEMENTINFORMATIONS"][value="1"]').check()

        // Local à sommeil : Non
        cy.get('input[name="LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"][value="0"]').check()

        // Effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear().type('500')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('50')
        cy.get('input[name="EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS"]').clear().type('0')

        // Plan d'intervention
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-ERP-001')
        })

        // Commission (sélectionner la première disponible)
        cy.get('select[name="ID_COMMISSION"]').select(1)

        // Données pratiques
        cy.get('input[name="NBPREV_ETABLISSEMENT"]').clear().type('2')
        cy.get('input[name="DUREEVISITE_ETABLISSEMENT"]').clear().type('02:30')

        // Adresse
        ajouterAdresse('Rennes', 'Rue', '10', 'Bâtiment A')

        soumettreEtVerifier(libelle)

        // Vérifications supplémentaires
        cy.contains('ERP').should('exist')
        cy.contains('0299100001').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 3 : Cellule
    // ——————————————————————————————————————————————————————————
    it('Genre Cellule — création avec types, effectifs, plans et données pratiques', () => {
        const libelle = `Cellule E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('CEL-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299200001')

        // Genre = Cellule (3)
        cy.get('select[name="ID_GENRE"]').select('3')

        // Type & activité
        cy.get('select[name="ID_TYPE"]').should('be.visible').select(1)
        cy.get('select[name="ID_TYPEACTIVITE"]').should('be.visible').select(1)

        // Effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear().type('200')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('30')
        cy.get('input[name="EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS"]').clear().type('10')

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-CEL-001')
        })

        // Données pratiques
        cy.get('input[name="NBPREV_ETABLISSEMENT"]').clear().type('1')
        cy.get('input[name="DUREEVISITE_ETABLISSEMENT"]').clear().type('01:00')

        soumettreEtVerifier(libelle)
        cy.contains('Cellule').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 4 : Habitation
    // ——————————————————————————————————————————————————————————
    it('Genre Habitation — création avec famille et adresse', () => {
        const libelle = `Habitation E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('HAB-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299300001')

        // Genre = Habitation (4)
        cy.get('select[name="ID_GENRE"]').select('4')

        // Famille
        cy.get('select[name="ID_FAMILLE"]').should('be.visible').select(1)

        // Adresse
        ajouterAdresse('Rennes', 'Rue', '5', '')

        soumettreEtVerifier(libelle)
        cy.contains('Habitation').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 5 : IGH
    // ——————————————————————————————————————————————————————————
    it('Genre IGH — création avec classe, effectifs, commission, adresse et plans', () => {
        const libelle = `IGH E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('IGH-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299400001')
        cy.get('input[name="FAX_ETABLISSEMENT"]').type('0299400002')

        // Genre = IGH (5)
        cy.get('select[name="ID_GENRE"]').select('5')

        // Classe IGH
        cy.get('select[name="ID_CLASSE"]').should('be.visible').select(1)

        // Périodicité
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').clear().type('24')

        // Effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear().type('1000')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('100')
        cy.get('input[name="EFFECTIFHEBERGE_ETABLISSEMENTINFORMATIONS"]').clear().type('50')

        // Commission
        cy.get('select[name="ID_COMMISSION"]').select(1)

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-IGH-001')
        })

        // Adresse
        ajouterAdresse('Rennes', 'Avenue', '1', 'Tour A')

        // Données pratiques
        cy.get('input[name="NBPREV_ETABLISSEMENT"]').clear().type('3')
        cy.get('input[name="DUREEVISITE_ETABLISSEMENT"]').clear().type('04:00')

        soumettreEtVerifier(libelle)
        cy.contains('IGH').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 6 : BUP (Bâtiment à Usage Professionnel)
    // ——————————————————————————————————————————————————————————
    it('Genre BUP — création avec ICPE, rubriques, effectifs et adresse', () => {
        const libelle = `BUP E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('BUP-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299500001')

        // Genre = BUP (6)
        cy.get('select[name="ID_GENRE"]').select('6')

        // ICPE coché
        cy.get('input[name="ICPE_ETABLISSEMENTINFORMATIONS"][value="1"]').check()

        // Rubrique ICPE
        cy.get('#add_rubrique').click()
        cy.get('#rubriques_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1) // Rubrique
            cy.get('input[type="text"]').eq(0).type('4321')  // Numéro
            cy.get('input[type="text"]').eq(1).type('Stockage produits')  // Nom
            cy.get('input[type="text"]').eq(2).type('500 T')  // Valeur
            cy.get('select').last().select(1)  // Classement
        })

        // Effectifs (pas d'effectif public ni hébergé pour BUP)
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('80')

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-BUP-001')
        })

        // Adresse
        ajouterAdresse('Rennes', 'Boulevard', '25', 'Zone Industrielle')

        soumettreEtVerifier(libelle)
        cy.contains('BUP').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 7 : Camping
    // ——————————————————————————————————————————————————————————
    it('Genre Camping — création avec emplacements, adresse et plans', () => {
        const libelle = `Camping E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('CAMP-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299600001')
        cy.get('input[name="COURRIEL_ETABLISSEMENT"]').type('camping@test.fr')

        // Genre = Camping (7)
        cy.get('select[name="ID_GENRE"]').select('7')

        // Emplacements camping
        cy.get('input[name="EFFECTIFHABITATION_ETABLISSEMENTINFORMATIONS"]').should('be.visible').clear().type('50')
        cy.get('input[name="EFFECTIFCARAVANE__ETABLISSEMENTINFORMATIONS"]').clear().type('30')
        cy.get('input[name="EFFECTIFEMPLACEMENTNU_ETABLISSEMENTINFORMATIONS"]').clear().type('100')
        cy.get('input[name="EFFECTIFDIVERS_ETABLISSEMENTINFORMATIONS"]').clear().type('20')

        // Vérifier le calcul automatique du total
        cy.get('input[name="EFFECTIFTOTALCAMPING_ETABLISSEMENTINFORMATIONS"]').should('have.value', '200')

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-CAMP-001')
        })

        // Adresse
        ajouterAdresse('Rennes', 'Chemin', '1', '')

        soumettreEtVerifier(libelle)
        cy.contains('Camping').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 8 : Manifestation temporaire
    // ——————————————————————————————————————————————————————————
    it('Genre Manifestation temporaire — création avec effectifs, adresse et plans', () => {
        const libelle = `Manif E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('MANIF-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299700001')

        // Genre = Manifestation temporaire (8)
        cy.get('select[name="ID_GENRE"]').select('8')

        // Effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').should('be.visible').clear().type('5000')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('200')

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-MANIF-001')
        })

        // Adresse
        ajouterAdresse('Rennes', 'Place', '1', 'Parc des expositions')

        soumettreEtVerifier(libelle)
        cy.contains('Manifestation').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 9 : IOP (Installation Ouverte au Public)
    // ——————————————————————————————————————————————————————————
    it('Genre IOP — création avec effectifs, adresse et plans', () => {
        const libelle = `IOP E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('IOP-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299800001')
        cy.get('input[name="FAX_ETABLISSEMENT"]').type('0299800002')
        cy.get('input[name="COURRIEL_ETABLISSEMENT"]').type('iop@test.fr')

        // Genre = IOP (9)
        cy.get('select[name="ID_GENRE"]').select('9')

        // Effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').should('be.visible').clear().type('2000')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear().type('50')

        // Plan
        cy.get('#add_plan').click()
        cy.get('#plans_tbody tr:not(.hide):not(.prototype)').last().within(() => {
            cy.get('select').first().select(1)
            cy.get('input[type="text"]').first().type('PLAN-IOP-001')
        })

        // Adresse
        ajouterAdresse('Rennes', 'Allée', '3', '')

        soumettreEtVerifier(libelle)
        cy.contains('IOP').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 10 : Zone
    // ——————————————————————————————————————————————————————————
    it('Genre Zone — création avec classement et adresse', () => {
        const libelle = `Zone E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('ZONE-001')
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299900001')

        // Genre = Zone (10)
        cy.get('select[name="ID_GENRE"]').select('10')

        // Classement de zone
        cy.get('select[name="ID_CLASSEMENT"]').should('be.visible').select(1)

        // Adresse
        ajouterAdresse('Rennes', 'Impasse', '2', '')

        soumettreEtVerifier(libelle)
        cy.contains('Zone').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Test complémentaire : ERP avec établissement père
    // ——————————————————————————————————————————————————————————
    it('Genre ERP avec père — rattachement à un Site existant', () => {
        const libelle = `ERP Fils E2E ${Date.now()}`

        cy.visit('/etablissement/ajouter')

        // Général
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(libelle)
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299110001')

        // Genre = ERP (2)
        cy.get('select[name="ID_GENRE"]').select('2')

        // Rechercher un établissement père (autocomplete)
        cy.get('#pere_autocomplete').type('Site E2E')
        cy.get('.typeahead.dropdown-menu').should('be.visible')
        cy.get('.typeahead.dropdown-menu li:first').click()
        cy.get('input[name="ID_PERE"]').should('not.have.value', '')

        // Catégorie + type minimal
        cy.get('select[name="ID_CATEGORIE"]').select(1)
        cy.get('select[name="ID_TYPE"]').select(1)
        cy.get('select[name="ID_TYPEACTIVITE"]').select(1)
        cy.get('input[name="LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"][value="0"]').check()

        // Adresse
        ajouterAdresse('Rennes', 'Rue', '12', '')

        soumettreEtVerifier(libelle)
    })
})
