import { authGuard } from '../plugins/auth/authGuard';

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
            path: ':caliber_id',
            name: 'CalibersShow',
            component: () =>
              import(/* webpackChunkName: "CalibersShow" */ '../pages/calibers/CalibersShow'),
            props: (route) => {
              return {
                caliberId: parseInt(route.params.caliber_id),
              };
            },
          },
          {
            path: ':caliber_id/ammunition/:ammunition_id',
            name: 'AmmunitionShow',
            component: () =>
              import(/* webpackChunkName: "AmmunitionShow" */ '../pages/ammunition/AmmunitionShow'),
            props: (route) => {
              return {
                caliberId: parseInt(route.params.caliber_id),
                ammunitionId: parseInt(route.params.ammunition_id),
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
        path: 'callback',
        name: 'callback',
        component: () => import('../pages/auth/Callback.vue'),
      },
      {
        path: 'forgot-username',
        name: 'forgotUsername',
        meta: {
          title: 'Forgot Username',
        },
        component: () => import('../pages/auth/ForgotPassword.vue'),
      },
      {
        path: 'forgot-password',
        name: 'forgotPassword',
        meta: {
          title: 'Forgot Password',
        },
        component: () => import('../pages/auth/ForgotUsername.vue'),
      },
    ],
  },

  /**
   * Catch-all for 404 Not Found
   */
  {
    path: '*',
    meta: {
      title: 'Error 404',
    },
    component: () => import('../pages/errors/404.vue'),
  },
];

export default routes;
