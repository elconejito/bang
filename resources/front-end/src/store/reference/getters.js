// eslint-disable-next-line
export function get(state) {
  return (model) => (state[model] ? state[model] : []);
}

export function ammunitionCasing(state) {
  return state.ammunitionCasing;
}

export function ammunitionCondition(state) {
  return state.ammunitionCondition;
}

export function bulletType(state) {
  return state.bulletType;
}

export function caliberType(state) {
  return state.caliberType;
}

export function primerType(state) {
  return state.primerType;
}

export function purpose(state) {
  return state.purpose;
}

export function shellLength(state) {
  return state.shellLength;
}

export function shellType(state) {
  return state.shellType;
}

export function shotMaterial(state) {
  return state.shotMaterial;
}
