const modelMap = {
  caliberType: 'caliber-type',
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
