/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
    "./assets/**/*.{css,js}",
    "./includes/**/**/*.php",
    "./includes/**/*.php",
    "./*.php",
  ],
  safelist: [
    "bg-blue-500",
    "bg-purple-500",
    "bg-green-500",
    "bg-orange-500",
    "bg-indigo-500",
    "bg-red-500",
    
    "bg-blue-600",
    "bg-purple-600",
    "bg-green-600",
    "bg-orange-600",
    "bg-indigo-600",

    "bg-blue-50",
    "bg-purple-50",
    "bg-green-50",
    "bg-orange-50",
    "bg-indigo-50",

    "bg-blue-100",
    "bg-purple-100",
    "bg-green-100",
    "bg-orange-100",
    "bg-indigo-100",

    "text-blue-600",
    "text-purple-600",
    "text-green-600",
    "text-orange-600",
    "text-indigo-600",

    "border-blue-500",
    "border-purple-500",
    "border-green-500",
    "border-orange-500",
    "border-indigo-500",

    "shadow-[0_20px_60px_rgba(59,130,246,0.3)]",
    "shadow-[0_20px_60px_rgba(168,85,247,0.3)]",
    "shadow-[0_20px_60px_rgba(34,197,194,0.3)]",
    "shadow-[0_20px_60px_rgba(249,115,22,0.3)]",
    "shadow-[0_20px_60px_rgba(99,102,241,0.3)]",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#0062a2",
        "primary-dim": "#00568e",
        "primary-container": "#75b8fd",
        background: "#f9f9fa",
        surface: "#f9f9fa",
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
      boxShadow: {
        soft: "0 10px 40px rgba(15,23,42,0.06)",
        softLg: "0 20px 60px rgba(15,23,42,0.10)",
      },
    },
  },
  plugins: [],
};
