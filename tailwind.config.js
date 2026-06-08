import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                brand: {
                    50:  '#EEF2FF',
                    100: '#E0E7FF',
                    200: '#C7D2FE',
                    300: '#A5B4FC',
                    400: '#818CF8',
                    500: '#6366F1',
                    600: '#4F46E5',
                    700: '#4338CA',
                    800: '#3730A3',
                    900: '#312E81',
                    950: '#1E1B4B',
                },
                income: {
                    50:  '#ECFDF5',
                    100: '#D1FAE5',
                    200: '#A7F3D0',
                    300: '#6EE7B7',
                    400: '#34D399',
                    500: '#10B981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065F46',
                    900: '#064E3B',
                    950: '#022C22',
                },
                expense: {
                    50:  '#FFF1F2',
                    100: '#FFE4E6',
                    200: '#FECDD3',
                    300: '#FDA4AF',
                    400: '#FB7185',
                    500: '#F43F5E',
                    600: '#E11D48',
                    700: '#BE123C',
                    800: '#9F1239',
                    900: '#881337',
                    950: '#4C0519',
                },
                accent: {
                    50:  '#FFFBEB',
                    100: '#FEF3C7',
                    200: '#FDE68A',
                    300: '#FCD34D',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                    700: '#B45309',
                    800: '#92400E',
                    900: '#78350F',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted: '#F8FAFC',
                    subtle: '#F1F5F9',
                    dark: '#0F172A',
                    'dark-muted': '#020617',
                    'dark-subtle': '#1E293B',
                },
            },

            borderRadius: {
                'xs': '4px',
                'sm': '6px',
                DEFAULT: '8px',
                'md': '10px',
                'lg': '12px',
                'xl': '16px',
                '2xl': '20px',
                '3xl': '28px',
            },

            boxShadow: {
                'xs': '0 1px 2px 0 rgb(0 0 0 / 0.04)',
                'sm': '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.06)',
                DEFAULT: '0 1px 3px 0 rgb(15 23 42 / 0.06), 0 1px 2px -1px rgb(15 23 42 / 0.06)',
                'md': '0 4px 6px -1px rgb(15 23 42 / 0.06), 0 2px 4px -2px rgb(15 23 42 / 0.05)',
                'lg': '0 10px 15px -3px rgb(15 23 42 / 0.08), 0 4px 6px -4px rgb(15 23 42 / 0.05)',
                'xl': '0 20px 25px -5px rgb(15 23 42 / 0.10), 0 8px 10px -6px rgb(15 23 42 / 0.04)',
                'inner-soft': 'inset 0 1px 0 0 rgb(255 255 255 / 0.6)',
                'glow-brand': '0 0 0 4px rgb(99 102 241 / 0.15)',
            },

            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in-fast': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },

            animation: {
                'fade-in': 'fade-in 250ms ease-out both',
                'fade-in-fast': 'fade-in-fast 150ms ease-out both',
                'scale-in': 'scale-in 200ms ease-out both',
            },
        },
    },

    plugins: [forms],
};
