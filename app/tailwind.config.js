/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class', '[data-theme="dark"]'], 
content: [
  "./templates/**/*.{html,twig}",
  "./src/**/*.{js,ts,vue,jsx,tsx}",
  "./public/index.html",
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
        fondDark: '#282626ff',
        texteClair: '#F9F4F2',
      },
      fontFamily: {
        primary: ['Poppins', 'sans-serif'],
        script: ['Jost', 'sans-serif'],
      },
    },
  },
  plugins: [],
}