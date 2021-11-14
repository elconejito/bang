import { queryParams } from '@/plugins/axios';

// eslint-disable-next-line
export function get(context, payload) {
  const { params } = payload;
  const getUrl = `/inventories${queryParams(params)}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.inventories.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.inventories.actions.store()', payload);
  const { data } = payload;
  const getUrl = `/inventories`;

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.inventories.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.inventories.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/inventories/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.inventories.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
