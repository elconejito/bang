import * as _ from 'lodash';

export default {
  methods: {
    initData(original) {
      console.log('HasForm initData()', original, Object.assign({}, this[original]), Object.assign({}, this.original));
      const paths = Object.keys(this[original]);
      this[original] = _.pick(this.original, paths);
      console.log('HasForm initData()', paths, Object.assign({}, this[original]));
    },
    removeEmpties(data) {
      const ret = {};
      Object.keys(data).forEach((key) => {
        if (data[key]) {
          ret[key] = data[key];
        }
      });

      return ret;
    },
  },
};
