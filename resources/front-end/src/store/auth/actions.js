export function login(context, payload) {
  const url = `/auth/login`;

  return this._vm.$axios.post(url, payload).then((response) => {
    const { status, statusText } = response;
    const { data } = response;
    console.log('store.auth.actions.login() axios then', response, data, status, statusText);

    const { authorisation: authorization, user } = response.data;

    return context
      .dispatch('saveAuthInformation', { token: authorization.token, user })
      .then(() => {
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

  return this._vm.$axios.post(url, payload).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.auth.actions.register() axios then', data, meta, status, statusText);

    return response;
  });
}

export function saveAuthInformation(context, payload) {
  const { token, user } = payload;

  // Save into Axios
  this._vm.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
  // Save into State
  context.commit('saveAuthToken', { token });
  context.commit('saveAuthUser', { user });
  context.commit('saveAuthState', true);

  return Promise.resolve({ token });
}
