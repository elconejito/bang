import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

export function useDateTimes() {
  function ago(d) {
    return dayjs(d).fromNow();
  }

  return { ago };
}
