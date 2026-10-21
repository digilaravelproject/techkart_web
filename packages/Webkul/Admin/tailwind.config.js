/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/Resources/**/*.blade.php", "./src/Resources/**/*.js"],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1920px",
            },

            padding: {
                DEFAULT: "16px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1920px",
        },

        extend: {
            colors: {
                darkGreen: '#40994A',
                darkBlue: '#c2955e',
                darkPink: '#F85156',
                blue: {
                    50: '#FFFAE7',
                    100: '#F4E0C5',
                    200: '#D9C2A4',
                    300: '#BDA584',
                    400: '#B69770',
                    500: '#C29559',
                    600: '#c2955e', // BEENZO Primary Accent / Warm Gold
                    700: '#a0753f', // Hover & Dark Border
                    800: '#8a6639',
                    900: '#543d20',
                    950: '#382612',
                },
                beenzo: {
                    DEFAULT: "#c2955e",
                    50: "#FFFAE7",
                    100: "#F4E0C5",
                    200: "#D9C2A4",
                    300: "#BDA584",
                    400: "#B69770",
                    500: "#c2955e",
                    600: "#C29559",
                    700: "#BF9564",
                    800: "#8a6639",
                    900: "#543d20",
                },
            },

            fontFamily: {
                inter: ['Inter'],
                icon: ['icomoon']
            }
        },
    },
    
    darkMode: 'class',

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        },
        {
            pattern: /text-blue-/,
        },
        {
            pattern: /bg-blue-/,
        },
        {
            pattern: /border-blue-/,
        },
        {
            pattern: /ring-blue-/,
        }
    ]
};
