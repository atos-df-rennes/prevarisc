describe('Ajout Dossiers', () => {
    beforeEach(() => {
        cy.login('root', 'root')
    });

    it('Ajout d\'un dossier', () => {
        const date = Date.now()

        cy.visit('/dossier/add')
        cy.get('select[name="TYPE_DOSSIER"]').select('1');
        cy.get('select[name="selectNature"]').select('1');
        cy.get('[name="OBJET_DOSSIER"]').type(`Dossier Test ${date}`);
        cy.contains('button', 'Créer le dossier').click();
        cy.get('#type').find('.right.value').should('contain', 'Étude')
        cy.get('#selectNature').should('be.disabled')
        cy.get('#selectNature option:selected').should('contain', 'Permis de construire (PC)')
        cy.get('#OBJET').find('.right.value').should('contain', `Dossier Test ${date}`)
      });
});
