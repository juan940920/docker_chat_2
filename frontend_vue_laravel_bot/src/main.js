import { createApp } from 'vue'

import App from './App.vue'
import router from "./router"

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import { abilitiesPlugin } from '@casl/vue';
import ability from './casl/ability';

import '@/assets/styles.scss';

createApp(App)
    .use(router)
    .use(abilitiesPlugin, ability, {
        useGlobalProperties: true
    })
    .use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '.app-dark'
            }
        }
    })
    .use(ToastService)
    .use(ConfirmationService)
    .mount('#app')
