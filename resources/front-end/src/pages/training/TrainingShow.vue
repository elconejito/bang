<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  Calendar,
  Check,
  ChevronDown,
  Home,
  MapPin,
  MessageSquareText,
  Package,
  Pencil,
  Plus,
  Trash2,
} from 'lucide-vue-next';
import dayjs from 'dayjs';
import AppBreadcrumb from '@/components/AppBreadcrumb.vue';
import AddSessionLineModal from '@/components/training/AddSessionLineModal.vue';
import EditSessionLineModal from '@/components/training/EditSessionLineModal.vue';
import AddTargetModal from '@/components/training/AddTargetModal.vue';
import NotesPanel from '@/components/notes/NotesPanel.vue';
import { useTrainingStore } from '@/stores/training';

const props = defineProps({
  trainingId: { type: Number, required: true },
});

function formatCurrency(n) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0,
  }).format(n ?? 0);
}

function firearmSubtitle(firearm) {
  if (!firearm) return '—';

  const caliberLabel = firearm.calibers
    ?.map((caliber) => caliber.label || caliber.caliber)
    .filter(Boolean)
    .join(', ');

  return [firearm.manufacturer, firearm.model, caliberLabel].filter(Boolean).join(' · ') || '—';
}

function ammunitionLabel(ammunition) {
  if (!ammunition) return '—';

  const weight = ammunition.weight ? `${ammunition.weight}gr` : null;
  const bulletType = ammunition.bullet_type?.abbreviation || ammunition.bullet_type?.label;

  return [ammunition.manufacturer, ammunition.label, weight, bulletType].filter(Boolean).join(' ');
}

const trainingStore = useTrainingStore();

const session = ref(null);
const loading = ref(true);
const editingLine = ref(null);
const addingLine = ref(false);
const addingTarget = ref(false);
const deletingTargetId = ref(null);
const expandedLineNotes = ref(new Set());

async function loadSession() {
  const { data } = await trainingStore.fetchOne(props.trainingId);
  session.value = data;
}

onMounted(async () => {
  await loadSession();
  loading.value = false;
});

const crumbs = computed(() => [
  { label: 'Home', to: '/' },
  { label: 'Training', to: { name: 'TrainingIndex' } },
  { label: session.value?.label ?? '…' },
]);

function aggregateRounds(lines, entityKey, idKey) {
  const totals = new Map();

  for (const line of lines) {
    const entity = line[entityKey];
    const entityId = line[idKey] ?? entity?.id;

    if (entityId == null || !entity) continue;

    const aggregate = totals.get(entityId) ?? { entity, rounds: 0 };
    aggregate.rounds += Number(line.rounds) || 0;
    totals.set(entityId, aggregate);
  }

  return [...totals.values()];
}

const ammunitionDeductions = computed(() =>
  aggregateRounds(
    (session.value?.lines ?? []).filter((line) => line.deduct_ammo),
    'ammunition',
    'ammunition_id'
  )
);

const firearmCounts = computed(() =>
  aggregateRounds(
    (session.value?.lines ?? []).filter((line) => line.add_firearm_count),
    'firearm',
    'firearm_id'
  )
);

const linesWithSuppressorCount = computed(() =>
  (session.value?.lines ?? []).filter((l) => l.add_suppressor_count && l.suppressor)
);

async function onLineCreated() {
  addingLine.value = false;
  await loadSession();
}

async function onLineUpdated() {
  editingLine.value = null;
  await loadSession();
}

async function onLineDeleted() {
  editingLine.value = null;
  await loadSession();
}

function onTargetCreated(target) {
  addingTarget.value = false;
  session.value.targets.push(target);
  session.value.target_count = (session.value.target_count ?? 0) + 1;
}

async function deleteTarget(targetId) {
  deletingTargetId.value = targetId;
  try {
    await trainingStore.deleteTarget(props.trainingId, targetId);
    session.value.targets = session.value.targets.filter((t) => t.id !== targetId);
    session.value.target_count = Math.max(0, (session.value.target_count ?? 1) - 1);
  } finally {
    deletingTargetId.value = null;
  }
}

function toggleLineNotes(lineId) {
  const expanded = new Set(expandedLineNotes.value);
  expanded.has(lineId) ? expanded.delete(lineId) : expanded.add(lineId);
  expandedLineNotes.value = expanded;
}
</script>

