/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
    "./assets/**/*.{css,js}",
    "./includes/**/**/*.php",
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
        "on-primary": "#ffffff",
        "surface-container": "#eceef4",
        "surface-container-high": "#e6e8ee",
      },
      fontFamily: {
        display: ["Manrope", "sans-serif"],
        body: ["Inter", "sans-serif"],
      },

      fontSize: {
        // Display
        "display-lg": [
          "3.5rem",
          { lineHeight: "1.1", fontWeight: "700", letterSpacing: "-0.02em" },
        ],
        "display-md": ["3rem", { lineHeight: "1.15", fontWeight: "700" }],

        // Headline
        "headline-lg": ["2rem", { lineHeight: "1.2", fontWeight: "700" }],
        "headline-md": ["1.75rem", { lineHeight: "1.25", fontWeight: "600" }],
        "headline-sm": ["1.5rem", { lineHeight: "1.3", fontWeight: "600" }],

        // Title
        "title-lg": ["1.25rem", { lineHeight: "1.4", fontWeight: "600" }],
        "title-md": ["1.125rem", { lineHeight: "1.4", fontWeight: "500" }],
        "title-sm": ["1rem", { lineHeight: "1.5", fontWeight: "500" }],

        // Body
        "body-lg": ["1rem", { lineHeight: "1.6" }],
        "body-md": ["0.875rem", { lineHeight: "1.6" }],
        "body-sm": ["0.8125rem", { lineHeight: "1.5" }],

        // Label
        "label-lg": ["0.875rem", { lineHeight: "1.4", fontWeight: "500" }],
        "label-md": ["0.75rem", { lineHeight: "1.4", fontWeight: "500" }],
        "label-sm": ["0.6875rem", { lineHeight: "1.3", fontWeight: "500" }],
      },
      spacing: {
        xs: "4px",
        sm: "8px",
        md: "16px",
        lg: "24px",
        xl: "32px",
      },
      borderRadius: {
        lg: "0.5rem",
        xl: "0.75rem",
      },
    },
  },
  plugins: [],
};
