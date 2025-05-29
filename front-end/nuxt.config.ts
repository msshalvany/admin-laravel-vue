// https://nuxt.com/docs/api/configuration/nuxt-config

import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  app: {
    pageTransition: { name: 'page', mode: 'out-in' } // 'page' باید با نامی که در CSS استفاده کرده‌اید مطابقت داشته باشد
  },

  css: ['~/assets/css/main.css'],
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  ssr: false,

  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  plugins: [
    {src: '~/plugins/persianDatetimePicker.client.js', mode: 'client',},
    {src: '~/plugins/ag-charts.client.js', mode: 'client',}
  ],

  modules: ['@nuxt/icon'],
})