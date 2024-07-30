describe('Tests ModifEtablissements', () => {
    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
        cy.visit('http://localhost');
        cy.get('input[name="prevarisc_login_username"]').type('root');
        cy.get('input[name="prevarisc_login_passwd"]').type('root');
        cy.get('#Connexion').click();
    });

    it('Etablissements', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();       
        cy.contains('Etablissements').should('exist');
        
    });

    it('Modifier le libellé d\'un établissement', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('input[name="label"]').eq(1).type('Etablissement Test');
        cy.get('[name=Rechercher]').click();
        cy.contains('Etablissement Test').should('exist');
        cy.contains('Etablissement Test').click(); 
        cy.get('a[href*="/etablissement/edit"]').click(); 
        const newLabel = `Etablissement Test ${Date.now()}`;
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').clear().type(newLabel);
        cy.get('input[type="submit"][value="Sauvegarder"]').click();
        cy.screenshot('page_test_after_modification');


    });
});