/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/Resources/**/*.blade.php", "./src/Resources/**/*.js"],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1440px",
            },

            padding: {
                DEFAULT: "90px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1440px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                navyBlue: "#c2955e",
                lightOrange: "#FDFDF8",
                darkGreen: '#40994A',
                darkBlue: '#BF9564',
                darkPink: '#F85156',
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
                poppins: ["Poppins", "sans-serif"],
                dmserif: ["DM Serif Display", "serif"],
            },
        }
    },

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        }
    ]
};
