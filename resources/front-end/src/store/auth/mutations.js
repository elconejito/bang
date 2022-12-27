export function saveAuthToken(state, data) {
  state.token = data.token;
}

export function saveAuthUser(state, data) {
  state.user = data.user;
}

export function saveAuthState(state, data) {
  state.authenticated = data;
}
