<template>
  <div :id="modalId" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <slot name="modalTitle" />
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
        </div>
        <div class="modal-body">
          <slot name="modalBody" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modalId: { type: String, required: true },
})
</script>

<style scoped></style>
