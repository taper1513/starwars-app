/** @type {import('tailwindcss').Config} */
const { theme } = require('./src/theme/theme');

module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: theme,
  },
  plugins: [],
} 