/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class', '[data-theme="dark"]'], 
  content: [
    "./templates/**/*.{html,twig}", // ← Ajoute aussi ton dossier Twig
    "./src/**/*.{js,ts,vue,jsx,tsx}",
  ],
  content: [
    "./src/**/*.{html,js,jsx,ts,tsx}", // your source files
    "./public/index.html",             // your main html
  ],
  theme: {
    extend: {
      colors: {
        rose: '#E5C1BD',
        menthe: '#84D3CE',
        roseFonce: '#C4847C',
        jaunePale: '#EDCC8B',
        sauge: '#7B9E87',
        creme: '#F9F4F2',
      },
      fontFamily: {
        primary: ['Poppins', 'sans-serif'],
        script: ['Jost', 'sans-serif'],
      },
    },
  },
  plugins: [],
}