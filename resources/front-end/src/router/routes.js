// Declare all the routes
const routes = [
  /**
   * Core routes
   */
  {
    path: '/',
    component: () => import(/* webpackChunkName: "Authenticated" */ '../layouts/Authenticated'),
    beforeEnter: (to, from, next) => {
      // #TODO Check for Authentication
      next();
    },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: () => import(/* webpackChunkName: "Dashboard" */ '../pages/HomeDashboard'),
      },
      /**
       * Caliber Section
       */
      {
        path: 'calibers',
        component: () => import(/* webpackChunkName: "CalibersLayout" */ '../layouts/sections/CalibersLayout'),
        children: [
          {
            path: '',
            name: 'CalibersIndex',
            component: () => import(/* webpackChunkName: "CalibersIndex" */ '../pages/calibers/CalibersIndex'),
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
        meta: {
          title: 'Login',
        },
        component: () => import('../pages/auth/Login.vue'),
      },
      {
        path: 'logout',
        name: 'logout',
        meta: {
          title: 'Logout',
        },
        component: () => import('../pages/auth/Logout.vue'),
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
