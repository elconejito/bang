<script setup>
import { onMounted, ref } from 'vue';
import { LoaderCircle, Trash2 } from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';
import PhotoLightbox from '@/components/photos/PhotoLightbox.vue';
import PictureStorageNotice from '@/components/photos/PictureStorageNotice.vue';
import { useAuthStore } from '@/stores/auth';
import { usePicturesStore } from '@/stores/pictures';

const picturesStore = usePicturesStore();
const authStore = useAuthStore();
const pictures = ref([]);
const loading = ref(true);
const error = ref(null);
const deletingId = ref(null);
const expandedPicture = ref(null);

async function loadPictures() {
  loading.value = true;
  error.value = null;
  if (!authStore.pictureUploadsEnabled) {
    pictures.value = [];
    loading.value = false;
    return;
  }
  try {
    const response = await picturesStore.fetchLibrary();
    pictures.value = response.data;
  } catch (exception) {
    error.value =
      exception?.response?.data?.message ??
      'The photo library could not be loaded. Check the AWS photo storage configuration.';
  } finally {
    loading.value = false;
  }
}

function deletionReason(picture) {
  if (picture.deletion_reason) return picture.deletion_reason;
  if ((picture.primary_usage_count ?? 0) > 0)
    return 'Choose a new primary on the attached item before deleting this photo.';
  if ((picture.attachments_count ?? 0) > 0)
    return `Remove this photo from ${picture.attachments_count} item${picture.attachments_count === 1 ? '' : 's'} before deleting it.`;
  return null;
}

async function deletePicture(picture) {
  if (deletionReason(picture) || !authStore.pictureUploadsEnabled) return;
  if (
    !window.confirm(
      'Permanently delete this photo? Its large, card, and thumbnail files will be removed.'
    )
  )
    return;
  deletingId.value = picture.id;
  error.value = null;
  try {
    await picturesStore.deletePicture(picture.id);
    await loadPictures();
  } catch (exception) {
    error.value = exception?.response?.data?.message ?? 'The photo could not be deleted.';
  } finally {
    deletingId.value = null;
  }
}

onMounted(loadPictures);
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-4 py-6 pb-16 sm:px-6 lg:px-8">
    <AppBreadcrumb :crumbs="[{ label: 'Account' }, { label: 'Photo Library' }]" class="mb-4" />
    <div class="mb-6">
      <h1 class="font-display text-[28px] font-bold tracking-[-0.02em]">Photo Library</h1>
      <p class="mt-1 text-[15px] text-muted">
        Photos remain here when detached from an item. Only unused photos can be permanently
        deleted.
      </p>
    </div>
    <PictureStorageNotice :status="authStore.pictureStorage" class="mb-4" />
    <div
      v-if="error"
      class="mb-4 rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
      role="alert"
    >
      {{ error }}
      <button v-if="!pictures.length" class="ml-2 underline" @click="loadPictures">
        Try again
      </button>
    </div>
    <div v-if="loading" class="py-16 text-center text-sm text-muted">Loading…</div>
    <div
      v-else-if="!pictures.length"
      class="rounded border border-dashed border-line bg-ink-50 px-6 py-12 text-center text-muted"
    >
      No photos in your library yet.
    </div>
    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <article
        v-for="picture in pictures"
        :key="picture.id"
        class="overflow-hidden rounded border border-line bg-white"
      >
        <button
          type="button"
          class="block w-full"
          :aria-label="`Expand ${picture.name}`"
          @click="expandedPicture = picture"
        >
          <ModelPhoto
            :src="picture.card_url || picture.url_card || picture.url"
            :alt="picture.name"
            family="gallery"
          />
        </button>
        <div class="space-y-2 p-3">
          <div class="truncate text-sm font-semibold" :title="picture.name">{{ picture.name }}</div>
          <div class="text-xs text-muted">
            Attached to {{ picture.attachments_count ?? picture.usage_count ?? 0 }} item{{
              (picture.attachments_count ?? picture.usage_count ?? 0) === 1 ? '' : 's'
            }}
          </div>
          <p v-if="deletionReason(picture)" class="text-xs text-muted">
            {{ deletionReason(picture) }}
          </p>
          <button
            type="button"
            :disabled="
              !authStore.pictureUploadsEnabled ||
              Boolean(deletionReason(picture)) ||
              deletingId === picture.id
            "
            class="inline-flex items-center gap-1.5 rounded border border-line px-2.5 py-1.5 text-xs text-caution hover:bg-caution-bg disabled:cursor-not-allowed disabled:opacity-45"
            :title="
              !authStore.pictureUploadsEnabled
                ? authStore.pictureStorage?.notice
                : deletionReason(picture) || 'Permanently delete photo'
            "
            @click="deletePicture(picture)"
          >
            <LoaderCircle
              v-if="deletingId === picture.id"
              class="h-3.5 w-3.5 animate-spin"
            /><Trash2 v-else class="h-3.5 w-3.5" />Delete permanently
          </button>
        </div>
      </article>
    </div>
    <PhotoLightbox
      v-if="expandedPicture"
      :src="
        expandedPicture.large_url ||
        expandedPicture.url_large ||
        expandedPicture.card_url ||
        expandedPicture.url_card ||
        expandedPicture.url
      "
      :alt="expandedPicture.name"
      @close="expandedPicture = null"
    />
  </div>
</template>
