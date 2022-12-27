export default {
  data() {
    return {
      isLoading: false,
      loadingQueue: {},
    };
  },
  watch: {
    loadingQueue: {
      handler: function (value) {
        console.log('HasLoading, watch, loadingQueue', this.isLoading, Object.assign({}, value));
        // We only want to run this when we have set loading to true, meaning we
        // are waiting for it to complete
        if (this.isLoading) {
          // check each item in the array to see if it is complete
          const total = Object.keys(value).length;
          const complete = Object.keys(value).filter((key) => value[key]).length;

          if (total === complete) {
            this.isLoading = false;
          }
        }
      },
      deep: true,
      immediate: true,
    },
  },
};
