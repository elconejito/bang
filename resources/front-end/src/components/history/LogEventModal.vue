<script setup>
import { ref } from 'vue'
import { X } from 'lucide-vue-next'
import { useAccessoryEventsStore } from '@/stores/accessoryEvents'

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  availableTypes: { type: Array, required: true }, // [{ value, label }]
})

const emit = defineEmits(['created', 'close'])

const store = useAccessoryEventsStore()

const today = new Date().toISOString().split('T')[0]
const form = ref({ event_type: props.availableTypes[0]?.value ?? '', event_date: today, description: '' })
const saving = ref(false)
const error = ref(null)

async function submit() {
  if (!form.value.event_type || !form.value.event_date) return
  saving.value = true
  error.value = null
  try {
    const { data } = await store.createForEntity(props.entityType, props.entityId, {
      event_type: form.value.event_type,
      event_date: form.value.event_date,
      description: form.value.description || null,
    })
    emit('created', data)
  } catch {
    error.value = 'Failed to log event. Please try again.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-[rgba(20,22,26,0.46)] flex items-start justify-center p-12 z-40 overflow-auto" @click.self="emit('close')">
    <div class="w-[440px] max-w-full bg-white border border-[#d6d9dc] rounded shadow-[0_10px_30px_rgba(20,22,26,0.22)] overflow-hidden">
      <!-- Header -->
      <div class="flex items-center justify-between gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
        <div class="font-display font-semibold text-[18px]">Log event</div>
        <button class="text-[#8a9098] hover:text-[#1a1c1f] transition-colors p-0.5" @click="emit('close')">
          <X class="w-[18px] h-[18px]" />
        </button>
      </div>

      <!-- Form -->
      <div class="px-[18px] py-5 flex flex-col gap-4">
        <!-- Event type -->
        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Event type</label>
          <select
            v-model="form.event_type"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          >
            <option v-for="t in availableTypes" :key="t.value" :value="t.value">{{ t.label }}</option>
          </select>
        </div>

        <!-- Date -->
        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Date</label>
          <input
            v-model="form.event_date"
            type="date"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6]"
          />
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-[13px] font-semibold text-[#3a3e44] mb-1.5">Notes <span class="font-normal text-[#8a9098]">(optional)</span></label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="Any additional details…"
            class="w-full border border-[#c2c6ca] rounded px-3 py-[9px] text-[14px] bg-white focus:outline-none focus:border-brass focus:ring-[3px] focus:ring-[#f4ecd6] resize-none"
          />
        </div>

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
          :disabled="saving || !form.event_type || !form.event_date"
          class="font-semibold text-[14px] bg-brass text-[#1a1c1f] px-4 py-2 border border-[#b08a2e] rounded hover:bg-[#b8902f] disabled:opacity-50 transition-colors"
          @click="submit"
        >
          {{ saving ? 'Saving…' : 'Log event' }}
        </button>
      </div>
    </div>
  </div>
</template>
