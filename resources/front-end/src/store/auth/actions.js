export function login(context, payload) {
  const url = `/auth/login`;

  return this._vm.$axios.post(url, payload).then((response) => {
    const { status, statusText } = response;
    const { data } = response;
    console.log('store.auth.actions.login() axios then', response, data, status, statusText);

    const { authorisation: authorization } = response.data;

    return context.dispatch('saveAuthInformation', { authorization }).
      then (() => {
        return context.dispatch('me');
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

  return this._vm.$axios.post(url, payload).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.auth.actions.register() axios then', data, meta, status, statusText);

    return response;
  });
}

export function me(context) {
  const url = `/auth/me`;

  return this._vm.$axios.get(url).then((response) => {
    const { status, statusText } = response;
    const { data } = response.data;
    console.log('store.auth.actions.me() axios then', data, status, statusText);

    return data;
  });
}

export async function saveAuthInformation(context, payload) {
  const { access_token, expires_in } = payload;

  // Save into Axios
  this._vm.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
  // Save into State
  context.commit('saveAuthToken', { access_token, expires_in });
  context.commit('saveAuthState', true);
  // #TODO Save into localStorage

  return Promise.resolve({ access_token });
}
