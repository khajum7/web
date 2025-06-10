import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Bebas Neue'],
                // primary: ['Inter']
                // sans:['Bebas Neue', ...defaultTheme.fontFamily.sans]
            },
            colors: {
                'orange': '#E86F00',
                primary: {
                    50: "#EFDDFE",
                    100: "#DDB5FC",
                    200: "#BC6CF9",
                    300: "#9A22F6",
                    400: "#7308C4",
                    500: "#49057D",
                    600: "#390462",
                    700: "#2B034A",
                    800: "#1D0231",
                    900: "#0E0119",
                    950: "#09010F"
                }
              },
              container: {
                center: true,
              },
        },
    },

    plugins: [forms],
};
