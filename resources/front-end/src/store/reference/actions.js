const references = [
  'ammunitionCasing',
  'ammunitionCondition',
  'bulletType',
  'caliberType',
  'primerType',
  'purpose',
  'shellLength',
  'shellType',
  'shotMaterial',
];
const modelMap = {
  ammunitionCasing: 'ammunition-casing',
  ammunitionCondition: 'ammunition-condition',
  bulletType: 'bullet-type',
  caliberType: 'caliber-type',
  primerType: 'primer-type',
  purpose: 'purpose',
  shellLength: 'shell-length',
  shellType: 'shell-type',
  shotMaterial: 'shot-material',
};

export function get(context, payload) {
  const { model } = payload;
  const getUrl = `/${modelMap[model]}`;

  return this._vm.$axios.get(getUrl).then((response) => {
    const { status, statusText } = response;
    const { data, meta } = response.data;
    console.log('store.reference.actions.get() axios then', data, meta, status, statusText);

    context.commit('set', { model, data });

    return response.data;
  });
}

export function getAll(context) {
  // Loop through each Reference and retrieve

  references.forEach((ref) => {
    context.dispatch('get', { model: ref });
  });
}
