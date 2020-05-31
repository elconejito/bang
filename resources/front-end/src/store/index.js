import Vue from "vue";
import Vuex from "vuex";

// Modules
import auth from './auth';
import ui from './ui';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    ui,
  },
});

export default store;
