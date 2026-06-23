<script setup>
import { ref, computed, onMounted } from 'vue'
import { Upload, Star, Trash2, Link, Info, LoaderCircle } from 'lucide-vue-next'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import LibraryPickerModal from '@/components/gallery/LibraryPickerModal.vue'
import { usePicturesStore } from '@/stores/pictures'

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  crumbs: { type: Array, default: () => [] },
  entityTitle: { type: String, default: null },
})

const picturesStore = usePicturesStore()

const pictures = ref([])
const loading = ref(true)
const showLibraryModal = ref(false)
const uploading = ref(false)
const fileInput = ref(null)
const dragSrcIndex = ref(null)
const dragOverIndex = ref(null)

onMounted(async () => {
  const { data } = await picturesStore.fetchForEntity(props.entityType, props.entityId)
  pictures.value = data
  loading.value = false
})

const attachedIds = computed(() => pictures.value.map((p) => p.id))

async function reloadPictures() {
  const { data } = await picturesStore.fetchForEntity(props.entityType, props.entityId)
  pictures.value = data
}

// Upload
function triggerUpload() {
  fileInput.value?.click()
}

async function onFileSelected(e) {
  const files = Array.from(e.target.files)
  if (!files.length) return
  uploading.value = true
  try {
    for (const file of files) {
      await picturesStore.uploadToEntity(props.entityType, props.entityId, file)
    }
    await reloadPictures()
  } finally {
    uploading.value = false
    e.target.value = ''
  }
}

async function onDropUpload(e) {
  const files = Array.from(e.dataTransfer.files).filter((f) => f.type.startsWith('image/'))
  if (!files.length) return
  uploading.value = true
  try {
    for (const file of files) {
      await picturesStore.uploadToEntity(props.entityType, props.entityId, file)
    }
    await reloadPictures()
  } finally {
    uploading.value = false
  }
}

// Set primary
async function setPrimary(pic) {
  await picturesStore.setPrimaryForEntity(props.entityType, props.entityId, pic.id)
  pictures.value = pictures.value.map((p) => ({ ...p, is_primary: p.id === pic.id }))
}

// Detach
async function detach(pic) {
  await picturesStore.detachFromEntity(props.entityType, props.entityId, pic.id)
  pictures.value = pictures.value.filter((p) => p.id !== pic.id)
  if (pic.is_primary && pictures.value.length) {
    pictures.value[0].is_primary = true
  }
}

// Library attach callback
async function onLibraryAttach() {
  showLibraryModal.value = false
  await reloadPictures()
}

// Drag-to-reorder
function onDragStart(e, index) {
  dragSrcIndex.value = index
  e.dataTransfer.effectAllowed = 'move'
}

function onDragOver(e, index) {
  e.preventDefault()
  e.dataTransfer.dropEffect = 'move'
  dragOverIndex.value = index
}

function onDragLeave() {
  dragOverIndex.value = null
}

async function onDrop(e, index) {
  e.preventDefault()
  if (dragSrcIndex.value === null || dragSrcIndex.value === index) {
    dragSrcIndex.value = null
    dragOverIndex.value = null
    return
  }
  const reordered = [...pictures.value]
  const [moved] = reordered.splice(dragSrcIndex.value, 1)
  reordered.splice(index, 0, moved)
  pictures.value = reordered
  dragSrcIndex.value = null
  dragOverIndex.value = null
  await picturesStore.reorderEntity(props.entityType, props.entityId, reordered.map((p) => p.id))
}

function onDragEnd() {
  dragSrcIndex.value = null
  dragOverIndex.value = null
}
</script>

