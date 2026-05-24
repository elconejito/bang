import { authGuard } from '@/plugins/auth/authGuard';

// Declare all the routes
const routes = [
  /**
   * Core routes
   */
  {
    path: '/',
    component: () => import(/* webpackChunkName: "Authenticated" */ '../layouts/Authenticated'),
    beforeEnter: authGuard,
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import(/* webpackChunkName: "Dashboard" */ '../pages/HomeDashboard'),
      },
      /**
       * Caliber Section
       */
      {
        path: '/calibers',
        component: () =>
          import(/* webpackChunkName: "CalibersLayout" */ '../layouts/sections/CalibersLayout'),
        children: [
          {
            path: '',
            name: 'CalibersIndex',
            component: () =>
              import(/* webpackChunkName: "CalibersIndex" */ '../pages/calibers/CalibersIndex'),
          },
          {
            path: 'create',
            name: 'CalibersCreate',
            component: () =>
              import(/* webpackChunkName: "CalibersShow" */ '../pages/calibers/CalibersCreate'),
          },
          {
            path: ':caliber_id',
            name: 'CalibersShow',
            component: () => import('../pages/calibers/CalibersShow'),
            props: (route) => ({ caliberId: parseInt(route.params.caliber_id) }),
          },
          {
            path: ':caliber_id/edit',
            name: 'CalibersEdit',
            component: () => import('../pages/calibers/CalibersEdit'),
            props: (route) => ({ caliberId: parseInt(route.params.caliber_id) }),
          },
          {
            path: ':caliber_id/ammunition/create',
            name: 'AmmunitionCreate',
            component: () => import('../pages/ammunition/AmmunitionCreate'),
            props: (route) => ({ caliberId: parseInt(route.params.caliber_id) }),
          },
          {
            path: ':caliber_id/ammunition/:ammunition_id',
            name: 'AmmunitionShow',
            component: () => import('../pages/ammunition/AmmunitionShow'),
            props: (route) => ({
              caliberId: parseInt(route.params.caliber_id),
              ammunitionId: parseInt(route.params.ammunition_id),
            }),
          },
          {
            path: ':caliber_id/ammunition/:ammunition_id/edit',
            name: 'AmmunitionEdit',
            component: () => import('../pages/ammunition/AmmunitionEdit'),
            props: (route) => ({
              caliberId: parseInt(route.params.caliber_id),
              ammunitionId: parseInt(route.params.ammunition_id),
            }),
          },
          {
            path: ':caliber_id/ammunition/:ammunition_id/inventory/create',
            name: 'InventoryCreate',
            component: () => import('../pages/inventory/InventoryCreate'),
            props: (route) => ({
              caliberId: parseInt(route.params.caliber_id),
              ammunitionId: parseInt(route.params.ammunition_id),
            }),
          },
        ],
      },
      /**
       * Firearms Section
       */
      {
        path: '/firearms',
        component: () => import('../layouts/sections/FirearmsLayout'),
        children: [
          {
            path: '',
            name: 'FirearmsIndex',
            component: () => import('../pages/firearms/FirearmsIndex'),
          },
          {
            path: 'create',
            name: 'FirearmsCreate',
            component: () => import('../pages/firearms/FirearmsCreate'),
          },
          {
            path: ':firearm_id',
            name: 'FirearmsShow',
            component: () => import('../pages/firearms/FirearmsShow'),
            props: (route) => ({ firearmId: parseInt(route.params.firearm_id) }),
          },
          {
            path: ':firearm_id/edit',
            name: 'FirearmsEdit',
            component: () => import('../pages/firearms/FirearmsEdit'),
            props: (route) => ({ firearmId: parseInt(route.params.firearm_id) }),
          },
        ],
      },
      /**
       * Magazines Section
       */
      {
        path: '/magazines',
        component: () => import('../layouts/sections/MagazinesLayout'),
        children: [
          {
            path: '',
            name: 'MagazinesIndex',
            component: () => import('../pages/magazines/MagazinesIndex'),
          },
          {
            path: 'create',
            name: 'MagazinesCreate',
            component: () => import('../pages/magazines/MagazinesCreate'),
          },
          {
            path: ':magazine_id',
            name: 'MagazinesShow',
            component: () => import('../pages/magazines/MagazinesShow'),
            props: (route) => ({ magazineId: parseInt(route.params.magazine_id) }),
          },
        ],
      },

      /**
       * Training Section
       */
      {
        path: '/training',
        component: () => import('../layouts/sections/TrainingLayout'),
        children: [
          {
            path: '',
            name: 'TrainingIndex',
            component: () => import('../pages/training/TrainingIndex'),
          },
          {
            path: 'new',
            name: 'TrainingCreate',
            component: () => import('../pages/training/TrainingCreate'),
          },
          {
            path: ':training_id',
            name: 'TrainingShow',
            component: () => import('../pages/training/TrainingShow'),
            props: (route) => {
              return {
                trainingId: parseInt(route.params.training_id),
              };
            },
          },
        ],
      },

      /**
       * Location Section
       */
      {
        path: '/locations',
        component: () => import('../layouts/sections/LocationsLayout'),
        children: [
          {
            path: '',
            name: 'LocationIndex',
            component: () => import('../pages/locations/LocationsIndex'),
          },
          {
            path: 'create',
            name: 'LocationsCreate',
            component: () => import('../pages/locations/LocationsCreate'),
          },
          {
            path: ':location_id',
            name: 'LocationsShow',
            component: () => import('../pages/locations/LocationsShow'),
            props: (route) => {
              return {
                locationId: parseInt(route.params.location_id),
              };
            },
          },
        ],
      },

      /**
       * Store Section
       */
      {
        path: '/stores',
        component: () => import('../layouts/sections/StoresLayout'),
        children: [
          {
            path: '',
            name: 'StoreIndex',
            component: () => import('../pages/stores/StoresIndex'),
          },
          {
            path: 'new',
            name: 'StoreCreate',
            component: () => import('../pages/stores/StoresCreate'),
          },
          {
            path: ':store_id',
            name: 'StoreShow',
            component: () => import('../pages/stores/StoresShow'),
            props: (route) => {
              return {
                storeId: parseInt(route.params.store_id),
              };
            },
          },
        ],
      },
    ],
  },

  /**
   * Authentication Paths for login/logout, forgot
   */
  {
    path: '/login',
    redirect: { name: 'login' },
  },
  {
    path: '/logout',
    redirect: { name: 'logout' },
  },
  {
    path: '/auth',
    component: () => import('../layouts/UnAuthenticated.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('../pages/auth/Login.vue'),
      },
      {
        path: 'logout',
        name: 'logout',
        component: () => import('../pages/auth/Logout.vue'),
      },
      {
        path: 'forgot-password',
        name: 'forgotPassword',
        meta: {
          title: 'Forgot Password',
        },
        component: () => import('../pages/auth/ForgotPassword.vue'),
      },
      {
        path: 'register',
        name: 'register',
        meta: {
          title: 'Register',
        },
        component: () => import('../pages/auth/Register.vue'),
      },
    ],
  },

  /**
   * Catch-all for 404 Not Found
   */
  {
    path: '/:pathMatch(.*)*',
    meta: {
      title: 'Error 404',
    },
    component: () => import('../pages/errors/404.vue'),
  },
];

export default routes;
