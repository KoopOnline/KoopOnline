/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],  
  theme: {
    extend: {
      colors: {
        'mb': '#EEEDEE',
        'sb': '#ffff',
        'accent': '#8CC739',
        'accent2': '#21BEDE'
      }
    },
  },
  plugins: [],
}

