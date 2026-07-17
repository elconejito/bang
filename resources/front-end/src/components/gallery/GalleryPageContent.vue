<script setup>
import { computed, onMounted, ref } from 'vue';
import {
  ArrowLeft,
  ArrowRight,
  CircleAlert,
  Info,
  Link,
  LoaderCircle,
  Star,
  Trash2,
  Upload,
} from 'lucide-vue-next';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import LibraryPickerModal from '@/components/gallery/LibraryPickerModal.vue';
import ModelPhoto from '@/components/photos/ModelPhoto.vue';
import PhotoLightbox from '@/components/photos/PhotoLightbox.vue';
import PictureStorageNotice from '@/components/photos/PictureStorageNotice.vue';
import { useAuthStore } from '@/stores/auth';
import { usePicturesStore } from '@/stores/pictures';

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  crumbs: { type: Array, default: () => [] },
  entityTitle: { type: String, default: null },
});

const picturesStore = usePicturesStore();
const authStore = useAuthStore();
const pictures = ref([]);
const loading = ref(true);
const error = ref(null);
const mutationError = ref(null);
const uploadFailures = ref([]);
const showLibraryModal = ref(false);
const uploading = ref(false);
const pendingPictureId = ref(null);
const fileInput = ref(null);
const dragSrcIndex = ref(null);
const dragOverIndex = ref(null);
const expandedPicture = ref(null);
const announcement = ref('');

const attachedIds = computed(() => pictures.value.map((picture) => picture.id));
const uploadsEnabled = computed(() => authStore.pictureUploadsEnabled);
const modelType = computed(() => props.entityType.replace(/s$/, ''));

function errorMessage(exception, fallback) {
  return exception?.response?.data?.message ?? fallback;
}

async function reloadPictures() {
  const { data } = await picturesStore.fetchForEntity(props.entityType, props.entityId);
  pictures.value = data;
}

async function loadPictures() {
  loading.value = true;
  error.value = null;
  try {
    await reloadPictures();
  } catch (exception) {
    error.value = errorMessage(
      exception,
      'Photos could not be loaded. Check the AWS photo storage configuration.'
    );
  } finally {
    loading.value = false;
  }
}

onMounted(loadPictures);

function triggerUpload() {
  if (!uploadsEnabled.value) return;
  fileInput.value?.click();
}

function openLibrary() {
  if (!uploadsEnabled.value) return;
  showLibraryModal.value = true;
}

async function uploadFiles(files) {
  if (!files.length || !uploadsEnabled.value) return;
  uploading.value = true;
  uploadFailures.value = [];
  for (const file of files) {
    try {
      await picturesStore.uploadToEntity(props.entityType, props.entityId, file);
    } catch (exception) {
      uploadFailures.value.push(
        `${file.name}: ${errorMessage(exception, 'The photo could not be uploaded. Please try again.')}`
      );
    }
  }
  await reloadPictures().catch(() => {});
  uploading.value = false;
}

async function onFileSelected(event) {
  await uploadFiles(Array.from(event.target.files));
  event.target.value = '';
}

async function onDropUpload(event) {
  await uploadFiles(
    Array.from(event.dataTransfer.files).filter((file) => file.type.startsWith('image/'))
  );
}

async function runMutation(picture, action, fallback) {
  mutationError.value = null;
  pendingPictureId.value = picture?.id ?? 'reorder';
  try {
    await action();
    await reloadPictures();
  } catch (exception) {
    mutationError.value = errorMessage(exception, fallback);
    await reloadPictures().catch(() => {});
  } finally {
    pendingPictureId.value = null;
  }
}

async function setPrimary(picture) {
  await runMutation(
    picture,
    () => picturesStore.setPrimaryForEntity(props.entityType, props.entityId, picture.id),
    'The primary photo could not be changed.'
  );
}

async function detach(picture) {
  if (picture.is_primary && pictures.value.length > 1) return;
  if (
    pictures.value.length === 1 &&
    !window.confirm('Remove this photo? This item will return to its default placeholder.')
  )
    return;
  await runMutation(
    picture,
    () => picturesStore.detachFromEntity(props.entityType, props.entityId, picture.id),
    'The photo could not be removed.'
  );
}

async function onLibraryAttach() {
  showLibraryModal.value = false;
  await reloadPictures();
}

async function persistOrder(reordered, movedPicture) {
  pictures.value = reordered;
  await runMutation(
    movedPicture,
    () =>
      picturesStore.reorderEntity(
        props.entityType,
        props.entityId,
        reordered.map((picture) => picture.id)
      ),
    'The photo order could not be saved.'
  );
  announcement.value = `${movedPicture.name} moved to position ${reordered.findIndex((picture) => picture.id === movedPicture.id) + 1}.`;
}

