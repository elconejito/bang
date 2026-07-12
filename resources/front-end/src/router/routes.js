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
       * Settings Section
       */
      {
        path: '/settings/reference-data/:list?',
        name: 'ReferenceData',
        component: () =>
          import(/* webpackChunkName: "ReferenceData" */ '../pages/settings/ReferenceData'),
        props: true,
      },
      /**
       * Accessories Section
       */
      {
        path: '/accessories',
        component: () => import('../layouts/sections/AccessoriesLayout'),
        children: [
          {
            path: '',
            name: 'AccessoriesIndex',
            component: () => import('../pages/accessories/AccessoriesIndex'),
          },
          // Suppressors
          {
            path: 'suppressors',
            name: 'AccessoriesSuppressors',
            component: () => import('../pages/accessories/AccessoriesIndex'),
            props: { category: 'suppressors' },
          },
          {
            path: 'suppressors/add',
            name: 'SuppressorCreate',
            component: () => import('../pages/accessories/suppressors/SuppressorCreate'),
          },
          {
            path: 'suppressors/:suppressor_id',
            name: 'SuppressorShow',
            component: () => import('../pages/accessories/suppressors/SuppressorShow'),
            props: (route) => ({ suppressorId: parseInt(route.params.suppressor_id) }),
          },
          {
            path: 'suppressors/:suppressor_id/edit',
            name: 'SuppressorEdit',
            component: () => import('../pages/accessories/suppressors/SuppressorEdit'),
            props: (route) => ({ suppressorId: parseInt(route.params.suppressor_id) }),
          },
          {
            path: 'suppressors/:suppressor_id/photos',
            name: 'SuppressorGallery',
            component: () => import('../pages/accessories/suppressors/SuppressorGallery'),
            props: (route) => ({ suppressorId: parseInt(route.params.suppressor_id) }),
          },
          // Optics
          {
            path: 'optics',
            name: 'AccessoriesOptics',
            component: () => import('../pages/accessories/AccessoriesIndex'),
            props: { category: 'optics' },
          },
          {
            path: 'optics/add',
            name: 'OpticCreate',
            component: () => import('../pages/accessories/optics/OpticCreate'),
          },
          {
            path: 'optics/:optic_id',
            name: 'OpticShow',
            component: () => import('../pages/accessories/optics/OpticShow'),
            props: (route) => ({ opticId: parseInt(route.params.optic_id) }),
          },
          {
            path: 'optics/:optic_id/edit',
            name: 'OpticEdit',
            component: () => import('../pages/accessories/optics/OpticEdit'),
            props: (route) => ({ opticId: parseInt(route.params.optic_id) }),
          },
          {
            path: 'optics/:optic_id/photos',
            name: 'OpticGallery',
            component: () => import('../pages/accessories/optics/OpticGallery'),
            props: (route) => ({ opticId: parseInt(route.params.optic_id) }),
          },
          // Lights
          {
            path: 'lights',
            name: 'AccessoriesLights',
            component: () => import('../pages/accessories/AccessoriesIndex'),
            props: { category: 'lights' },
          },
          {
            path: 'lights/add',
            name: 'LightCreate',
            component: () => import('../pages/accessories/lights/LightCreate'),
          },
          {
            path: 'lights/:light_id',
            name: 'LightShow',
            component: () => import('../pages/accessories/lights/LightShow'),
            props: (route) => ({ lightId: parseInt(route.params.light_id) }),
          },
          {
            path: 'lights/:light_id/edit',
            name: 'LightEdit',
            component: () => import('../pages/accessories/lights/LightEdit'),
            props: (route) => ({ lightId: parseInt(route.params.light_id) }),
          },
          {
            path: 'lights/:light_id/photos',
            name: 'LightGallery',
            component: () => import('../pages/accessories/lights/LightGallery'),
            props: (route) => ({ lightId: parseInt(route.params.light_id) }),
          },
          // Misc
          {
            path: 'misc',
            name: 'AccessoriesMisc',
            component: () => import('../pages/accessories/AccessoriesIndex'),
            props: { category: 'misc' },
          },
          {
            path: 'misc/add',
            name: 'MiscCreate',
            component: () => import('../pages/accessories/misc/MiscCreate'),
          },
          {
            path: 'misc/:misc_id',
            name: 'MiscShow',
            component: () => import('../pages/accessories/misc/MiscShow'),
            props: (route) => ({ miscId: parseInt(route.params.misc_id) }),
          },
          {
            path: 'misc/:misc_id/edit',
            name: 'MiscEdit',
            component: () => import('../pages/accessories/misc/MiscEdit'),
            props: (route) => ({ miscId: parseInt(route.params.misc_id) }),
          },
          {
            path: 'misc/:misc_id/photos',
            name: 'MiscGallery',
            component: () => import('../pages/accessories/misc/MiscGallery'),
            props: (route) => ({ miscId: parseInt(route.params.misc_id) }),
          },
        ],
      },
      /**
       * Ammo Section
       */
      {
        path: '/ammo',
        component: () => import('../layouts/sections/AmmoLayout'),
        children: [
          {
            path: '',
            name: 'AmmoIndex',
            component: () => import('../pages/ammunition/AmmoIndex'),
          },
          {
            path: 'add',
            name: 'AmmoCreate',
            component: () => import('../pages/ammunition/AmmoCreate'),
          },
          {
            path: ':ammunition_id',
            name: 'AmmoShow',
            component: () => import('../pages/ammunition/AmmoShow'),
            props: (route) => ({ ammunitionId: parseInt(route.params.ammunition_id) }),
          },
          {
            path: ':ammunition_id/edit',
            name: 'AmmoEdit',
            component: () => import('../pages/ammunition/AmmoEdit'),
            props: (route) => ({ ammunitionId: parseInt(route.params.ammunition_id) }),
          },
          {
            path: ':ammunition_id/photos',
            name: 'AmmoGallery',
            component: () => import('../pages/ammunition/AmmoGallery'),
            props: (route) => ({ ammunitionId: parseInt(route.params.ammunition_id) }),
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
          {
            path: ':firearm_id/photos',
            name: 'FirearmGallery',
            component: () => import('../pages/firearms/FirearmGallery'),
            props: (route) => ({ firearmId: parseInt(route.params.firearm_id) }),
          },
        ],
      },
      /**
       * Magazines Section
       */
      {
        path: '/accessories/magazines',
        component: () => import('../layouts/sections/MagazinesLayout'),
        children: [
          {
            path: '',
            name: 'MagazinesIndex',
            component: () => import('../pages/magazines/MagazinesIndex'),
          },
          {
            path: 'compatible/:firearm_id',
            name: 'CompatibleMagazines',
            component: () => import('../pages/magazines/MagazinesIndex'),
          },
          {
            path: 'create',
            name: 'MagazinesCreate',
            component: () => import('../pages/magazines/MagazinesCreate'),
          },
          {
            path: 'create-batch',
            name: 'MagazineBatchCreate',
            component: () => import('../pages/magazines/MagazineBatchCreate'),
          },
          {
            path: 'groups/:group',
            name: 'MagazineGroupShow',
            component: () => import('../pages/magazines/MagazineGroupShow'),
            props: (route) => ({ groupKey: route.params.group }),
          },
          {
            path: ':magazine_id/details',
            name: 'MagazinesShow',
            component: () => import('../pages/magazines/MagazinesShow'),
            props: (route) => ({ magazineId: parseInt(route.params.magazine_id) }),
          },
          {
            path: ':magazine_id/photos',
            name: 'MagazineGallery',
            component: () => import('../pages/magazines/MagazineGallery'),
            props: (route) => ({ magazineId: parseInt(route.params.magazine_id) }),
          },
          {
            path: ':magazine_id',
            name: 'MagazinesEdit',
            component: () => import('../pages/magazines/MagazinesEdit'),
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
          {
            path: ':training_id/edit',
            name: 'TrainingEdit',
            component: () => import('../pages/training/TrainingEdit'),
            props: (route) => ({
              trainingId: parseInt(route.params.training_id),
            }),
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
          {
            path: ':location_id/edit',
            name: 'LocationsEdit',
            component: () => import('../pages/locations/LocationsCreate'),
            props: (route) => ({ locationId: parseInt(route.params.location_id) }),
          },
          {
            path: ':location_id/photos',
            name: 'LocationGallery',
            component: () => import('../pages/locations/LocationGallery'),
            props: (route) => ({ locationId: parseInt(route.params.location_id) }),
          },
        ],
      },

      /**
       * Ranges Section
       */
      {
        path: '/ranges',
        component: () => import('../layouts/sections/RangesLayout'),
        children: [
          {
            path: '',
            name: 'RangesIndex',
            component: () => import('../pages/ranges/RangesIndex'),
          },
          {
            path: 'create',
            name: 'RangesCreate',
            component: () => import('../pages/ranges/RangesCreate'),
          },
          {
            path: ':range_id',
            name: 'RangesShow',
            component: () => import('../pages/ranges/RangesShow'),
            props: (route) => ({ rangeId: parseInt(route.params.range_id) }),
          },
          {
            path: ':range_id/edit',
            name: 'RangesEdit',
            component: () => import('../pages/ranges/RangesEdit'),
            props: (route) => ({ rangeId: parseInt(route.params.range_id) }),
          },
          {
            path: ':range_id/photos',
            name: 'RangeGallery',
            component: () => import('../pages/ranges/RangeGallery'),
            props: (route) => ({ rangeId: parseInt(route.params.range_id) }),
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
          {
            path: ':store_id/edit',
            name: 'StoreEdit',
            component: () => import('../pages/stores/StoresCreate'),
            props: (route) => ({ storeId: parseInt(route.params.store_id) }),
          },
          {
            path: ':store_id/photos',
            name: 'StoreGallery',
            component: () => import('../pages/stores/StoreGallery'),
            props: (route) => ({ storeId: parseInt(route.params.store_id) }),
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
