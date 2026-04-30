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
                    amber: '#ffae00ff',
                    bg: '#F8FAFC',
                    success: '#059669',
                    danger: '#DC2626',
                    // Priority badges
                    'priority-low-bg': '#fff4a0',
                    'priority-low-text': '#744214',
                    'priority-mid-bg': '#FEDDAA',
                    'priority-mid-text': '#791E00',
                    'priority-high-bg': '#FECACA',
                    'priority-high-text': '#7a1717',
                    'priority-urgent-bg': '#fce7f3',
                    'priority-urgent-text': '#831843',
                    // Status badges
                    'status-open-bg': '#e9d5ff',
                    'status-open-text': '#652d91',
                    'status-progress-bg': '#bfdbfe',
                    'status-progress-text': '#183aab',
                    'status-done-bg': '#bbf7d0',
                    'status-done-text': '#1c5b35',
                }
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },



        },
    },

    plugins: [forms],
};