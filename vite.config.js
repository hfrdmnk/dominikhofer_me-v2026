import { defineConfig } from "vite"
import tailwindcss from "@tailwindcss/vite"
import kirby from "vite-plugin-kirby"

export default defineConfig(({ mode }) => ({
  base: mode === "development" ? "/" : "/dist/",
  build: {
    outDir: "dist",
    rollupOptions: {
      input: "src/main.css",
    },
  },
  plugins: [
    tailwindcss(),
    kirby({
      watch: ["site/(templates|snippets|controllers|models|layouts)/**/*.php"],
    }),
  ],
}))
