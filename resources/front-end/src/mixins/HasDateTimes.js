import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
dayjs.extend(relativeTime);

export default {
  methods: {
    ago(d) {
      return dayjs(d).fromNow();
    },
  },
};
