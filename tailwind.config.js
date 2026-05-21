import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue', // Đảm bảo dòng này có mặt để Tailwind quét file Vue
    ],
    theme: {
        extend: {
            colors: {
                'brand-green': '#4A7055',
                'brand-green-dark': '#3a5944',
            },
        },
    },
    plugins: [],
};