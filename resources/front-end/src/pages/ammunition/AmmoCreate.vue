<template>
  <div class="mx-auto max-w-[640px] px-8 py-6 pb-16">
    <AppBreadcrumb
      :crumbs="[{ label: 'Home', to: '/' }, { label: 'Ammo', to: { name: 'AmmoIndex' } }, { label: 'Add load' }]"
      class="mb-4"
    />

    <PageHeader title="Add load" class="mb-6" />

    <div class="rounded border border-line bg-white p-6">
      <AmmoFormCard
        :preselected-caliber-id="preselectedCaliberId"
        @complete="onComplete"
        @cancel="router.push({ name: 'AmmoIndex' })"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import AppBreadcrumb from '@/components/AppBreadcrumb.vue'
import PageHeader from '@/components/PageHeader.vue'
import AmmoFormCard from '@/components/ammunition/AmmoFormCard.vue'

const router = useRouter()
const route = useRoute()

const preselectedCaliberId = computed(() => {
  const id = parseInt(route.query.caliber_id)
  return isNaN(id) ? null : id
})

function onComplete(created) {
  router.push({ name: 'AmmoShow', params: { ammunition_id: created.id } })
}
</script>
