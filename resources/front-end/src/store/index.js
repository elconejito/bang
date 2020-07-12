import Vue from 'vue';
import Vuex from 'vuex';

// Modules
import auth from './auth';
import ammunition from './ammunition';
import calibers from './calibers';
import ui from './ui';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    ammunition,
    calibers,
    ui,
  },
});

export default store;
