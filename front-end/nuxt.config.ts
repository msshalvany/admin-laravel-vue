// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  app: {
    pageTransition: { name: 'page', mode: 'out-in' } // 'page' باید با نامی که در CSS استفاده کرده‌اید مطابقت داشته باشد
  },
  css: ['~/assets/css/main.css'],
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  modules: ['@nuxt/icon', '@nuxt/ui'],
  colorMode: {
    preference: 'light'
  },
  ssr: false
})