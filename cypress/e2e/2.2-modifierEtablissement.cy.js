describe('Modification Etablissements', () => {
    beforeEach(() => {
        cy.login('root', 'root')
        cy.visit('/')
    })

    it('Modifier le libellé d\'un établissement', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('.dashboard').find('input[name="label"]').type('Etablissement Test');
        cy.get('[name=Rechercher]').click();
        cy.contains('Etablissement Test').should('exist');
        cy.contains('Etablissement Test').click();
        cy.contains('Modifier la fiche').click();
        const newLabel = `Etablissement Test ${Date.now()}`;
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear().type(newLabel);
        cy.contains('Sauvegarder').click();
        cy.contains('Confirmer et sauvegarder les changements').click();
        cy.get('h2.page-header').should('contain', newLabel)
    });
});