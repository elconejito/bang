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
  baseURL: import.meta.env.VITE_API_BASE_URL,
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
        // Re-read the token at send time so throttled requests that fire after a
        // token refresh don't use the stale token captured at request-creation time.
        const token = localStorage.getItem('access_token');
        if (token) {
          config.headers = config.headers ?? {};
          config.headers['Authorization'] = 'Bearer ' + token;
        }
        resolve(config);
      }
    }, INTERVAL_MS);
  });
});

let isRefreshing = false;
let failedQueue = [];

function processQueue(error, token = null) {
  failedQueue.forEach(({ resolve, reject }) => (error ? reject(error) : resolve(token)));
  failedQueue = [];
}

function forceLogout() {
  localStorage.removeItem('access_token');
  delete axiosInstance.defaults.headers.common['Authorization'];
  window.location.href = '/auth/login';
}

axiosInstance.interceptors.response.use(
  function (response) {
    PENDING_REQUESTS = Math.max(0, PENDING_REQUESTS - 1);
    return Promise.resolve(response);
  },
  function (error) {
    PENDING_REQUESTS = Math.max(0, PENDING_REQUESTS - 1);

    const originalRequest = error.config;
    const url = originalRequest?.url ?? '';
    const isAuthEndpoint =
      url.includes('/auth/login') ||
      url.includes('/auth/register') ||
      url.includes('/auth/refresh');

    if (error.response?.status !== 401 || isAuthEndpoint) {
      return Promise.reject(error);
    }

    if (originalRequest?._retry) {
      forceLogout();
      return Promise.reject(error);
    }

    // Queue this request while a refresh is already in flight
    if (isRefreshing) {
      return new Promise((resolve, reject) => {
        failedQueue.push({ resolve, reject });
      }).then((token) => {
        originalRequest._retry = true;
        originalRequest.headers = originalRequest.headers ?? {};
        originalRequest.headers['Authorization'] = 'Bearer ' + token;
        return axiosInstance(originalRequest);
      });
    }

    // First 401 — attempt a token refresh
    isRefreshing = true;
    originalRequest._retry = true;

    return new Promise((resolve, reject) => {
      axiosInstance
        .post('/auth/refresh')
        .then(({ data }) => {
          const token = data.authorisation.access_token;
          localStorage.setItem('access_token', token);
          axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + token;
          originalRequest.headers = originalRequest.headers ?? {};
          originalRequest.headers['Authorization'] = 'Bearer ' + token;
          processQueue(null, token);
          resolve(axiosInstance(originalRequest));
        })
        .catch((refreshError) => {
          processQueue(refreshError, null);
          forceLogout();
          reject(refreshError);
        })
        .finally(() => {
          isRefreshing = false;
        });
    });
  }
);

export default (app) => {
  app.config.globalProperties.$axios = axiosInstance;
};

export { axiosInstance, queryParams };
