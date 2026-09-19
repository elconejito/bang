<script setup>
import { ChevronDown, Info, Package } from 'lucide-vue-next';
import { ammoLabel } from '@/composables/useAmmunitionHelper';

const props = defineProps({
  firearms: { type: Array, required: true },
  ammunition: { type: Array, required: true },
  suppressors: { type: Array, required: true },
});

const line = defineModel('line', { type: Object, required: true });
const emit = defineEmits(['firearm-change']);

function selectedFirearm() {
  return props.firearms.find((firearm) => firearm.id === Number(line.value.firearm_id));
}

function selectedAmmo() {
  return props.ammunition.find((ammo) => ammo.id === Number(line.value.ammunition_id));
}

function selectedSuppressor() {
  return props.suppressors.find((suppressor) => suppressor.id === Number(line.value.suppressor_id));
}
</script>

<template>
  <div class="border-b border-[#eef0f1] p-4">
    <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]">
      Firearm <span class="font-normal text-muted">· optional</span>
    </label>
    <div class="relative">
      <select
        v-model="line.firearm_id"
        class="w-full appearance-none rounded border border-[#c2c6ca] bg-white px-3 py-[9px] pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        @change="emit('firearm-change', line)"
      >
        <option value="">No firearm selected</option>
        <option v-for="firearm in firearms" :key="firearm.id" :value="firearm.id">
          {{ firearm.label }}
        </option>
      </select>
      <ChevronDown
        class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
      />
    </div>
  </div>

  <div class="grid grid-cols-1 gap-[14px] border-b border-[#eef0f1] p-4 sm:grid-cols-[130px_1fr]">
    <div>
      <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]">
        Rounds <span class="text-red-500">*</span>
      </label>
      <input
        v-model="line.rounds"
        type="number"
        min="1"
        required
        placeholder="0"
        class="w-full rounded border border-[#c2c6ca] bg-white px-3 py-[9px] font-mono text-[18px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
      />
    </div>
    <div>
      <label class="mb-1.5 block text-[14px] font-medium text-[#3a3e44]">
        Ammo used <span class="text-red-500">*</span>
      </label>
      <div class="relative">
        <Package
          class="pointer-events-none absolute left-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-[#7d6320]"
        />
        <select
          v-model="line.ammunition_id"
          required
          class="w-full appearance-none rounded border border-[#c2c6ca] bg-white py-[9px] pl-9 pr-9 text-[15px] outline-none focus:border-brass focus:shadow-[0_0_0_3px_#f4ecd6]"
        >
          <option value="">Select ammunition</option>
          <option v-for="ammo in ammunition" :key="ammo.id" :value="ammo.id">
            {{ ammoLabel(ammo) }}
          </option>
        </select>
        <ChevronDown
          class="pointer-events-none absolute right-3 top-1/2 h-[15px] w-[15px] -translate-y-1/2 text-muted"
        />
      </div>
      <div v-if="selectedAmmo()" class="mt-1 font-mono text-[12px] text-muted">
        {{ selectedAmmo().on_hand.toLocaleString() }} left
      </div>
    </div>
  </div>

  <div class="px-4 pb-[14px] pt-1">
    <div class="mb-1.5 mt-2 font-mono text-[10px] tracking-[0.06em] text-muted">
      APPLY TO INVENTORY
    </div>
    <label
      class="flex cursor-pointer select-none items-center gap-3 border-b border-[#f1f2f3] py-[9px]"
    >
      <input v-model="line.deduct_ammo" type="checkbox" class="peer sr-only" />
      <span
        class="relative h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
      >
        <span
          class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
        ></span>
      </span>
      <span class="min-w-0 flex-1">
        <span class="block text-[14px] font-medium text-[#3a3e44]">Deduct from ammo inventory</span>
        <span class="block text-[12px] text-muted">
          <template v-if="line.rounds && selectedAmmo()">
            −{{ Number(line.rounds).toLocaleString() }} from {{ ammoLabel(selectedAmmo()) }}
          </template>
          <template v-else>Subtract fired rounds from the selected load.</template>
        </span>
      </span>
    </label>
    <label
      v-if="line.firearm_id"
      class="flex cursor-pointer select-none items-center gap-3 border-b border-[#f1f2f3] py-[9px]"
    >
      <input v-model="line.add_firearm_count" type="checkbox" class="peer sr-only" />
      <span
        class="relative h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
      >
        <span
          class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
        ></span>
      </span>
      <span class="min-w-0 flex-1">
        <span class="block text-[14px] font-medium text-[#3a3e44]">
          Add to {{ selectedFirearm()?.label ?? 'firearm' }} round count
        </span>
        <span class="block text-[12px] text-muted">
          <template v-if="line.rounds">+{{ Number(line.rounds).toLocaleString() }} rounds</template>
          <template v-else>Keep firearm lifetime totals in sync.</template>
        </span>
      </span>
    </label>
    <label class="flex cursor-pointer select-none items-start gap-3 py-[9px]">
      <input v-model="line.add_suppressor_count" type="checkbox" class="peer sr-only" />
      <span
        class="relative mt-0.5 h-[23px] w-10 shrink-0 rounded-full border border-[#c2c6ca] bg-[#d6d9dc] transition-colors peer-checked:border-[#b08a2e] peer-checked:bg-brass"
      >
        <span
          class="absolute left-1 top-[3px] h-[15px] w-[15px] rounded-full bg-white transition-transform peer-checked:translate-x-[17px]"
        ></span>
      </span>
      <span class="min-w-0 flex-1">
        <span class="block text-[14px] font-medium text-[#3a3e44]">
          Add to suppressor round count
        </span>
        <span class="block text-[12px] text-muted">
          Use when a suppressor was mounted or used for this session.
        </span>
      </span>
    </label>
    <div v-if="line.add_suppressor_count" class="ml-[52px] mt-1">
      <div class="flex flex-wrap items-center gap-2">
        <div class="relative min-w-[220px]">
          <select
            v-model="line.suppressor_id"
            class="w-full appearance-none rounded border border-[#ddd4ea] bg-[#f7f4fa] px-3 py-2 pr-9 text-[13px] outline-none focus:border-brass"
          >
            <option value="">Select suppressor</option>
            <option v-for="suppressor in suppressors" :key="suppressor.id" :value="suppressor.id">
              {{ suppressor.label }}
            </option>
          </select>
          <ChevronDown
            class="pointer-events-none absolute right-3 top-1/2 h-[14px] w-[14px] -translate-y-1/2 text-muted"
          />
        </div>
        <span
          v-if="selectedSuppressor()?.is_nfa"
          class="rounded-sm bg-[#1a1c1f] px-1 font-mono text-[9px] text-white"
          >NFA</span
        >
      </div>
      <div
        class="mt-2 inline-flex items-start gap-1.5 rounded border border-[#ecdcb4] bg-[#fbf7ec] px-2.5 py-1.5 text-[12px] text-[#6b7077]"
      >
        <Info class="mt-0.5 h-[13px] w-[13px] shrink-0 text-[#a8842f]" />
        <span>Counted for this session only. This does not change mounted status.</span>
      </div>
    </div>
  </div>
</template>
