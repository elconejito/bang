<template>
  <div class="modal" tabindex="-1" role="dialog" :id="modalId">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <slot name="modalTitle"></slot>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <font-awesome-icon icon="times" />
          </button>
        </div>
        <div class="modal-body">
          <slot name="modalBody"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Modal',
  props: {
    modalId: {
      type: String,
      required: true,
    },
  },
};
</script>

<style scoped></style>
