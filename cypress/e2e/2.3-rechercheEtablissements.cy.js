describe('Tests Etablissements', () => {
    before(() => {
        cy.login('root', 'root')
    });

    beforeEach(() => {
        cy.visit('/')
    })

    it('Etablissements', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();       
        cy.contains('Etablissements').should('exist');
    });

    it('Etablissement Quick', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('input[name="label"]').eq(1).type('Quick');
        cy.get('[name=Rechercher]').click();
    });

    it('Etablissement Test', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('input[name="label"]').eq(1).type('Etablissement Test 1722259235007');
        cy.get('[name=Rechercher]').click();
        cy.screenshot('etablissement-page');
    });

    it('Etablissement avec avis défavorable', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('[class="ui-multiselect ui-widget ui-state-default ui-corner-all"]').eq(2).click();
        cy.get('#ui-multiselect-6-option-1').then(($checkbox) => {
            if (!$checkbox.is(':checked')) {
                cy.wrap($checkbox).check();
            }
        });
        cy.get('[name=Rechercher]').click();
        cy.screenshot('etablissement-page-défavorable');
    });

    it('Etablissement avec avis favorable', () => {
        cy.contains('a.dropdown-toggle', 'Établissements').click();
        cy.get('a[href="/search/etablissement?label=&page=1"]').click();
        cy.get('[class="ui-multiselect ui-widget ui-state-default ui-corner-all"]').eq(2).click();
        cy.get('#ui-multiselect-6-option-0').then(($checkbox) => {
            if (!$checkbox.is(':checked')) {
                cy.wrap($checkbox).check();
            }
        });
        cy.get('[name=Rechercher]').click();
        cy.screenshot('etablissement-page-favorable');
    });
});
