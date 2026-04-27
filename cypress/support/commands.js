// Login using credentials from Cypress.env() (overridable with CYPRESS_username / CYPRESS_password)
Cypress.Commands.add('login', (username = Cypress.env('username'), password = Cypress.env('password')) => {
    cy.session([username, password], () => {
        cy.visit('/session/login')
        cy.get('[name=prevarisc_login_username]').type(username)
        cy.get('[name=prevarisc_login_passwd]').type(password)
        cy.get('#Connexion').click()
        cy.title().should('eq', 'Tableau de bord')
    })
})