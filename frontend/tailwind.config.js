/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
    theme: {
        extend: {
            screens: {
                "sm-custom": "425px",
                "md-custom": "550px",
            },
        },
    },
    plugins: [],
};
