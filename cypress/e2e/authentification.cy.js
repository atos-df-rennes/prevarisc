describe('Tests Authentification', () => {
    it('Connexion réussie', () => {
      cy.visit('/');
      cy.get('input[name="prevarisc_login_username"]').type('root');
      cy.get('input[name="prevarisc_login_passwd"]').type('root');
      cy.get('#Connexion').click();
      cy.contains('Bonjour ROOT').should('exist');
    });

    it('Connexion échouée', () => {
      cy.visit('/');
      cy.get('input[name="prevarisc_login_username"]').type('wrongUser');
      cy.get('input[name="prevarisc_login_passwd"]').type('wrongPass');
      cy.get('#Connexion').click();
      cy.contains('Erreur d\'authentification').should('exist');
    });
});
