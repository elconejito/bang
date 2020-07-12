<template>
  <div class="container">
    <div class="row">
      <div class="col">
        CalibersIndex, toolbar
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
          Add Caliber
        </button>
      </div>
    </div>
    <CaliberList :calibers="calibers" />
    <Modal />
  </div>
</template>

<script>
import CaliberList from '../../components/caliber/CaliberList';
import Modal from '../../components/Modal';

export default {
  name: 'CalibersIndex',
  components: { Modal, CaliberList },
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
