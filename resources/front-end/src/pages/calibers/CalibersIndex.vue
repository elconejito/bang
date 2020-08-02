<template>
  <div class="container">
    <div class="row">
      <div class="col">
        CalibersIndex, toolbar
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#caliber-form">
          Add Caliber
        </button>
      </div>
    </div>
    <CaliberList :calibers="calibers" />
    <Modal modalId="caliber-form">
      <template v-slot:modalTitle>Add Caliber Form</template>
      <template v-slot:modalBody>
        <CaliberForm @complete="fetchData" />
      </template>
    </Modal>
  </div>
</template>

<script>
import CaliberList from '../../components/caliber/CaliberList';
import Modal from '../../components/Modal';
import CaliberForm from '../../components/caliber/CaliberForm';

export default {
  name: 'CalibersIndex',
  components: { CaliberForm, Modal, CaliberList },
  data() {
    return {
      calibers: [],
      meta: {},
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.$store.dispatch('calibers/all').then((response) => {
        console.log('CalibersIndex fetchData() then', response);
        const { data, meta } = response;
        this.calibers = data;
        this.meta = meta || {};
      });
    },
  },
};
</script>

<style></style>
