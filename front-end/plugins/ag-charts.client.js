import { AgCharts } from 'ag-charts-vue3'

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.component('AgCharts', AgCharts)
})
