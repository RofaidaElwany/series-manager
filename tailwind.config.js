/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
    "./assets/**/*.{css,js}",
    "./includes/class-series-block-render.php",
    "./includes/**/*.php",
    "./*.php",
  ],
  theme: {
    extend: {
      colors: {
        "primary": "#0062a2",
        "primary-dim": "#00568e",
        "primary-container": "#75b8fd",
        "background": "#f9f9fa",
        "surface": "#f9f9fa",
        "surface-container-lowest": "#ffffff",
        "surface-container-low": "#f2f4f5",
        "surface-container-highest": "#dfe3e5",
        "on-surface": "#2f3335",
        "on-surface-variant": "#5b6062",
        "outline-variant": "#afb2b5",
      },
    },
  },
  plugins: [],
};
