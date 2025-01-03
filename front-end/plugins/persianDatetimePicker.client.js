import Vue3PersianDatetimePicker from 'vue3-persian-datetime-picker'

export default defineNuxtPlugin(nuxtApp => {
    nuxtApp.vueApp.component('date-picker', Vue3PersianDatetimePicker);
});
