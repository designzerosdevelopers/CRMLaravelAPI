import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import path from "path";
import { defineConfig } from "vite";


export default defineConfig({
  plugins: [
    laravel({
      input: "resources/js/src/main.jsx",
      refresh: true,
      valetTls: false, // Ensure it's disabled if not using Laravel Valet
    }),
    react(),
  ],
  resolve: {
    alias: {
      '@': '/resources/js/src' // Optional alias
    }
  },
  server: {
    port: 5173,
    strictPort: true,
    open: false,
  },

  build: {
    manifest: true,
    outDir: "public/build", // ✅ Ensure correct output path
    emptyOutDir: true, // ✅ Clears old files before build
    rollupOptions: {
      input: "resources/js/src/main.jsx",
    },
  },

  resolve: {
    alias: {
      "@": path.resolve(__dirname, "resources/js"),
    },
  },

  define: {
    "import.meta.env.VITE_API_URL": JSON.stringify(process.env.VITE_API_URL),
  },
});
