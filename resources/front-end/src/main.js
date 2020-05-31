import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";

// eslint-disable-next-line no-unused-vars
import $ from 'jquery';

Vue.config.productionTip = false;

// Import our Stylesheets
import './styles/index.scss';

// make router instance available in store
store.$router = router;

// Import any Plugins we want globally available in the application
import axios from './plugins/axios';
import bootstrap from './plugins/bootstrap';
import errorProcessor from './plugins/errorProcessor';
import fontAwesome from './plugins/font-awesome';
import permissions from './plugins/permissions';
import vCalendar from './plugins/v-calendar';

// Run Plugin functions
axios({ store, Vue });
bootstrap({ Vue });
errorProcessor({ Vue });
fontAwesome({ Vue });
permissions({ store, Vue });
vCalendar({ Vue });

new Vue({
  el: '#app',
  router,
  store,
  render: (h) => h(App),
});
