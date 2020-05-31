// leave the export, even if you don't use it
const env = (key, fallback) => process.env[key] || fallback;

export default ({ Vue }) => {
  Vue.prototype.$env = env;
};

export { env };
