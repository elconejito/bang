import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

export const useAuthStore = defineStore('auth', () => {
  const token = ref(null);
  const expires = ref(null);
  const authenticated = ref(false);
  const user = ref(null);
  const pictureStorage = ref(null);
  const registrationEnabled = ref(false);
  const passwordResetEnabled = ref(true);

  const isAuthenticated = computed(() => authenticated.value);
  const currentUser = computed(() => user.value);
  const pictureUploadsEnabled = computed(() => pictureStorage.value?.uploads_enabled ?? true);

  async function login(payload) {
    const { data } = await axiosInstance.post('/auth/login', payload);
    saveAuthInformation(data.authorisation);
    await me();
  }

  async function logout() {
    await axiosInstance.post('/auth/logout');
    clearAuthInformation();
  }

  async function register(payload) {
    return axiosInstance.post('/auth/register', payload);
  }

  async function loadPublicConfiguration() {
    const { data } = await axiosInstance.get('/auth/configuration');
    registrationEnabled.value = data.data.registration_enabled;
    passwordResetEnabled.value = data.data.password_reset_enabled;
    return data.data;
  }

  async function forgotPassword(email) {
    return axiosInstance.post('/auth/forgot-password', { email });
  }

  async function resetPassword(payload) {
    return axiosInstance.post('/auth/reset-password', payload);
  }

  async function me() {
    const { data } = await axiosInstance.get('/auth/me');
    user.value = data.data;
    pictureStorage.value = data.meta?.picture_storage ?? null;
    return user.value;
  }

  function saveAuthInformation({ access_token, expires_in }) {
    localStorage.setItem('access_token', access_token);
    axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + access_token;
    token.value = access_token;
    expires.value = expires_in;
    authenticated.value = true;
  }

  async function restoreFromStorage() {
    const savedToken = localStorage.getItem('access_token');
    if (!savedToken) {
      return;
    }

    axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + savedToken;
    token.value = savedToken;
    authenticated.value = true;

    try {
      await me();
    } catch {
      clearAuthInformation();
    }
  }

  function clearAuthInformation() {
    localStorage.removeItem('access_token');
    delete axiosInstance.defaults.headers.common['Authorization'];
    token.value = null;
    expires.value = null;
    authenticated.value = false;
    user.value = null;
    pictureStorage.value = null;
  }

  return {
    token,
    expires,
    authenticated,
    user,
    pictureStorage,
    registrationEnabled,
    passwordResetEnabled,
    isAuthenticated,
    currentUser,
    pictureUploadsEnabled,
    login,
    logout,
    register,
    loadPublicConfiguration,
    forgotPassword,
    resetPassword,
    me,
    saveAuthInformation,
    restoreFromStorage,
  };
});
