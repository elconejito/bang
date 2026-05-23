export function saveAuthToken(state, data) {
  state.token = data.access_token;
  state.expires = data.expires_in;
}

export function saveAuthUser(state, data) {
  state.user = data;
}

export function saveAuthState(state, data) {
  state.authenticated = data;
}
