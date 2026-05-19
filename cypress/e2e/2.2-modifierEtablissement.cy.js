// Tests fonctionnels — Modification d'un établissement (un test par genre)
// URL migrée : /etablissement/{id}/modifier
// Pré-requis : les tests 2.1 (ajout) doivent avoir été exécutés au préalable
// afin de garantir l'existence d'au moins un établissement par genre.
// Les libellés créés par 2.1 suivent le pattern "{Genre} E2E {timestamp}".
describe('Établissement — modification par genre', () => {
    beforeEach(() => {
        cy.login()
    })

    /**
     * Helper : recherche un établissement par son libellé partiel, navigue vers sa fiche,
     * puis clique sur « Modifier la fiche ».
     *
     * @param {string} searchTerm - Terme de recherche (préfixe du libellé, ex: "Site E2E")
     */
    function naviguerVersEdition(searchTerm) {
        cy.visit('/rechercher/etablissement')
        cy.get('input[name="label"].search-query').clear()
        cy.get('input[name="label"].search-query').type(searchTerm)
        cy.get('input[type="submit"][name="Rechercher"]').click()
        cy.contains(searchTerm).should('exist')
        cy.contains(searchTerm).first().click()
        cy.contains('Modifier la fiche').click()
        cy.url().should('include', '/modifier')
    }

    /**
     * Helper : soumet le formulaire de modification et vérifie le succès.
     * Intercepte l'appel defaults_values et auto-confirme la modale si elle apparaît.
     */
    function soumettreModificationEtVerifier(expectedText) {
        cy.intercept('POST', '**/defaults_values*').as('defaultsValues')

        cy.window().then(win => {
            win.$('#confirm-modal').on('shown.bs.modal', function () {
                win.$(this).find('input[type="submit"]').trigger('click')
            })
        })

        cy.get("form#etablissement input[type='submit']").click()
        cy.wait('@defaultsValues')

        // Vérification : redirection vers la fiche informations
        cy.url({ timeout: 15000 }).should('match', /\/etablissement\/\d+/)
        cy.get('h2.page-header, h2').should('contain', expectedText)
    }

    // ——————————————————————————————————————————————————————————
    // Genre 1 : Site
    // ——————————————————————————————————————————————————————————
    it('Genre Site — modification du libellé et du téléphone', () => {
        naviguerVersEdition('Site E2E')

        const newLibelle = `Site Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').clear()
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('0299000099')

        soumettreModificationEtVerifier(newLibelle)
        cy.contains('0299000099').should('exist')
    })

    // ——————————————————————————————————————————————————————————
    // Genre 2 : ERP
    // ——————————————————————————————————————————————————————————
    it('Genre ERP — modification des effectifs et de la périodicité', () => {
        naviguerVersEdition('ERP E2E')

        const newLibelle = `ERP Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)

        // Modifier les effectifs
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').type('750')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').type('80')

        // Modifier la périodicité
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').type('24')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 3 : Cellule
    // ——————————————————————————————————————————————————————————
    it('Genre Cellule — modification du libellé et des effectifs', () => {
        naviguerVersEdition('Cellule E2E')

        const newLibelle = `Cellule Modifiée ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').type('300')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 4 : Habitation
    // ——————————————————————————————————————————————————————————
    it('Genre Habitation — modification du libellé et de la famille', () => {
        naviguerVersEdition('Habitation E2E')

        const newLibelle = `Habitation Modifiée ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)

        // Changer la famille (sélection index 2 si disponible, sinon 1)
        cy.get('select[name="ID_FAMILLE"]').then($select => {
            const options = $select.find('option').length
            cy.get('select[name="ID_FAMILLE"]').select(options > 2 ? 2 : 1)
        })

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 5 : IGH
    // ——————————————————————————————————————————————————————————
    it('Genre IGH — modification du libellé et de la périodicité', () => {
        naviguerVersEdition('IGH E2E')

        const newLibelle = `IGH Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="PERIODICITE_ETABLISSEMENTINFORMATIONS"]').type('12')
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').type('1500')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 6 : BUP
    // ——————————————————————————————————————————————————————————
    it('Genre BUP — modification du libellé et des effectifs', () => {
        naviguerVersEdition('BUP E2E')

        const newLibelle = `BUP Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').type('120')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 7 : Camping
    // ——————————————————————————————————————————————————————————
    it('Genre Camping — modification des emplacements et vérification du total', () => {
        naviguerVersEdition('Camping E2E')

        const newLibelle = `Camping Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)

        // Modifier les emplacements
        cy.get('input[name="EFFECTIFHABITATION_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFHABITATION_ETABLISSEMENTINFORMATIONS"]').type('60')
        cy.get('input[name="EFFECTIFCARAVANE__ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFCARAVANE__ETABLISSEMENTINFORMATIONS"]').type('40')
        cy.get('input[name="EFFECTIFEMPLACEMENTNU_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFEMPLACEMENTNU_ETABLISSEMENTINFORMATIONS"]').type('120')
        cy.get('input[name="EFFECTIFDIVERS_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFDIVERS_ETABLISSEMENTINFORMATIONS"]').type('10')

        // Vérifier le calcul dynamique du total
        cy.get('input[name="EFFECTIFTOTALCAMPING_ETABLISSEMENTINFORMATIONS"]').should('have.value', '230')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 8 : Manifestation temporaire
    // ——————————————————————————————————————————————————————————
    it('Genre Manifestation temporaire — modification des effectifs', () => {
        naviguerVersEdition('Manif E2E')

        const newLibelle = `Manif Modifiée ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').type('8000')
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"]').type('300')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 9 : IOP
    // ——————————————————————————————————————————————————————————
    it('Genre IOP — modification des effectifs', () => {
        naviguerVersEdition('IOP E2E')

        const newLibelle = `IOP Modifié ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"]').type('3000')

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Genre 10 : Zone
    // ——————————————————————————————————————————————————————————
    it('Genre Zone — modification du libellé et du classement', () => {
        naviguerVersEdition('Zone E2E')

        const newLibelle = `Zone Modifiée ${Date.now()}`
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear()
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(newLibelle)

        // Changer le classement (sélection index 2 si disponible, sinon 1)
        cy.get('select[name="ID_CLASSEMENT"]').then($select => {
            const options = $select.find('option').length
            cy.get('select[name="ID_CLASSEMENT"]').select(options > 2 ? 2 : 1)
        })

        soumettreModificationEtVerifier(newLibelle)
    })

    // ——————————————————————————————————————————————————————————
    // Test CSRF : un token invalide ne provoque pas la sauvegarde
    // ——————————————————————————————————————————————————————————
    it('Soumission avec token CSRF invalide affiche une erreur', () => {
        // Utiliser "Modifié" car les tests précédents ont renommé les établissements "E2E"
        naviguerVersEdition('Modifié')

        // Altérer le token CSRF dans le DOM
        cy.get('input[name="_token"]').invoke('val', 'token_invalide_csrf')

        cy.get("form#etablissement input[type='submit']").click()

        // La page ne redirige pas, elle affiche un flash d'erreur
        cy.url().should('include', '/modifier')
        cy.contains('Token CSRF invalide').should('exist')
    })
})
