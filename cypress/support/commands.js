// Login using credentials from Cypress.env() (overridable with CYPRESS_username / CYPRESS_password)
Cypress.Commands.add('login', (username = Cypress.env('username'), password = Cypress.env('password')) => {
    cy.session([username, password], () => {
        cy.visit('/session/login')
        cy.get('[name=_username]').type(username)
        cy.get('[name=_password]').type(password)
        cy.contains('button', 'Connexion').click()
        cy.title().should('eq', 'Tableau de bord')
    })
})