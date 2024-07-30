describe('Tests ModifDossier', () => {
    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
            return false;
        });
        cy.visit('http://localhost');
        cy.get('input[name="prevarisc_login_username"]').type('root');
        cy.get('input[name="prevarisc_login_passwd"]').type('root');
        cy.get('#Connexion').click();
    });

    it('Dossiers', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.contains('Dossiers').should('exist');
        
    });

    it('Modifier le libellé d\'un dossier', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.get('input[name="objet"]').type('Dossier Test');
        cy.get('[name=Rechercher]').click();
        cy.contains('Dossier Test').should('exist');
        cy.contains('Dossier Test').click(); 
        cy.get('a[id="modificationDossier"]').click(); 
        const newObjet = `Dossier Test ${Date.now()}`;
        cy.get('[name="OBJET_DOSSIER"]').clear().type(newObjet);
        cy.get('input[type="submit"][value="Sauvegarder"]').click();
        cy.screenshot('page_test_after_modification');
    });
});
