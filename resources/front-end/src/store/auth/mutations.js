export function saveAuthToken(state, data) {
  state.token = data.expires_in;
  state.expires = data.expires_in;
}

export function saveAuthUser(state, data) {
  state.user = data.user;
}

export function saveAuthState(state, data) {
  state.authenticated = data;
}
