// eslint-disable-next-line
export function all(context, payload) {
  const getUrl = '/stores';
  console.log('store.stores.actions.all()', payload);

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.stores.actions.all() axios then', data, meta, status, statusText);

    context.commit('set', data);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.stores.actions.get()', payload);
  const { storeId } = payload;
  const getUrl = `/stores/${storeId}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.stores.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.stores.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/stores';

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.stores.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.stores.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/stores/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.stores.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
