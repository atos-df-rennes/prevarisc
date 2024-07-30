const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    specPattern: 'cypress/e2e/**/*.{js,jsx,ts,tsx}',
    supportFile: false,
    defaultCommandTimeout: 25000,
    "screenshotOnRunFailure": true
  },
});


  