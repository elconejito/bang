<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <div class="row">
      <div class="col">
        AmmunitionShow, toolbar
        <button type="button" class="btn btn-primary" @click="openModal('edit-ammunition-form')">
          Edit Ammunition
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <h1>{{ ammunition.label }}</h1>
      </div>
    </div>

    <Modal modalId="edit-ammunition-form">
      <template v-slot:modalTitle>Edit Ammunition Form</template>
      <template v-slot:modalBody>
        <EditAmmunitionForm
          :caliber="caliber"
          :original="ammunition"
          @complete="completeEditAmmunition"
        />
      </template>
    </Modal>
  </div>
</template>

<script>
import Loading from 'components/Loading';
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';
import EditAmmunitionForm from 'components/ammunition/EditAmmunitionForm';
import Modal from 'components/Modal';

export default {
  name: 'AmmunitionShow',
  components: { Modal, EditAmmunitionForm, Loading },
  mixins: [HasLoading, HasModal],
  props: {
    ammunitionId: {
      type: Number,
      required: true,
    },
    caliberId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      ammunition: {},
      caliber: {},
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    completeEditAmmunition() {
      this.closeModal('edit-ammunition-form');
      this.$set(this.loadingQueue, 'ammunition', false);
      this.fetchAmmunition();
    },
    fetchData() {
      this.isLoading = true;
      this.$set(this.loadingQueue, 'caliber', false);
      this.$set(this.loadingQueue, 'ammunition', false);

      this.fetchCaliber();
      this.fetchAmmunition();
    },
    fetchCaliber() {
      console.log('AmmunitionShow fetchCaliber()');
      const payload = {
        caliberId: this.caliberId,
      };

      return this.$store
        .dispatch('calibers/get', payload)
        .then((response) => {
          console.log('AmmunitionShow fetchCaliber() then', response);
          const { data } = response;
          this.caliber = data;
        })
        .catch((error) => {
          console.error('AmmunitionShow fetchCaliber() catch', error);
        })
        .finally(() => {
          this.loadingQueue.caliber = true;
        });
    },
    fetchAmmunition() {
      console.log('AmmunitionShow fetchAmmunition()');
      const payload = {
        ammunitionId: this.ammunitionId,
        caliberId: this.caliberId,
      };

      return this.$store
        .dispatch('ammunition/get', payload)
        .then((response) => {
          console.log('AmmunitionShow fetchAmmunition() then', response);
          const { data } = response;
          this.ammunition = data;
        })
        .catch((error) => {
          console.error('AmmunitionShow fetchAmmunition() catch', error);
        })
        .finally(() => {
          this.loadingQueue.ammunition = true;
        });
    },
  },
};
</script>

<style scoped></style>
