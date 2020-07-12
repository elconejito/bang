export function all(context, payload) {
  const { caliberId } = payload;
  const getUrl = `/calibers/${caliberId}/ammunition`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.ammunition.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  const { caliberId, ammunitionId } = payload;
  const getUrl = `/calibers/${caliberId}/ammunition/${ammunitionId}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.ammunition.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}
