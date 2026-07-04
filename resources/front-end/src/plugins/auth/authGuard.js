import { useAuthStore } from '@/stores/auth';

export const authGuard = (to, from, next) => {
  const authStore = useAuthStore();
  if (authStore.isAuthenticated) {
    next();
  } else {
    next({ name: 'login' });
  }
};
