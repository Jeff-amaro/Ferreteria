/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#0820d7',
                    50: '#eef1ff',
                    100: '#dfe4ff',
                    200: '#c5ceff',
                    300: '#9faaff',
                    400: '#737cff',
                    500: '#4f52ff',
                    600: '#0820d7',
                    700: '#0719b0',
                    800: '#0b188c',
                    900: '#101a6f',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
