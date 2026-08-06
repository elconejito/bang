<script setup>
import { computed, nextTick, ref } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
  modelValue: { type: [String, Number, Array], default: () => [] },
  options: { type: Array, default: () => [] },
  multiple: { type: Boolean, default: false },
  label: { type: String, required: true },
  placeholder: { type: String, default: 'Search options' },
  optionLabel: { type: Function, default: (option) => option.label ?? String(option.id) },
  optionValue: { type: Function, default: (option) => option.id },
  inputId: { type: String, required: true },
  describedby: { type: String, default: undefined },
});
const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const search = ref('');
const activeIndex = ref(0);
const input = ref(null);

const selectedValues = computed(() =>
  props.multiple
    ? (Array.isArray(props.modelValue) ? props.modelValue : []).map((value) => String(value))
    : props.modelValue === '' || props.modelValue === null || props.modelValue === undefined
      ? []
      : [String(props.modelValue)]
);
const filteredOptions = computed(() => {
  const needle = search.value.trim().toLowerCase();
  return props.options.filter(
    (option) => !needle || props.optionLabel(option).toLowerCase().includes(needle)
  );
});

function valueOf(option) {
  return String(props.optionValue(option));
}

function labelOf(option) {
  return props.optionLabel(option);
}

function isSelected(option) {
  return selectedValues.value.includes(valueOf(option));
}

function update(values) {
  emit('update:modelValue', props.multiple ? values : (values[0] ?? ''));
}

function choose(option) {
  const value = valueOf(option);
  if (props.multiple) {
    update(
      isSelected(option)
        ? selectedValues.value.filter((selected) => selected !== value)
        : [...selectedValues.value, value]
    );
  } else {
    update([value]);
    open.value = false;
  }
  search.value = '';
}

function clear() {
  update([]);
  search.value = '';
}

function remove(value) {
  update(selectedValues.value.filter((selected) => selected !== String(value)));
}

function handleKeydown(event) {
  if (event.key === 'ArrowDown') {
    event.preventDefault();
    open.value = true;
    activeIndex.value = Math.min(
      activeIndex.value + 1,
      Math.max(filteredOptions.value.length - 1, 0)
    );
  } else if (event.key === 'ArrowUp') {
    event.preventDefault();
    activeIndex.value = Math.max(activeIndex.value - 1, 0);
  } else if (event.key === 'Enter' && open.value && filteredOptions.value[activeIndex.value]) {
    event.preventDefault();
    choose(filteredOptions.value[activeIndex.value]);
  } else if (event.key === 'Escape') {
    if (!open.value) return;
    event.preventDefault();
    event.stopPropagation();
    open.value = false;
  }
}

function focusInput() {
  open.value = true;
  nextTick(() => input.value?.focus());
}
</script>

<template>
  <div class="relative">
    <div
      class="flex min-h-10 flex-wrap items-center gap-1.5 rounded border border-[#c2c6ca] bg-white px-2 py-1.5 focus-within:border-brass-700 focus-within:ring-2 focus-within:ring-brass-200"
      @click="focusInput"
    >
      <span
        v-for="value in selectedValues"
        :key="value"
        class="inline-flex max-w-full items-center gap-1 rounded bg-ink-100 px-2 py-1 text-xs text-ink-700"
      >
        <span class="truncate">{{
          labelOf(options.find((option) => valueOf(option) === value) ?? { id: value })
        }}</span>
        <button
          type="button"
          class="rounded p-0.5 text-muted hover:text-ink-900"
          :aria-label="`Remove ${labelOf(options.find((option) => valueOf(option) === value) ?? { id: value })}`"
          @click.stop="remove(value)"
        >
          <X class="h-3 w-3" />
        </button>
      </span>
      <input
        :id="inputId"
        ref="input"
        v-model="search"
        type="search"
        role="combobox"
        :aria-label="label"
        :aria-describedby="describedby"
        :aria-expanded="open"
        aria-autocomplete="list"
        :aria-controls="`${inputId}-listbox`"
        :aria-activedescendant="
          open && filteredOptions[activeIndex]
            ? `${inputId}-option-${valueOf(filteredOptions[activeIndex])}`
            : undefined
        "
        class="min-w-[120px] flex-1 border-0 bg-transparent px-1 py-1 text-sm outline-none"
        :placeholder="selectedValues.length ? '' : placeholder"
        @focus="open = true"
        @keydown="handleKeydown"
      />
      <button
        v-if="selectedValues.length"
        type="button"
        class="rounded p-1 text-muted hover:text-ink-900"
        aria-label="Clear selected options"
        @click.stop="clear"
      >
        <X class="h-4 w-4" />
      </button>
    </div>
    <div
      v-if="open"
      :id="`${inputId}-listbox`"
      role="listbox"
      :aria-label="label"
      :aria-multiselectable="multiple"
      class="absolute z-10 mt-1 max-h-52 w-full overflow-y-auto rounded border border-line bg-white p-1 shadow-lg"
      @mousedown.prevent
    >
      <button
        v-for="(option, index) in filteredOptions"
        :key="valueOf(option)"
        :id="`${inputId}-option-${valueOf(option)}`"
        type="button"
        role="option"
        :aria-selected="isSelected(option)"
        class="flex w-full items-center justify-between rounded px-3 py-2 text-left text-sm text-ink-700 hover:bg-ink-50"
        :class="{
          'bg-brass-50 text-ink-900': activeIndex === index,
          'font-semibold': isSelected(option),
        }"
        @mouseenter="activeIndex = index"
        @click="choose(option)"
      >
        <span>{{ labelOf(option) }}</span>
        <span v-if="isSelected(option)" aria-hidden="true">✓</span>
      </button>
      <p v-if="filteredOptions.length === 0" class="px-3 py-2 text-sm text-muted">No matches.</p>
    </div>
  </div>
</template>
