import state from '@/store/auth/state';
export const authGuard = (to, from, next) => {
  if (state.authenticated) {
    next();
  } else {
    next({ name: 'login' });
  }
};
