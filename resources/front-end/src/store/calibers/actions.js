// eslint-disable-next-line
export function all(context, payload) {
  const getUrl = '/calibers';

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}
// eslint-disable-next-line
export function get(context, payload) {
  console.log('store.calibers.actions.get()', payload);
  const { caliberId } = payload;
  const getUrl = `/calibers/${caliberId}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}