<template>
  <div class="max-w-[1080px] mx-auto px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-4" />

    <!-- Header -->
    <div class="flex items-center gap-4 mb-2 flex-wrap">
      <h1 class="font-display font-bold text-[28px] tracking-[-0.02em]">Photos</h1>
      <span class="font-mono text-[12px] text-[#8a9098] border border-[#e2e4e6] bg-white rounded-sm px-[9px] py-[3px]">
        {{ pictures.length }} ATTACHED
      </span>
      <div class="ml-auto flex items-center gap-2.5">
        <button
          class="inline-flex items-center gap-1.5 bg-white text-[#1a1c1f] font-semibold text-[14px] px-[14px] py-2 border border-[#c2c6ca] rounded hover:bg-[#f5f6f7] transition-colors"
          @click="showLibraryModal = true"
        >
          <svg class="w-[15px] h-[15px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V10"/></svg>
          Add from Library
        </button>
        <button
          :disabled="uploading"
          class="inline-flex items-center gap-1.5 bg-brass text-[#1a1c1f] font-semibold text-[14px] px-[15px] py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-60 transition-colors"
          @click="triggerUpload"
        >
          <LoaderCircle v-if="uploading" class="h-4 w-4 animate-spin" />
          <Upload v-else class="w-4 h-4" />
          {{ uploading ? 'Uploading…' : 'Upload' }}
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

    <p v-if="entityTitle" class="text-[15px] text-[#6b7077] mb-5">
      Attached to <span class="font-semibold text-[#3a3e44]">{{ entityTitle }}</span>.
      Images live in one shared library — the same photo can appear on multiple items without duplicating it.
    </p>
    <p v-else class="text-[15px] text-[#6b7077] mb-5">
      Images live in one shared library — the same photo can appear on multiple items without duplicating it.
    </p>

    <div v-if="loading" class="py-16 text-center text-sm text-muted">Loading…</div>

    <template v-else>
      <!-- Photo grid -->
      <div class="grid grid-cols-4 gap-3.5">
        <!-- Existing photo tiles -->
        <div
          v-for="(pic, i) in pictures"
          :key="pic.id"
          class="relative rounded overflow-hidden transition-all duration-150"
          :class="[
            pic.is_primary ? 'border-2 border-brass' : 'border border-[#e2e4e6]',
            dragOverIndex === i ? 'ring-2 ring-brass ring-offset-1' : '',
          ]"
          draggable="true"
          @dragstart="onDragStart($event, i)"
          @dragover="onDragOver($event, i)"
          @dragleave="onDragLeave"
          @drop="onDrop($event, i)"
          @dragend="onDragEnd"
        >
          <img :src="pic.url_medium || pic.url" :alt="pic.name" class="w-full h-40 object-cover" />

          <!-- PRIMARY badge -->
          <div
            v-if="pic.is_primary"
            class="absolute top-2 left-2 inline-flex items-center gap-1 bg-brass text-[#1a1c1f] font-mono text-[10px] font-semibold tracking-[0.04em] rounded-sm px-2 py-px"
          >
            <Star class="w-3 h-3 fill-[#1a1c1f] stroke-none" />
            PRIMARY
          </div>

          <!-- Also on badge -->
          <div
            v-if="pic.also_on_count > 0"
            class="absolute inline-flex items-center gap-1 bg-[rgba(26,28,31,0.78)] text-white rounded-sm px-[7px] py-px text-[11px]"
            :class="pic.is_primary ? 'top-8 left-2' : 'top-2 left-2'"
          >
            <Link class="w-[11px] h-[11px]" />
            Also on {{ pic.also_on_count }}
          </div>

          <!-- Action buttons -->
          <div class="absolute bottom-2 right-2 flex gap-1.5">
            <button
              v-if="!pic.is_primary"
              class="w-7 h-7 rounded flex items-center justify-center bg-[rgba(255,255,255,0.92)] border border-[#d6d9dc] text-[#5b6066] hover:text-brass transition-colors"
              title="Set as primary"
              @click="setPrimary(pic)"
            >
              <Star class="w-[15px] h-[15px]" />
            </button>
            <button
              class="w-7 h-7 rounded flex items-center justify-center bg-[rgba(255,255,255,0.92)] border border-[#d6d9dc] text-[#5b6066] hover:text-[#b4452f] transition-colors"
              title="Remove from item"
              @click="detach(pic)"
            >
              <Trash2 class="w-[15px] h-[15px]" />
            </button>
          </div>
        </div>

        <!-- Upload drop tile -->
        <div
          class="border border-dashed border-[#c2c6ca] rounded bg-[#fafbfb] flex flex-col items-center justify-center gap-1.5 h-40 text-[#7d6320] cursor-pointer hover:bg-[#f3f4f5] hover:border-[#a9aeb3] transition-colors"
          @click="triggerUpload"
          @dragover.prevent
          @drop.prevent="onDropUpload"
        >
          <Upload class="w-[22px] h-[22px]" />
          <span class="text-[13px] font-semibold">Upload</span>
          <span class="text-[11px] text-[#8a9098]">or drop files</span>
        </div>
      </div>

      <!-- Info note -->
      <div class="inline-flex items-start gap-2 mt-[18px] text-[13px] text-[#6b7077] bg-white border border-[#e2e4e6] rounded-sm px-3 py-[9px]">
        <Info class="w-[15px] h-[15px] text-[#8a9098] mt-px flex-none" />
        <span>Drag tiles to reorder. The <span class="font-semibold text-[#7d6320]">starred</span> photo is the cover shown on cards &amp; lists. Removing a photo here only detaches it — it stays in your Library.</span>
      </div>
    </template>

    <!-- Library picker modal -->
    <LibraryPickerModal
      v-if="showLibraryModal"
      :entity-type="entityType"
      :entity-id="entityId"
      :attached-ids="attachedIds"
      @attach="onLibraryAttach"
      @close="showLibraryModal = false"
    />
  </div>
</template>
