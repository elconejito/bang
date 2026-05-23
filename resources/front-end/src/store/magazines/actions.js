import { axiosInstance } from '@/plugins/axios';

export function all(context, payload) {
  const getUrl = '/magazines';

  return axiosInstance.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.magazines.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.magazines.actions.get()', payload);
  const { magazineId } = payload;
  const getUrl = `/magazines/${magazineId}`;

  return axiosInstance.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.magazines.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.magazines.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/magazines';

  return axiosInstance.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.magazines.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.magazines.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/magazines/${id}`;

  return axiosInstance.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.magazines.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
