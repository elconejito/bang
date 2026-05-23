import { axiosInstance } from '@/plugins/axios';

export function login(context, payload) {
  const url = `/auth/login`;

  return axiosInstance.post(url, payload).then((response) => {
    const { authorisation: authorization } = response.data;

    return context.dispatch('saveAuthInformation', authorization).then(() => {
      return context.dispatch('me');
    });
  });
}

export function logout(context) {
  const url = `/auth/logout`;

  return axiosInstance.post(url).then((response) => {
    localStorage.removeItem('access_token');
    delete axiosInstance.defaults.headers.common['Authorization'];
    context.commit('saveAuthState', false);
    context.commit('saveAuthToken', { access_token: null, expires_in: null });

    return response.data;
  });
}

export function register(context, payload) {
  const url = `/auth/register`;

  return axiosInstance.post(url, payload).then((response) => {
    return response;
  });
}

export function me(context) {
  const url = `/auth/me`;

  return axiosInstance.get(url).then((response) => {
    const { data } = response.data;
    context.commit('saveAuthUser', data);

    return data;
  });
}

export async function saveAuthInformation(context, payload) {
  const { access_token, expires_in } = payload;

  localStorage.setItem('access_token', access_token);
  axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
  context.commit('saveAuthToken', { access_token, expires_in });
  context.commit('saveAuthState', true);

  return Promise.resolve({ access_token });
}
