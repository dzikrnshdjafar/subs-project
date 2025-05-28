import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Poppins', 'Inter', 'sans-serif'],
            },
            colors: {
                'primary': '#171717',
                'secondary': '#6ee7b7',
                'sechov': '#10b981',
                'emerald' : '#d1fae5',
                'neutral' : '#262626',
                'slate': '#e2e8f0',
                'red': '#fca5a5',
                'redhov': '#f87171'
              },
        },
    },

    plugins: [forms],
};
