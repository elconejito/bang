<template>
  <div v-if="isLoading" class="flex h-screen items-center justify-center">
    <Loading class="text-3xl text-gray-400" />
  </div>
  <div v-else class="min-h-screen bg-canvas pb-[60px]">
    <TopNavigation />
    <div
      v-if="authStore.pictureStorage?.notice"
      class="mx-auto max-w-[1280px] px-4 pt-4 sm:px-6 lg:px-8"
    >
      <PictureStorageNotice :status="authStore.pictureStorage" />
    </div>
    <router-view />
    <SiteFooter />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import TopNavigation from '@/components/navigation/TopNavigation.vue';
import SiteFooter from '@/components/SiteFooter.vue';
import Loading from '@/components/Loading.vue';
import PictureStorageNotice from '@/components/photos/PictureStorageNotice.vue';
import { useLoading } from '@/composables/useLoading';
import { useReferenceStore } from '@/stores/reference';
import { useAuthStore } from '@/stores/auth';

const { isLoading, loadingQueue } = useLoading();
const referenceStore = useReferenceStore();
const authStore = useAuthStore();

onMounted(async () => {
  isLoading.value = true;
  loadingQueue.references = false;
  try {
    await referenceStore.fetchAll();
  } catch (error) {
    console.error('Authenticated: failed to load references', error);
  } finally {
    loadingQueue.references = true;
  }
});
</script>
