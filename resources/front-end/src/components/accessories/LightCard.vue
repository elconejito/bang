<script setup>
import { useRouter } from 'vue-router'
import { ChevronRight } from 'lucide-vue-next'

const props = defineProps({
  light: { type: Object, required: true },
})

const router = useRouter()

function goToEdit(e) {
  e.preventDefault()
  e.stopPropagation()
  router.push({ name: 'LightEdit', params: { light_id: props.light.id } })
}
</script>

<template>
  <router-link
    :to="{ name: 'LightShow', params: { light_id: light.id } }"
    class="block bg-white border border-[#e2e4e6] rounded-sm overflow-hidden flex flex-col cursor-pointer hover:border-[#c2c6ca] hover:shadow-md transition-all duration-150"
  >
    <div class="p-[14px_16px] flex flex-col gap-2 flex-1">
      <span class="font-mono text-[9px] tracking-[0.06em] text-[#8a9098] border border-[#d6d9dc] rounded-sm px-[7px] py-[2px] w-fit">
        LIGHT
      </span>
      <div>
        <div class="font-display font-semibold text-lg leading-tight">{{ light.label }}</div>
        <div class="text-[13px] text-[#6b7077]">{{ light.manufacturer }}</div>
      </div>
      <div class="flex gap-1.5 flex-wrap mt-auto">
        <span v-if="light.lumens" class="text-[12px] border border-[#c2c6ca] rounded-sm px-[9px] py-[1px] text-[#3a3e44] bg-[#f5f6f7]">
          {{ light.lumens.toLocaleString() }} lm
        </span>
        <span v-if="light.battery_type" class="text-[12px] border border-[#c2c6ca] rounded-sm px-[9px] py-[1px] text-[#3a3e44] bg-[#f5f6f7]">
          {{ light.battery_type }}
        </span>
      </div>
    </div>
    <div class="border-t border-[#eef0f1] flex items-center justify-between px-4 py-[9px] bg-[#fafbfb]">
      <span v-if="light.firearm" class="font-mono text-[10px] text-[#2f7d57] border border-[#9ccbb1] bg-[#e7f1eb] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5">
        <span class="w-[5px] h-[5px] rounded-full bg-[#2f7d57]" />
        ON · {{ light.firearm.label ?? light.firearm.manufacturer }}
      </span>
      <span v-else class="font-mono text-[10px] text-[#5b6066] border border-[#c2c6ca] bg-[#f5f6f7] rounded-sm px-[7px] py-[2px] flex items-center gap-1.5">
        <span class="w-[5px] h-[5px] rounded-full border-[1.5px] border-[#8a9098]" />
        OFF · {{ light.location?.label ?? 'Unmounted' }}
      </span>
      <button class="inline-flex items-center gap-1 text-[13px] font-semibold text-[#7d6320]" @click="goToEdit">
        {{ light.firearm ? 'Move' : 'Mount' }}
        <ChevronRight class="h-[13px] w-[13px]" />
      </button>
    </div>
  </router-link>
</template>
