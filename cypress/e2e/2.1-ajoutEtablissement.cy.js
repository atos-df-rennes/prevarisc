describe('Tests AjoutEtablissements', () => {
    before(() => {
        cy.login('root', 'root')
    });

    beforeEach(() => {
        cy.visit('/')
    })

    it('Etablissement avec date', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/etablissement/add"]').click();
        cy.get('input[name="LIBELLE_ETABLISSEMENTINFORMATIONS"]').type(`Etablissement Test ${Date.now()}`);
        cy.get('input[name="NUMEROID_ETABLISSEMENT"]').type('1234567');
        cy.get('input[name="TELEPHONE_ETABLISSEMENT"]').type('06123456789');
        cy.get('select[name="ID_GENRE"]').select('1');
        cy.get('input[type="submit"][value="Ajouter l\'établissement"]').click();
        cy.screenshot('page_test');
    });
});
