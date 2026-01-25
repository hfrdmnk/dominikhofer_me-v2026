import eslintPluginTailwindCSS from "eslint-plugin-tailwindcss";

export default [
  ...eslintPluginTailwindCSS.configs["flat/recommended"],
  {
    files: ["site/**/*.php"],
    rules: {
      "tailwindcss/no-custom-classname": "warn",
      "tailwindcss/classnames-order": "off", // Prettier handles this
    },
  },
];