async function movePicture(index, offset) {
  const destination = index + offset;
  if (destination < 0 || destination >= pictures.value.length) return;
  const reordered = [...pictures.value];
  const [moved] = reordered.splice(index, 1);
  reordered.splice(destination, 0, moved);
  await persistOrder(reordered, moved);
}

function onDragStart(event, index) {
  dragSrcIndex.value = index;
  event.dataTransfer.effectAllowed = 'move';
}
function onDragOver(event, index) {
  event.preventDefault();
  dragOverIndex.value = index;
}
async function onDrop(event, index) {
  event.preventDefault();
  const source = dragSrcIndex.value;
  dragSrcIndex.value = null;
  dragOverIndex.value = null;
  if (source === null || source === index) return;
  const reordered = [...pictures.value];
  const [moved] = reordered.splice(source, 1);
  reordered.splice(index, 0, moved);
  await persistOrder(reordered, moved);
}
</script>

<template>
  <div class="mx-auto max-w-[1080px] px-4 py-6 pb-16 sm:px-6 lg:px-8">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />
    <div class="mb-2 flex flex-wrap items-center gap-4">
      <h1 class="font-display text-[28px] font-bold tracking-[-0.02em]">Photos</h1>
      <span
        class="rounded-sm border border-line bg-white px-[9px] py-[3px] font-mono text-[12px] text-muted"
        >{{ pictures.length }} ATTACHED</span
      >
      <div class="flex w-full flex-wrap gap-2.5 sm:ml-auto sm:w-auto">
        <button
          type="button"
          :disabled="!uploadsEnabled"
          aria-label="Add photos from library"
          :title="uploadsEnabled ? 'Add photos from the library' : authStore.pictureStorage?.notice"
          class="rounded border border-[#c2c6ca] bg-white px-[14px] py-2 text-[14px] font-semibold hover:bg-ink-50 disabled:cursor-not-allowed disabled:opacity-50"
          @click="openLibrary"
        >
          Add from Library
        </button>
        <button
          type="button"
          :disabled="uploading || !uploadsEnabled"
          aria-label="Upload photos"
          :title="uploadsEnabled ? 'Upload photos' : authStore.pictureStorage?.notice"
          class="inline-flex items-center gap-1.5 rounded border border-[#b08a2e] bg-brass px-[15px] py-2 text-[14px] font-semibold disabled:opacity-60"
          @click="triggerUpload"
        >
          <LoaderCircle v-if="uploading" class="h-4 w-4 animate-spin" /><Upload
            v-else
            class="h-4 w-4"
          />{{ uploading ? 'Uploading…' : 'Upload' }}
        </button>
        <input
          ref="fileInput"
          type="file"
          accept="image/*"
          multiple
          class="hidden"
          @change="onFileSelected"
        />
      </div>
    </div>
    <p class="mb-5 text-[15px] text-muted">
      {{ entityTitle ? `Attached to ${entityTitle}. ` : '' }}Images live in one shared library and
      may be reused without duplication.
    </p>
    <PictureStorageNotice :status="authStore.pictureStorage" class="mb-5" />

    <div v-if="loading" class="py-16 text-center text-sm text-muted">Loading…</div>
    <div
      v-else-if="error"
      class="rounded border border-caution-border bg-caution-bg p-4 text-sm text-caution"
      role="alert"
    >
      {{ error }} <button class="ml-2 underline" @click="loadPictures">Try again</button>
    </div>
    <template v-else>
      <div
        v-if="mutationError"
        class="mb-4 rounded border border-caution-border bg-caution-bg p-3 text-sm text-caution"
        role="alert"
      >
        {{ mutationError }}
      </div>
      <div
        v-if="uploadFailures.length"
        class="mb-4 flex items-start gap-2.5 rounded border border-caution-border bg-caution-bg p-4 text-caution"
        role="alert"
        aria-live="assertive"
      >
        <CircleAlert class="mt-0.5 h-5 w-5 shrink-0" aria-hidden="true" />
        <div>
          <p class="text-sm font-semibold">Photo upload failed</p>
          <ul class="mt-1 list-disc space-y-1 pl-5 text-sm">
            <li v-for="failure in uploadFailures" :key="failure">{{ failure }}</li>
          </ul>
        </div>
      </div>
      <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <article
          v-for="(picture, index) in pictures"
          :key="picture.id"
          class="relative overflow-hidden rounded"
          :class="[
            picture.is_primary ? 'border-2 border-brass' : 'border border-line',
            dragOverIndex === index ? 'ring-2 ring-brass ring-offset-1' : '',
          ]"
          draggable="true"
          @dragstart="onDragStart($event, index)"
          @dragover="onDragOver($event, index)"
          @dragleave="dragOverIndex = null"
          @drop="onDrop($event, index)"
          @dragend="dragSrcIndex = null"
        >
          <button
            type="button"
            class="block w-full bg-ink-100"
            :aria-label="`Expand ${picture.name}`"
            @click="expandedPicture = picture"
          >
            <ModelPhoto
              :src="picture.card_url || picture.url_card || picture.url_medium || picture.url"
              :alt="picture.name"
              :model-type="modelType"
              family="gallery"
            />
          </button>
          <div
            v-if="picture.is_primary"
            class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-sm bg-brass px-2 py-px font-mono text-[10px] font-semibold"
          >
            <Star class="h-3 w-3 fill-current" />PRIMARY
          </div>
          <div
            v-if="picture.attachments_count > 1 || picture.also_on_count > 0"
            class="absolute left-2 top-8 inline-flex items-center gap-1 rounded-sm bg-black/75 px-[7px] py-px text-[11px] text-white"
          >
            <Link class="h-3 w-3" />Also on
            {{ Math.max((picture.attachments_count || 1) - 1, picture.also_on_count || 0) }}
          </div>
          <div class="absolute bottom-2 right-2 flex gap-1.5">
            <button
              type="button"
              :disabled="index === 0 || pendingPictureId"
              class="photo-action"
              :aria-label="`Move ${picture.name} earlier`"
              @click="movePicture(index, -1)"
            >
              <ArrowLeft class="h-3.5 w-3.5" />
            </button>
            <button
              type="button"
              :disabled="index === pictures.length - 1 || pendingPictureId"
              class="photo-action"
              :aria-label="`Move ${picture.name} later`"
              @click="movePicture(index, 1)"
            >
              <ArrowRight class="h-3.5 w-3.5" />
            </button>
            <button
              v-if="!picture.is_primary"
              type="button"
              :disabled="pendingPictureId"
              class="photo-action"
              :aria-label="`Set ${picture.name} as primary`"
              @click="setPrimary(picture)"
            >
              <Star class="h-4 w-4" />
            </button>
            <button
              type="button"
              :disabled="pendingPictureId || (picture.is_primary && pictures.length > 1)"
              class="photo-action disabled:cursor-not-allowed disabled:opacity-50"
              :title="
                picture.is_primary && pictures.length > 1
                  ? 'Choose another primary before removing this photo.'
                  : 'Remove from item'
              "
              :aria-label="
                picture.is_primary && pictures.length > 1
                  ? `Choose another primary before removing ${picture.name}`
                  : `Remove ${picture.name} from item`
              "
              @click="detach(picture)"
            >
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </article>
        <button
          type="button"
          :disabled="!uploadsEnabled"
          class="flex aspect-[4/3] flex-col items-center justify-center gap-1.5 rounded border border-dashed border-[#c2c6ca] bg-[#fafbfb] text-[#7d6320] hover:bg-ink-50 disabled:cursor-not-allowed disabled:opacity-50"
          @click="triggerUpload"
          @dragover.prevent
          @drop.prevent="onDropUpload"
        >
          <Upload class="h-6 w-6" /><span class="text-[13px] font-semibold">Upload</span
          ><span class="text-[11px] text-muted">or drop files</span>
        </button>
      </div>
      <div
        class="mt-[18px] inline-flex items-start gap-2 rounded-sm border border-line bg-white px-3 py-[9px] text-[13px] text-muted"
      >
        <Info class="mt-px h-4 w-4 shrink-0" /><span
          >Reorder with drag and drop or the arrow controls. Removing a photo only detaches it; it
          stays in your Library.</span
        >
      </div>
      <p class="sr-only" aria-live="polite">{{ announcement }}</p>
    </template>

    <LibraryPickerModal
      v-if="showLibraryModal"
      :entity-type="entityType"
      :entity-id="entityId"
      :attached-ids="attachedIds"
      @attach="onLibraryAttach"
      @close="showLibraryModal = false"
    />
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

<style scoped>
.photo-action {
  align-items: center;
  background: rgb(255 255 255 / 95%);
  border: 1px solid #d6d9dc;
  border-radius: 0.25rem;
  color: #5b6066;
  display: flex;
  height: 1.75rem;
  justify-content: center;
  transition: color 150ms;
  width: 1.75rem;
}
.photo-action:hover {
  color: #c2a14d;
}
.photo-action:disabled {
  opacity: 0.4;
}
</style>
