import numeral from 'numeral';
const QuantityFormatSmall = '0,0';
const QuantityFormatMedium = '0.0a';
const QuantityFormatLarge = '0a';

export default {
  methods: {
    formatQuantity(value) {
      if (typeof value === 'undefined') {
        return '-';
      }

      return numeral(value).format(QuantityFormatSmall);
    },
    formatSmartQuantity(value) {
      if (typeof value === 'undefined') {
        return '-';
      }

      let format = QuantityFormatSmall;

      switch (true) {
        case value.toString().length >= 6:
          format = QuantityFormatLarge;
          break;
        case value.toString().length >= 4:
          format = QuantityFormatMedium;
          break;
      }

      return numeral(value).format(format);
    },
  },
};
