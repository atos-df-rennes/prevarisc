describe('Tests Etablissements', () => {
    beforeEach(() => {
        cy.visit('/');
        cy.get('input[name="prevarisc_login_username"]').type('root');
        cy.get('input[name="prevarisc_login_passwd"]').type('root');
        cy.get('#Connexion').click();
    });

    it('Dossiers', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.contains('Dossiers').should('exist');
    });

    it('Dossier 22990F1619', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.get('input[name="objet"]').type('22990F1619');
        cy.get('[name=Rechercher]').click();
    });

    it(' Dossier Test', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.get('input[name="objet"]').type('Dossier Test');
        cy.get('[name=Rechercher]').click();
        cy.screenshot('dossier-page');
    });

   it('Sélection par attribut avis de la commission ', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.get('input[value="Avis de la commission"]').clear().type('Favorable');
        cy.get('.chosen-drop .chosen-results')
        .contains('Favorable')
        .click();
        cy.get('[name=Rechercher]').click();
        cy.screenshot('dossier-avis');
    });
});
