import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';

import './styles/index.scss';

import axiosPlugin from './plugins/axios';
import bootstrapPlugin from './plugins/bootstrap';
import errorProcessorPlugin from './plugins/errorProcessor';
import fontAwesomePlugin from './plugins/font-awesome';
import permissionsPlugin from './plugins/permissions';
import vCalendarPlugin from './plugins/v-calendar';

const app = createApp(App);

app.use(router);
app.use(store);

axiosPlugin(app, store);
bootstrapPlugin(app);
errorProcessorPlugin(app);
fontAwesomePlugin(app);
permissionsPlugin(app, store);
vCalendarPlugin(app);

app.mount('#app');