<template>
  <div class="mx-auto max-w-[1280px] px-8 py-6 pb-16">
    <AppBreadcrumb :crumbs="crumbs" class="mb-5" />

    <div v-if="loading" class="text-sm text-muted py-12 text-center">Loading…</div>

    <template v-else-if="session">
      <!-- Header -->
      <div class="mb-[22px] flex flex-wrap items-start gap-4">
        <div class="flex-1 min-w-0">
          <h1 class="font-display text-[28px] font-bold leading-tight tracking-[-0.02em]">
            {{ session.label }}
          </h1>
          <div
            class="mt-1 flex flex-wrap items-center gap-x-[14px] gap-y-1 text-[14px] text-[#6b7077]"
          >
            <span class="inline-flex items-center gap-1.5">
              <Calendar class="h-[15px] w-[15px] text-muted" />
              {{ dayjs(session.session_date).format('ddd, MMM D, YYYY') }}
            </span>
            <span v-if="session.range" class="inline-flex items-center gap-1.5">
              <MapPin class="h-[15px] w-[15px] text-muted" />
              {{ session.range.label }}
            </span>
          </div>
        </div>
        <router-link
          :to="{ name: 'TrainingEdit', params: { training_id: session.id } }"
          class="detail-action"
        >
          <Pencil class="h-[15px] w-[15px]" />
          Edit
        </router-link>
      </div>

      <!-- Two-col layout -->
      <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[344px_1fr]">
        <!-- Left -->
        <div class="flex flex-col gap-4">
          <!-- Stat grid -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="grid grid-cols-2">
              <div class="border-b border-r border-[#eef0f1] px-4 py-[15px]">
                <div class="font-mono text-[26px] font-medium leading-none">
                  {{ session.total_rounds.toLocaleString() }}
                </div>
                <div class="mt-[3px] font-mono text-[10px] tracking-[0.05em] text-muted">
                  ROUNDS
                </div>
              </div>
              <div class="border-b border-[#eef0f1] px-4 py-[15px]">
                <div class="font-mono text-[26px] font-medium leading-none">
                  {{ session.firearms_count }}
                </div>
                <div class="mt-[3px] font-mono text-[10px] tracking-[0.05em] text-muted">
                  FIREARMS
                </div>
              </div>
              <div class="border-r border-[#eef0f1] px-4 py-[15px]">
                <div class="font-mono text-[26px] font-medium leading-none">
                  {{ session.ammo_cost > 0 ? `≈${formatCurrency(session.ammo_cost)}` : '—' }}
                </div>
                <div class="mt-[3px] font-mono text-[10px] tracking-[0.05em] text-muted">
                  AMMO COST
                </div>
              </div>
              <div class="px-4 py-[15px]">
                <div class="font-mono text-[26px] font-medium leading-none">
                  {{ session.target_count }}
                </div>
                <div class="mt-[3px] font-mono text-[10px] tracking-[0.05em] text-muted">
                  TARGETS
                </div>
              </div>
            </div>
          </div>

          <!-- Applied to your data -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex items-center gap-2 border-b border-[#eef0f1] bg-[#fafbfb] px-4 py-3">
              <Check class="h-[15px] w-[15px] text-[#2f7d57]" />
              <span class="font-display text-[16px] font-semibold">Applied to your data</span>
            </div>

            <!-- Ammo deducted -->
            <div v-if="ammunitionDeductions.length" class="px-4 py-3 border-b border-[#f1f2f3]">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">
                AMMO DEDUCTED
              </div>
              <div
                v-for="deduction in ammunitionDeductions"
                :key="deduction.entity.id"
                data-testid="ammo-deduction"
                class="flex items-center justify-between gap-3 border-b border-[#f1f2f3] py-1.5 text-[14px] last:border-b-0"
              >
                <span class="min-w-0 truncate text-[#3a3e44]">{{
                  deduction.entity.label ?? '—'
                }}</span>
                <span class="shrink-0 font-mono text-[#b4452f]"
                  >−{{ deduction.rounds.toLocaleString() }}</span
                >
              </div>
            </div>

            <!-- Firearm counts -->
            <div v-if="firearmCounts.length" class="px-4 py-3 border-b border-[#f1f2f3]">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">
                FIREARM COUNTS
              </div>
              <div
                v-for="count in firearmCounts"
                :key="count.entity.id"
                data-testid="firearm-count"
                class="flex items-center justify-between gap-3 border-b border-[#f1f2f3] py-1.5 text-[14px] last:border-b-0"
              >
                <span class="min-w-0 truncate text-[#3a3e44]">{{ count.entity.label ?? '—' }}</span>
                <span class="shrink-0 font-mono text-[#2f7d57]"
                  >+{{ count.rounds.toLocaleString() }}</span
                >
              </div>
            </div>

            <!-- Suppressor counts -->
            <div v-if="linesWithSuppressorCount.length" class="px-4 py-3">
              <div class="font-mono text-[10px] text-muted tracking-[0.06em] mb-2">
                SUPPRESSOR COUNTS
              </div>
              <div
                v-for="line in linesWithSuppressorCount"
                :key="line.id"
                class="flex items-center justify-between gap-3 border-b border-[#f1f2f3] py-1.5 text-[14px] last:border-b-0"
              >
                <span class="inline-flex min-w-0 items-center gap-[7px] text-[#3a3e44]">
                  <span
                    v-if="line.suppressor?.is_nfa"
                    class="rounded-sm bg-[#1a1c1f] px-1 font-mono text-[9px] text-white"
                    >NFA</span
                  >
                  <span class="truncate">{{ line.suppressor?.label ?? '—' }}</span>
                </span>
                <span class="shrink-0 font-mono text-[#2f7d57]">+{{ line.rounds }}</span>
              </div>
            </div>

            <div
              v-if="
                !ammunitionDeductions.length &&
                !firearmCounts.length &&
                !linesWithSuppressorCount.length
              "
              class="px-4 py-4 text-[13px] text-muted"
            >
              No effects applied.
            </div>
          </div>

          <!-- Session summary -->
          <div
            v-if="session.description"
            class="overflow-hidden rounded border border-line bg-white"
          >
            <div class="border-b border-[#eef0f1] px-4 py-3 font-display text-[16px] font-semibold">
              Session summary
            </div>
            <div
              class="whitespace-pre-wrap px-4 py-[13px] text-[14px] leading-[1.55] text-[#3a3e44]"
            >
              {{ session.description }}
            </div>
          </div>

          <NotesPanel entity-type="training" :entity-id="trainingId" />
        </div>

        <!-- Right -->
        <div class="flex flex-col gap-4">
          <!-- Shooting lines -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div class="flex items-center gap-3 px-[18px] py-4 border-b border-[#eef0f1]">
              <span class="font-display font-semibold text-[18px]">Shooting lines</span>
              <span class="font-mono text-[11px] text-muted tracking-[0.04em]"
                >{{ session.lines.length }} LINE{{ session.lines.length !== 1 ? 'S' : '' }}</span
              >
              <button
                class="ml-auto inline-flex items-center gap-1 text-[13px] font-medium text-brass hover:text-[#b08a2e] transition-colors"
                @click="addingLine = true"
              >
                <Plus class="h-[14px] w-[14px]" />
                Add line
              </button>
            </div>

            <div
              v-if="!session.lines.length"
              class="px-[18px] py-8 text-center text-muted text-[14px]"
            >
              No shooting lines logged.
            </div>

            <div
              v-for="line in session.lines"
              :key="line.id"
              class="px-[18px] py-4 border-b border-[#eef0f1] last:border-b-0"
            >
              <div class="mb-3 flex flex-wrap items-center gap-3">
                <div
                  class="flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded border border-line bg-[#f5f6f7] text-[#6b7077]"
                >
                  <Home class="h-[19px] w-[19px]" />
                </div>
                <div class="min-w-0 flex-1">
                  <router-link
                    v-if="line.firearm?.id"
                    :to="{ name: 'FirearmsShow', params: { firearm_id: line.firearm.id } }"
                    class="font-display text-[16px] font-semibold leading-tight text-ink-900 transition-colors hover:text-[#7d6320]"
                  >
                    {{ line.firearm?.label ?? '—' }}
                  </router-link>
                  <div v-else class="font-display text-[16px] font-semibold leading-tight">—</div>
                  <div class="text-[13px] text-[#6b7077]">{{ firearmSubtitle(line.firearm) }}</div>
                </div>
                <span
                  v-if="line.suppressor"
                  class="inline-flex items-center gap-[5px] rounded border border-[#c3b6d6] bg-[#eee9f3] px-[7px] py-0.5 font-mono text-[10px] text-[#6b5a8c]"
                >
                  SUPPRESSED · {{ line.suppressor.label }}
                </span>
                <div class="shrink-0 text-right">
                  <div class="font-mono text-[22px] font-medium leading-none">
                    {{ line.rounds.toLocaleString() }}
                  </div>
                  <div class="font-mono text-[9px] tracking-[0.05em] text-muted">ROUNDS</div>
                </div>
                <button
                  class="inline-flex h-7 items-center gap-1 rounded px-2 text-[12px] font-medium text-muted transition-colors hover:bg-ink-50 hover:text-ink-900"
                  :aria-expanded="expandedLineNotes.has(line.id)"
                  @click="toggleLineNotes(line.id)"
                >
                  <MessageSquareText class="h-[14px] w-[14px]" />
                  Notes
                  <ChevronDown
                    class="h-3.5 w-3.5 transition-transform"
                    :class="expandedLineNotes.has(line.id) ? 'rotate-180' : ''"
                  />
                </button>
                <button
                  class="flex h-7 w-7 items-center justify-center rounded text-muted transition-colors hover:bg-ink-50 hover:text-ink-900"
                  title="Edit line"
                  @click="editingLine = line"
                >
                  <Pencil class="h-[15px] w-[15px]" />
                </button>
              </div>

              <div
                class="flex items-center gap-2 rounded border border-[#eef0f1] bg-[#fafbfb] px-[11px] py-2 text-[14px] text-[#3a3e44]"
              >
                <Package class="h-[15px] w-[15px] shrink-0 text-[#7d6320]" />
                <router-link
                  v-if="line.ammunition?.id"
                  :to="{ name: 'AmmoShow', params: { ammunition_id: line.ammunition.id } }"
                  class="min-w-0 flex-1 truncate text-ink-900 transition-colors hover:text-[#7d6320]"
                >
                  {{ ammunitionLabel(line.ammunition) }}
                </router-link>
                <span v-else class="min-w-0 flex-1 truncate">—</span>
                <span
                  v-if="line.deduct_ammo"
                  class="shrink-0 whitespace-nowrap font-mono text-[13px] text-[#6b7077]"
                >
                  −{{ line.rounds.toLocaleString() }} rds
                  <template v-if="line.estimated_cost">
                    · ≈{{ formatCurrency(line.estimated_cost) }}
                  </template>
                </span>
              </div>

              <NotesPanel
                v-if="expandedLineNotes.has(line.id)"
                class="mt-3"
                entity-type="session-lines"
                :entity-id="line.id"
                compact
              />
            </div>
          </div>

          <!-- Targets -->
          <div class="overflow-hidden rounded border border-line bg-white">
            <div
              class="flex items-center justify-between gap-3 border-b border-[#eef0f1] px-[18px] py-4"
            >
              <div class="flex items-center gap-3">
                <span class="font-display text-[18px] font-semibold">Targets</span>
                <span
                  v-if="session.target_count"
                  class="font-mono text-[11px] tracking-[0.04em] text-muted"
                >
                  {{ session.target_count }} TARGET{{ session.target_count !== 1 ? 'S' : '' }}
                </span>
              </div>
              <button
                class="inline-flex items-center gap-1 text-[13px] font-semibold text-brass-800 transition-colors hover:text-brass-900"
                @click="addingTarget = true"
              >
                <Plus class="h-[14px] w-[14px]" />
                Add target
              </button>
            </div>

            <div
              v-if="!session.targets?.length"
              class="px-[18px] py-10 text-center text-[14px] text-muted"
            >
              No targets logged for this session.
            </div>

            <div v-else class="grid grid-cols-1 gap-3 p-[18px] sm:grid-cols-2 xl:grid-cols-3">
              <div
                v-for="target in session.targets"
                :key="target.id"
                class="group relative h-[150px] overflow-hidden rounded border border-line bg-ink-50"
              >
                <img
                  v-if="target.medium_url"
                  :src="target.medium_url"
                  :alt="target.label || `Target at ${target.distance} yds`"
                  class="h-full w-full object-cover"
                />
                <div
                  v-else
                  class="flex h-full items-center justify-center font-mono text-[11px] tracking-[0.04em] text-muted"
                >
                  Target photo
                </div>
                <!-- Overlay -->
                <div
                  class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[rgba(20,22,26,0.72)] to-transparent px-2.5 pb-2 pt-6"
                >
                  <div class="text-white">
                    <div class="font-mono text-[11px] tracking-[0.04em] opacity-90">
                      {{ target.distance }} yds · {{ target.group_size }}"
                    </div>
                    <div v-if="target.label" class="mt-0.5 text-[12px] font-medium leading-tight">
                      {{ target.label }}
                    </div>
                  </div>
                </div>
                <!-- Delete -->
                <button
                  class="absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded bg-[rgba(20,22,26,0.6)] text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[rgba(192,57,43,0.85)]"
                  :disabled="deletingTargetId === target.id"
                  @click="deleteTarget(target.id)"
                >
                  <Trash2 class="h-[13px] w-[13px]" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <AddSessionLineModal
      v-if="addingLine"
      :training-id="trainingId"
      @close="addingLine = false"
      @created="onLineCreated"
    />

    <EditSessionLineModal
      v-if="editingLine"
      :line="editingLine"
      :training-id="trainingId"
      @close="editingLine = null"
      @updated="onLineUpdated"
      @deleted="onLineDeleted"
    />

    <AddTargetModal
      v-if="addingTarget"
      :training-id="trainingId"
      @close="addingTarget = false"
      @created="onTargetCreated"
    />
  </div>
</template>
