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

            colors: {
                synapso: {
                    navy: '#1E293B',
                    gold: '#D97706',
                    bg: '#F8FAFC',
                    success: '#059669',
                    danger: '#DC2626',
                }
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

        },
    },

    plugins: [forms],
};