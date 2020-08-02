// eslint-disable-next-line
export function get(state) {
  return (model) => (state[model] ? state[model] : []);
}
