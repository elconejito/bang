import { axiosInstance } from '@/plugins/axios';

export function all(context, payload) {
  const getUrl = '/locations';

  return axiosInstance.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.locations.actions.all() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function get(context, payload) {
  console.log('store.locations.actions.get()', payload);
  const { locationId } = payload;
  const getUrl = `/locations/${locationId}`;

  return axiosInstance.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.locations.actions.get() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function store(context, payload) {
  console.log('store.locations.actions.store()', payload);
  const { data } = payload;
  const getUrl = '/locations';

  return axiosInstance.post(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.locations.actions.store() axios then', data, meta, status, statusText);

    return response.data;
  });
}

export function update(context, payload) {
  console.log('store.locations.actions.update()', payload);
  const { data, id } = payload;
  const getUrl = `/locations/${id}`;

  return axiosInstance.put(getUrl, data).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.locations.actions.update() axios then', data, meta, status, statusText);

    return response.data;
  });
}
