import numeral from 'numeral';

const FORMAT_SMALL = '0,0';
const FORMAT_MEDIUM = '0.0a';
const FORMAT_LARGE = '0a';

export function useNumbers() {
  function formatQuantity(value) {
    if (typeof value === 'undefined') return '-';
    return numeral(value).format(FORMAT_SMALL);
  }

  function formatSmartQuantity(value) {
    if (typeof value === 'undefined') return '-';

    let format = FORMAT_SMALL;
    const len = value.toString().length;

    if (len >= 6) format = FORMAT_LARGE;
    else if (len >= 4) format = FORMAT_MEDIUM;

    return numeral(value).format(format);
  }

  return { formatQuantity, formatSmartQuantity };
}
