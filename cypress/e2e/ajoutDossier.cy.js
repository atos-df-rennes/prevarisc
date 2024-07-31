describe('Tests AjoutDossiers', () => {
    beforeEach(() => {
        cy.visit('/');
        cy.get('input[name="prevarisc_login_username"]').type('root');
        cy.get('input[name="prevarisc_login_passwd"]').type('root');
        cy.get('#Connexion').click();
    });

    it('AjoutDossier', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/dossier/add"]').click();
        cy.get('select[name="TYPE_DOSSIER"]').select('1');
        cy.get('select[name="selectNature"]').select('6');
        cy.get('[name="OBJET_DOSSIER"]').type(`Dossier Test ${Date.now()}`);
        cy.get('#creationDossier').click();
      });
});
