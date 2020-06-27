export function saveAuthToken(context, payload) {
  const { token } = payload;

  context.commit('saveAuthToken', { token });

  this._vm.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

  return Promise.resolve({ token });
}
