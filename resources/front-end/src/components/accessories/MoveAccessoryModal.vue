<script setup>
import { ref, computed, onMounted } from 'vue'
import { LoaderCircle, X } from 'lucide-vue-next'
import { useFirearmsStore } from '@/stores/firearms'

const props = defineProps({
  currentFirearmId: { type: Number, default: null },
  accessoryLabel: { type: String, default: 'accessory' },
})

const emit = defineEmits(['move', 'close'])

const firearmsStore = useFirearmsStore()

const firearms = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref(null)
const selected = ref(props.currentFirearmId ?? '')

const unchanged = computed(() => {
  const next = selected.value === '' ? null : Number(selected.value)
  return next === (props.currentFirearmId ?? null)
})

onMounted(async () => {
  try {
    const { data } = await firearmsStore.fetchAll()
    firearms.value = data ?? []
  } finally {
    loading.value = false
  }
})

async function submit() {
  if (unchanged.value) {
    emit('close')
    return
  }
  saving.value = true
  error.value = null
  try {
    await emit('move', selected.value === '' ? null : Number(selected.value))
  } catch {
    error.value = 'Failed to move. Please try again.'
    saving.value = false
  }
}
</script>

<template>
  <div
    class="fixed inset-0 bg-[rgba(20,22,26,0.46)] flex items-start justify-center p-12 z-40 overflow-auto"
    @click.self="emit('close')"
  >
    <div class="w-[440px] max-w-full bg-white border border-[#d6d9dc] rounded shadow-[0_10px_30px_rgba(20,22,26,0.22)] overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
        <div class="font-display font-semibold text-[18px]">Move {{ accessoryLabel }}</div>
        <button class="text-[#8a9098] hover:text-[#1a1c1f] transition-colors p-0.5" @click="emit('close')">
          <X class="w-[18px] h-[18px]" />
        </button>
      </div>

      <!-- Body -->
      <div class="px-[18px] py-5 flex flex-col gap-4">
        <div v-if="loading" class="text-[14px] text-muted py-4 text-center">Loading firearms…</div>
        <template v-else>
          <div>
            <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Mount on firearm</label>
            <select
              v-model="selected"
              class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
            >
              <option value="">Unmounted (no firearm)</option>
              <option v-for="f in firearms" :key="f.id" :value="f.id">
                {{ f.label ?? f.manufacturer }}
              </option>
            </select>
          </div>
          <p class="text-[13px] text-muted">Moving records a mount entry in this item's history.</p>
        </template>

        <div v-if="error" class="text-[13px] text-[#b4452f]">{{ error }}</div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end gap-2.5 px-[18px] py-3.5 border-t border-[#eef0f1] bg-[#fafbfb]">
        <button
          class="font-semibold text-[14px] bg-white text-[#3a3e44] px-4 py-2 border border-[#c2c6ca] rounded hover:bg-[#f5f6f7] transition-colors"
          @click="emit('close')"
        >
          Cancel
        </button>
        <button
          :disabled="saving || loading"
          class="inline-flex items-center justify-center gap-2 font-semibold text-[14px] bg-brass text-[#1a1c1f] px-4 py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-50 transition-colors"
          @click="submit"
        >
          <LoaderCircle v-if="saving" class="h-4 w-4 animate-spin" />
          {{ saving ? 'Moving…' : 'Move' }}
        </button>
      </div>
    </div>
  </div>
</template>
