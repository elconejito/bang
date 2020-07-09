export function getToken(state) {
  console.log('store.auth.getters.getToken()', state);
  return state.token ? state.token : undefined;
}
