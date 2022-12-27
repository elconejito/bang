import axios from 'axios';

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
  withCredentials: true,
});

/**
 * These interceptors attempt to limit the max number of concurrent connections
 * through Axios.
 */
const MAX_REQUESTS_COUNT = 3;
const INTERVAL_MS = 20;
let PENDING_REQUESTS = 0;
// Add interceptors
axiosInstance.interceptors.request.use(function (config) {
  // eslint-disable-next-line no-unused-vars
  return new Promise((resolve, reject) => {
    let interval = setInterval(() => {
      if (PENDING_REQUESTS < MAX_REQUESTS_COUNT) {
        PENDING_REQUESTS++;
        clearInterval(interval);
        resolve(config);
      }
    }, INTERVAL_MS);
  });
});

axiosInstance.interceptors.response.use(
  function (response) {
    PENDING_REQUESTS = Math.max(0, PENDING_REQUESTS - 1);
    return Promise.resolve(response);
  },
  function (error) {
    PENDING_REQUESTS = Math.max(0, PENDING_REQUESTS - 1);
    return Promise.reject(error);
  }
);

// leave the export, even if you don't use it
export default ({ Vue }) => {
  Vue.prototype.$axios = axiosInstance;
};

export { axiosInstance, queryParams };
