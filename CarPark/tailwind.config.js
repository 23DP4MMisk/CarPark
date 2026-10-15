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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f0f7f4',
                    100: '#d9ebe2',
                    200: '#b3d7c5',
                    300: '#84bca2',
                    400: '#58a185',
                    500: '#3b8569',
                    600: '#2b6d56',
                    700: '#1f5e46',
                    800: '#184c39',
                    900: '#0f3d2e',
                },
            },
        },
    },

    plugins: [forms],
};
