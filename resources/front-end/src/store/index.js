import Vue from 'vue';
import Vuex from 'vuex';

// Modules
import auth from './auth';
import ammunition from './ammunition';
import calibers from './calibers';
import firearms from './firearms';
import reference from './reference';
import ui from './ui';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    ammunition,
    calibers,
    firearms,
    reference,
    ui,
  },
});

export default store;
