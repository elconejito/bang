<script setup>
import { ref, computed } from 'vue';
import { X, ImagePlus } from 'lucide-vue-next';
import { useTrainingStore } from '@/stores/training';
import ActionButton from '@/components/ActionButton.vue';
import FormError from '@/components/FormError.vue';

const props = defineProps({
  trainingId: { type: Number, required: true },
});

const emit = defineEmits(['close', 'created']);

const trainingStore = useTrainingStore();

const saving = ref(false);
const error = ref(null);
const imageFile = ref(null);
const imagePreview = ref(null);
const fileInput = ref(null);

const form = ref({
  label: '',
  distance: '',
  group_size: '',
});

const canSubmit = computed(() => imageFile.value && form.value.distance && form.value.group_size);

function onFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
}

function onDrop(e) {
  const file = e.dataTransfer.files[0];
  if (!file || !file.type.startsWith('image/')) return;
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
}

async function submit() {
  if (!canSubmit.value) return;
  saving.value = true;
  error.value = null;
  try {
    const formData = new FormData();
    formData.append('image', imageFile.value);
    formData.append('distance', form.value.distance);
    formData.append('group_size', form.value.group_size);
    if (form.value.label.trim()) formData.append('label', form.value.label.trim());

    const result = await trainingStore.addTarget(props.trainingId, formData);
    emit('created', result.data);
  } catch (e) {
    error.value = e;
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-start justify-center overflow-auto bg-[rgba(20,22,26,0.46)] px-6 pb-6 pt-14"
      @click.self="$emit('close')"
    >
      <div
        class="w-[480px] max-w-full overflow-hidden rounded border border-[#d6d9dc] bg-white shadow-[0_10px_30px_rgba(20,22,26,0.22),0_2px_8px_rgba(20,22,26,0.12)]"
      >
        <!-- Header -->
        <div
          class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4"
        >
          <span class="font-display text-[19px] font-semibold">Add target</span>
          <button class="p-0.5 text-muted hover:text-ink-900" @click="$emit('close')">
            <X class="h-[18px] w-[18px]" />
          </button>
        </div>

        <form @submit.prevent="submit">
          <div class="flex flex-col gap-4 p-[18px]">
            <!-- Image drop zone -->
            <div
              class="relative overflow-hidden rounded border-2 border-dashed border-[#c2c6ca] transition-colors hover:border-brass cursor-pointer"
              :class="imagePreview ? 'border-solid border-[#c2c6ca]' : ''"
              style="min-height: 180px"
              @click="fileInput.click()"
              @dragover.prevent
              @drop.prevent="onDrop"
            >
              <img
                v-if="imagePreview"
                :src="imagePreview"
                class="w-full object-cover"
                style="max-height: 260px"
                alt="Target preview"
              />
              <div v-else class="flex flex-col items-center justify-center gap-2 py-10 text-muted">
                <ImagePlus class="h-8 w-8 text-ink-300" />
                <span class="text-[13px]">Click or drop a photo</span>
              </div>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onFileChange"
              />
            </div>

            <!-- Distance + Group size -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                  >Distance (yds) <span class="text-red-500">*</span></label
                >
                <input
                  v-model="form.distance"
                  type="number"
                  min="0"
                  step="any"
                  placeholder="25"
                  required
                  class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] font-mono focus:outline-none focus:border-brass"
                />
              </div>
              <div>
                <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                  >Group size (in) <span class="text-red-500">*</span></label
                >
                <input
                  v-model="form.group_size"
                  type="number"
                  min="0"
                  step="any"
                  placeholder="1.5"
                  required
                  class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] font-mono focus:outline-none focus:border-brass"
                />
              </div>
            </div>

            <!-- Label -->
            <div>
              <label class="block text-[13px] font-medium text-[#3a3e44] mb-1"
                >Label <span class="font-normal text-[#8a9098]">(optional)</span></label
              >
              <input
                v-model="form.label"
                type="text"
                placeholder="e.g. 50 yd cold bore"
                class="w-full rounded-sm border border-[#c2c6ca] px-3 py-2 text-[14px] focus:outline-none focus:border-brass"
              />
            </div>
          </div>

          <FormError v-if="error" :error="error" class="mx-[18px] mb-4" />

          <!-- Footer -->
          <div
            class="flex items-center gap-2.5 border-t border-[#eef0f1] bg-[#fafbfb] px-[18px] py-[14px]"
          >
            <ActionButton
              text="Save target"
              :is-loading="saving"
              :disabled="!canSubmit"
              variant="primary"
              type="submit"
            />
            <button
              type="button"
              class="rounded border border-[#c2c6ca] bg-white px-[14px] py-[8px] text-[14px] font-semibold text-ink-700 hover:bg-[#f5f6f7] transition-colors"
              @click="$emit('close')"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
