export function saveAuthToken(context, payload) {
  const { token } = payload;

  // Save into Axios
  this._vm.$axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
  // Save into State
  context.commit('saveAuthToken', { token });

  return Promise.resolve({ token });
}
