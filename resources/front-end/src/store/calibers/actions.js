// eslint-disable-next-line
import { queryParams } from '@/plugins/axios';

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

export function total(context, payload) {
  const { caliberId, params } = payload;
  const getUrl = `/calibers/${caliberId}/total${queryParams(params)}`;
  console.log('store.calibers.actions.total()', caliberId, params);

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.total() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.calibers.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/calibers';

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.calibers.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/calibers/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
