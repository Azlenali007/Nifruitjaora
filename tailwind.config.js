/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/**/*.php',
    './public/**/*.php',
    './public/assets/js/**/*.js'
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
          950: '#06180e',
          dark: '#0d381e'
        },
        coconut: {
          light: '#fdfbf7',
          shell: '#8b5a2b',
          water: '#e6f7ff',
          green: '#2e7d32'
        }
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', '-apple-system', 'sans-serif']
      },
      boxShadow: {
        'soft-xl': '0 20px 27px 0 rgba(0, 0, 0, 0.05)',
        'card-glow': '0 10px 30px -5px rgba(22, 163, 74, 0.15)'
      }
    }
  },
  plugins: []
};
