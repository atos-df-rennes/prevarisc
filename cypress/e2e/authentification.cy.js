describe('Tests Authentification', () => {
    beforeEach(() => {
        cy.on('uncaught:exception', (err, runnable) => {
          return false;
        });
    });

    it('Connexion réussie', () => {
      cy.visit('http://localhost');
      cy.get('input[name="prevarisc_login_username"]').type('root');
      cy.get('input[name="prevarisc_login_passwd"]').type('root');
      cy.get('#Connexion').click();
      cy.contains('Bonjour ROOT').should('exist');
    });

    it('Connexion échouée', () => {
      cy.visit('http://localhost');
      cy.get('input[name="prevarisc_login_username"]').type('wrongUser');
      cy.get('input[name="prevarisc_login_passwd"]').type('wrongPass');
      cy.get('#Connexion').click();
      cy.contains('Erreur d\'authentification').should('exist');
    });
});
