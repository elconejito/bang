import axios from 'axios';
import * as _ from 'lodash';

const queryParams = (params) => {
  params = params || {};
  const query = Object.keys(params).length
    ? Object.keys(params)
        .map((key) => {
          return key + '=' + params[key];
        })
        .join('&')
    : false;

  return query ? `?${query}` : '';
};

const axiosInstance = axios.create({
  baseURL: process.env.VUE_APP_API_BASE_URL,
  timeout: 10000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

// leave the export, even if you don't use it
export default ({ store, Vue }) => {
  // Check for JWT in state, or in localstorage
  const token = store.getters['auth/getToken'];
  // console.log('axios.js token', _.isEmpty(token), token);
  if (!_.isEmpty(token)) {
    axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + token;
  }

  // Set the interceptor to handle JWT token refresh
  axiosInstance.interceptors.response.use(
    (response) => {
      // Return a successful response back to the calling service
      return response;
    },
    (error) => {
      // For convenience, pull out StatusCode and Url here
      const statusCode = error.response.status;
      const url = error.config.url.split('/').pop();
      console.log('axios.js interceptors statusCode, url', statusCode, url);

      // Pass all non 401s back to the caller.
      if (statusCode !== 401) {
        // console.log('axios.js interceptors not 401');
        return Promise.reject(error);
      }

      // If one of these AuthRoutes returns a 401, this is expected (no token) and carry on as usual
      const authRoutes = ['authenticate', 'logout'];
      if (authRoutes.includes(url) && statusCode === 401) {
        // console.log('axios.js interceptors is authRoutes and 401', statusCode, url);
        return Promise.reject(error);
      }

      // Logout user if token refresh didn't work or user is disabled
      if (url === 'refresh') {
        // console.log('axios.js interceptors is refresh');
        store.dispatch('auth/logout').then(() => {
          // console.log('axios.js interceptors is refresh after logout');
          store.$router.push({ name: 'login' });
        });

        return Promise.reject(error);
      }

      console.log('axios.js interceptors before refreshToken');
      // Attempt to refresh the token and retry the original request
      return store
        .dispatch('auth/refreshToken')
        .then((token) => {
          console.log('axios.js interceptors refreshToken then');
          // New request with new token
          const config = error.config;
          config.headers['Authorization'] = `Bearer ${token}`;

          return axiosInstance.request(config);
        })
        .catch((error) => {
          return Promise.reject(error);
        });
    }
  );

  Vue.prototype.$axios = axiosInstance;
};

export { axiosInstance, queryParams };
