import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: "#0ea5e9", // Ocean Blue
                "primary-dark": "#0284c7",
                "accent": "#8b5a2b", // Wood Brown
                "background-light": "#f8fafc", // Slate 50
                "background-dark": "#0f172a", // Slate 900
                "text-light": "#334155", // Slate 700
                "text-dark": "#f1f5f9", // Slate 100
                "surface-light": "#e2e8f0", // Slate 200
                "surface-dark": "#1e293b" // Slate 800
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ["Plus Jakarta Sans", "Poppins"],
                playfair: ["Playfair Display", "serif"],
                script: ["Pinyon Script", "cursive"],
            },
        },
    },

    plugins: [forms],
};
