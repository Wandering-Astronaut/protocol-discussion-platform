import type { Config } from "tailwindcss";
const config: Config = {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        forest:  { DEFAULT: '#1A3C34', light: '#2D6A5A', dark: '#0F2420' },
        sage:    { DEFAULT: '#4A7C6F', light: '#6BA898', muted: '#C8DDD9' },
        cream:   { DEFAULT: '#F7F3EE', warm: '#EDE8E0' },
        amber:   { DEFAULT: '#D4873A', light: '#E8A85C', dark: '#B06828' },
        slate:   { DEFAULT: '#3D4852', light: '#6B7280', muted: '#9CA3AF' },
      },
      fontFamily: {
        sans:    ['var(--font-inter)', 'system-ui', 'sans-serif'],
        display: ['var(--font-fraunces)', 'Georgia', 'serif'],
        mono:    ['var(--font-jetbrains)', 'monospace'],
      },
      typography: { DEFAULT: { css: { maxWidth: 'none' } } },
    },
  },
  plugins: [],
};
export default config;
