const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost',
    defaultCommandTimeout: 25000
  },
});


  