describe('Modification Dossiers', () => {
    beforeEach(() => {
        cy.login('root', 'root')
        cy.visit('/')
    })

    it('Modification du libellé d\'un dossier', () => {
        cy.contains('a.dropdown-toggle', 'Dossiers').click();
        cy.get('a[href="/search/dossier?objet=&page=1"]').click();
        cy.get('input[name="objet"]').type('Dossier Test');
        cy.get('[name=Rechercher]').click();
        cy.contains('Dossier Test').should('exist');
        cy.contains('Dossier Test').click();
        cy.contains('Modifier le dossier').click();
        const newObjet = `Dossier Test ${Date.now()}`;
        cy.get('[name="OBJET_DOSSIER"]').clear().type(newObjet);
        cy.get('input[type="submit"][value="Sauvegarder"]').click();
        cy.get('#OBJET').find('.right.value').should('contain', newObjet)
    });
});
