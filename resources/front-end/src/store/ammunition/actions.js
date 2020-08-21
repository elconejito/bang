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

export function store(context, payload) {
  console.log('store.ammunition.actions.store()', payload);
  const { caliberId, data } = payload;
  const getUrl = `/calibers/${caliberId}/ammunition`;

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.ammunition.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.ammunition.actions.update()', payload);
  const { caliberId, data, id } = payload;
  const getUrl = `/calibers/${caliberId}/ammunition/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.ammunition.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
