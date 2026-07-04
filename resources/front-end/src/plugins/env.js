const env = (key, fallback) => import.meta.env[key] || fallback;

export default (app) => {
  app.config.globalProperties.$env = env;
};

export { env };
