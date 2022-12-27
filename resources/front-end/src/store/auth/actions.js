import { csrfUrl } from '@/plugins/axios';

export function login(context, payload) {
  const url = `/auth/login`;

  return this._vm.$axios.get(csrfUrl).then((response) => {
    console.log('store.auth.actions.login() axios csrf then', response);

    return this._vm.$axios.post(url, payload).then((response) => {
      const { status, statusText } = response;
      const { data, meta } = response.data;
      console.log('store.auth.actions.login() axios then', data, meta, status, statusText);

      return response.data;
    });
  });
}

export function logout(context, payload) {
  const url = `/auth/logout`;

  return this._vm.$axios.post(url).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.auth.actions.logout() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function register(context, payload) {
  const url = `/auth/register`;

  return this._vm.$axios.get(csrfUrl).then((response) => {
    console.log('store.auth.actions.register() axios csrf then', response);

    return this._vm.$axios.post(url, payload).then((response) => {
      const { status, statusText } = response;
      const { data, meta } = response.data;
      console.log('store.auth.actions.register() axios then', data, meta, status, statusText);

      return response;
    });
  });
}

export function saveAuthToken(context, payload) {
  const { token } = payload;

  // Save into Axios
  this._vm.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
  // Save into State
  context.commit('saveAuthToken', { token });

  return Promise.resolve({ token });
}
