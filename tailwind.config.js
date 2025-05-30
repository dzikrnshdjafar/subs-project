//tailwind.config.js
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
            colors: { // Added from Neubrutalism
                'cream': '#FFFAE3',
                'orange': '#FF8E3C',
                'pink': '#FF577F',
                'yellow': '#FFD166',
                'teal': '#9BE8D8',
                'blue': '#70E1E8',
                'white': '#f4f4f4',

                'lime': '#C4F000',        // warna aksen cerah dan edgy
  'purple': '#C084FC',      // buat variasi yang tetap playful
  'red': '#FF4D4D',         // aksen tegas, untuk tombol penting/error
  'green': '#6EEB83',       // alternatif segar dan kontras
  'gray': '#E0E0E0',        // untuk latar atau elemen minor
                // Original colors below - keep them or remove if not needed
                'primary': '#171717',
                'secondary': '#6ee7b7',
                'sechov': '#10b981',
                'emerald' : '#d1fae5',
                'neutral' : '#262626',
                'slate': '#e2e8f0',
                // 'red': '#fca5a5', // Neubrutalism has 'pink', this might conflict or be an alternative
                'redhov': '#f87171'
            },
            boxShadow: { // Added from Neubrutalism
                'neu': '5px 5px 0px #000',
                'neu-lg': '8px 8px 0px #000',
                'neu-sm': '3px 3px 0px #000',
            },
            borderWidth: { // Added from Neubrutalism
                '3': '3px',
            },
            fontFamily: {
                // Changed sans to Space Grotesk as primary, keeping Figtree as fallback
                sans: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};