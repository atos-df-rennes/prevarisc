Cypress.Commands.add('login', (username, password) => {
    cy.session([username, password], () => {
        cy.visit('/session/login')
        cy.get('[name=prevarisc_login_username]').type(username)
        cy.get('[name=prevarisc_login_passwd]').type(password)
        cy.get('#Connexion').click()
        cy.title().should('eq', 'Accueil')
    })
})