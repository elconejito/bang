import dayjs from 'dayjs';

export function set(state, data) {
  state.stores = data;
  state.updatedAt = dayjs();
}
