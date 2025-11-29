/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#00d4ff',
          dark: '#00a8cc',
        },
        accent: '#7b2ff7',
        dark: {
          900: '#1a1a2e',
          800: '#16213e',
          700: '#0f3460',
        },
        success: '#34c759',
        danger: '#ff3b30',
        warning: '#ffcc00',
      },
    },
  },
  plugins: [],
}
