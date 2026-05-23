import { axiosInstance, queryParams } from '@/plugins/axios';

export function all(context, payload) {
  const getUrl = '/calibers';

  return axiosInstance.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.calibers.actions.get()', payload);
  const { caliberId } = payload;
  const getUrl = `/calibers/${caliberId}`;

  return axiosInstance.get(getUrl).then((response) => {
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

  return axiosInstance.get(getUrl).then((response) => {
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

  return axiosInstance.post(getUrl, data).then((response) => {
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

  return axiosInstance.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.calibers.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
