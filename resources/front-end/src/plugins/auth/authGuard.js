import store from '@/store';

export const authGuard = (to, from, next) => {
  if (store.getters['auth/isAuthenticated']) {
    next();
  } else {
    next({ name: 'login' });
  }
};
