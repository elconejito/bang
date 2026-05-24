import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

import './styles/tailwind.css';
import './styles/index.scss';

import axiosPlugin from './plugins/axios';
import bootstrapPlugin from './plugins/bootstrap';
import errorProcessorPlugin from './plugins/errorProcessor';
import fontAwesomePlugin from './plugins/font-awesome';
import vCalendarPlugin from './plugins/v-calendar';

import { useAuthStore } from '@/stores/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia); // must be before router so navigation guards can access Pinia stores
app.use(router);

axiosPlugin(app);
bootstrapPlugin(app);
errorProcessorPlugin(app);
fontAwesomePlugin(app);
vCalendarPlugin(app);

useAuthStore().restoreFromStorage();

app.mount('#app');
