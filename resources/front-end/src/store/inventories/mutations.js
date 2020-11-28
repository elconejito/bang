export function set(state, { model, data }) {
  state[model] = data || [];
}
