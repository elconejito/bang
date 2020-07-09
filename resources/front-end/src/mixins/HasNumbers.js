import numeral from 'numeral';
const QuantityFormat = '0,0';

export default {
  methods: {
    formatQuantity(value) {
      return numeral(value).format(QuantityFormat);
    },
  },
};
