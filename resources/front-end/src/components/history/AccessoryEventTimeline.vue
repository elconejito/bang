<script setup>
import { ref, onMounted } from 'vue'
import { Plus } from 'lucide-vue-next'
import LogEventModal from '@/components/history/LogEventModal.vue'
import { useAccessoryEventsStore } from '@/stores/accessoryEvents'

const props = defineProps({
  entityType: { type: String, required: true },
  entityId: { type: Number, required: true },
  manualEventTypes: { type: Array, required: true }, // [{ value, label }]
  historyLabel: { type: String, default: 'HISTORY' },
})

const store = useAccessoryEventsStore()

const events = ref([])
const loading = ref(true)
const showModal = ref(false)

onMounted(async () => {
  const { data } = await store.fetchForEntity(props.entityType, props.entityId)
  events.value = data
  loading.value = false
})

function onEventCreated(event) {
  events.value = [event, ...events.value]
  showModal.value = false
}

const EVENT_CONFIG = {
  ADDED: { label: 'Added to inventory', dot: 'bg-[#2f7d57]', text: 'text-[#2f7d57]' },
  MOUNT: { label: 'Mounted', dot: 'bg-[#6b5a8c]', text: 'text-[#6b5a8c]' },
  UNMOUNT: { label: 'Unmounted', dot: 'bg-[#8a9098]', text: 'text-[#5b6066]' },
  LOAD: { label: 'Loaded', dot: 'bg-[#2f7d57]', text: 'text-[#2f7d57]' },
  UNLOAD: { label: 'Unloaded', dot: 'bg-[#8a9098]', text: 'text-[#5b6066]' },
  CLEAN: { label: 'Cleaned', dot: 'bg-[#2563a8]', text: 'text-[#2563a8]' },
  BATTERY_REPLACE: { label: 'Battery replaced', dot: 'bg-[#b08a2e]', text: 'text-[#7d6320]' },
  REPAIR: { label: 'Repair / Service', dot: 'bg-[#b45a2f]', text: 'text-[#b45a2f]' },
  LOCATION_CHANGE: { label: 'Location changed', dot: 'bg-[#2563a8]', text: 'text-[#2563a8]' },
}

function configFor(event) {
  return EVENT_CONFIG[event.event_type] ?? { label: event.event_type, dot: 'bg-[#c2c6ca]', text: 'text-[#5b6066]' }
}

function formatDate(dateStr) {
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

function titleFor(event) {
  const config = configFor(event)
  if (event.event_type === 'MOUNT' && event.firearm) {
    return `Mounted on ${event.firearm.label ?? event.firearm.manufacturer}`
  }
  if (event.event_type === 'UNMOUNT' && event.firearm) {
    return `Unmounted from ${event.firearm.label ?? event.firearm.manufacturer}`
  }
  return config.label
}
</script>

<template>
  <div class="bg-white border border-[#e2e4e6] rounded-sm overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
      <div class="flex items-center gap-3">
        <span class="font-display font-semibold text-[18px]">History</span>
        <span class="font-mono text-[11px] text-muted tracking-[0.04em]">{{ historyLabel }}</span>
      </div>
      <button
        class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#3a3e44] bg-white border border-[#c2c6ca] rounded px-3 py-1.5 hover:bg-[#f5f6f7] transition-colors"
        @click="showModal = true"
      >
        <Plus class="w-[13px] h-[13px]" />
        Log event
      </button>
    </div>

    <!-- Timeline -->
    <div v-if="loading" class="px-[18px] py-12 text-center text-muted text-[14px]">Loading…</div>

    <div v-else-if="!events.length" class="px-[18px] py-12 text-center text-muted text-[14px]">
      No history yet. Add this item or log an event to start the timeline.
    </div>

    <div v-else class="px-[18px] py-4">
      <div class="relative">
        <!-- Vertical line -->
        <div class="absolute left-[7px] top-2 bottom-2 w-px bg-[#e2e4e6]" />

        <div class="flex flex-col gap-5">
          <div v-for="event in events" :key="event.id" class="flex gap-3 relative">
            <!-- Dot -->
            <div class="flex-none mt-[3px] w-[15px] flex items-start justify-center">
              <div :class="configFor(event).dot" class="w-[9px] h-[9px] rounded-full ring-2 ring-white z-10" />
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0 pb-px">
              <div class="flex items-baseline justify-between gap-2 flex-wrap">
                <span :class="configFor(event).text" class="font-semibold text-[14px]">
                  {{ titleFor(event) }}
                </span>
                <span class="text-[12px] text-[#8a9098] font-mono flex-none">
                  {{ formatDate(event.event_date) }}
                </span>
              </div>
              <p v-if="event.description" class="text-[13px] text-[#6b7077] mt-0.5">{{ event.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <LogEventModal
    v-if="showModal"
    :entity-type="entityType"
    :entity-id="entityId"
    :available-types="manualEventTypes"
    @created="onEventCreated"
    @close="showModal = false"
  />
</template>
