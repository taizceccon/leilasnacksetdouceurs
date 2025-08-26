/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./templates/**/*.{html,twig}",
    "./assets/**/*.{js,jsx,ts,tsx,vue}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Quicksand', 'sans-serif'],
        serif: ['Playfair Display', 'serif'],
      },
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
    },
  },
  plugins: [],
}