describe('Ajout Etablissements', () => {
    beforeEach(() => {
        cy.login('root', 'root')
    })

    it('Etablissement avec date', () => {
        const date = Date.now()

        cy.visit('/etablissement/add')
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(`Etablissement Test ${date}`);
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('06123456789');
        cy.get('select[name="ID_GENRE"]').select('2');
        cy.contains('Ajouter l\'établissement').click();
        cy.contains('Confirmer et sauvegarder les changements').click();
        cy.get('h2.page-header').should('contain', `Etablissement Test ${date}`)
        cy.contains('Établissement').should('exist')
        cy.contains('06123456789').should('exist')
    });
});
