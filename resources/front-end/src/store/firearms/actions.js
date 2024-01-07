// eslint-disable-next-line
import { queryParams } from '@/plugins/axios';

export function all(context, payload) {
  const { params } = payload;
  const getUrl = `/firearms${queryParams(params)}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.firearms.actions.get()', payload);
  const { firearmId, params } = payload;
  const getUrl = `/firearms/${firearmId}${queryParams(params)}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.firearms.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/firearms';

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.firearms.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/firearms/${id}`;

  return this._vm.$axios.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function getNotes(context, payload) {
  const { firearmId, params } = payload;
  const getUrl = `/firearms/${firearmId}/notes${queryParams(params)}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.getNotes() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function storeNote(context, payload) {
  console.log('store.ammunition.actions.storeNote()', payload);
  const { firearmId, data } = payload;
  const getUrl = `/firearms/${firearmId}/notes`;

  return this._vm.$axios.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.firearms.actions.storeNote() axios then', data, meta, status, statusText);

    return response.data;
  });
}