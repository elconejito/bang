export function all(context, payload) {
  const getUrl = '/calibers';

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.all() axios then', data, meta, status, statusText);
  });
}

export function get(context, payload) {}
