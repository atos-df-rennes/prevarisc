const { defineConfig } = require("cypress");

module.exports = defineConfig({
  e2e: {
    baseUrl: 'http://localhost:7080',
    defaultCommandTimeout: 25000,
  },
  // Credentials are overridable via CYPRESS_username / CYPRESS_password env vars
  env: {
    username: 'root',
    password: 'root',
  },
});


  