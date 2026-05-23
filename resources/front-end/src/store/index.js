import { createStore } from 'vuex';

import auth from './auth';
import ammunition from './ammunition';
import calibers from './calibers';
import firearms from './firearms';
import inventories from './inventories';
import locations from './locations';
import magazines from './magazines';
import reference from './reference';
import stores from './stores';
import training from './training';
import ui from './ui';

const store = createStore({
  modules: {
    auth,
    ammunition,
    calibers,
    firearms,
    inventories,
    locations,
    magazines,
    reference,
    stores,
    training,
    ui,
  },
});

export default store;
