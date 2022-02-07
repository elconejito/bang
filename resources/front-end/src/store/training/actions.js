// eslint-disable-next-line
export function all(context, payload) {
  const getUrl = '/training';

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.training.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.training.actions.get()', payload);
  const { magazineId } = payload;
  const getUrl = `/training/${magazineId}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.training.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.training.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/training';

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.training.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.training.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/training/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.training.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
