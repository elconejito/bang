<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>

    <div class="row">
      <div class="col">
        CalibersShow, toolbar
        <button type="button" class="btn btn-primary" @click="openModal('edit-caliber-form')">
          Edit Caliber
        </button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create-ammunition-form">
          Add Ammunition
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col">
        <h1>{{ caliber.label }}</h1>
        <p class="text-muted">{{ caliberTypeLabel }}</p>
      </div>
    </div>

    <AmmunitionList :ammunition="ammunition.data" />

    <Modal modalId="edit-caliber-form">
      <template v-slot:modalTitle>Edit Caliber Form</template>
      <template v-slot:modalBody>
        <EditCaliberForm :original="caliber" @complete="completeEditCaliber" />
      </template>
    </Modal>

    <Modal modalId="create-ammunition-form">
      <template v-slot:modalTitle>Add Ammunition Form</template>
      <template v-slot:modalBody>
        <AmmunitionForm :caliber="caliber" @complete="completeAddAmmunition" />
      </template>
    </Modal>

  </div>
</template>

<script>
import AmmunitionForm from '../../components/ammunition/AmmunitionForm';
import AmmunitionList from '../../components/ammunition/AmmunitionList';
import EditCaliberForm from '../../components/caliber/EditCaliberForm';
import Loading from '../../components/Loading';
import Modal from '../../components/Modal';
import HasLoading from '../../mixins/HasLoading';
import HasModal from 'mixins/HasModal';

export default {
  name: 'CalibersShow',
  components: { Loading, AmmunitionForm, AmmunitionList, EditCaliberForm, Modal },
  mixins: [HasLoading, HasModal],
  props: {
    caliberId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      ammunition: {
        data: [],
        meta: {},
      },
      caliber: {},
    };
  },
  computed: {
    caliberTypeLabel() {
      return this.caliber.caliber_type ? this.caliber.caliber_type.label : '';
    },
  },
  mounted() {
    console.log('CalibersShow mounted()');
    this.fetchData();
  },
  methods: {
    completeAddAmmunition() {
      this.closeModal('create-ammunition-form');
      this.$set(this.loadingQueue, 'ammunition', false);
      this.fetchAmmunition();
    },
    completeEditCaliber() {
      this.closeModal('edit-caliber-form');
      this.$set(this.loadingQueue, 'caliber', false);
      this.fetchCaliber();
    },
    fetchData() {
      console.log('CalibersShow fetchData()1', this.loadingQueue);
      this.isLoading = true;
      this.$set(this.loadingQueue, 'caliber', false);
      this.$set(this.loadingQueue, 'ammunition', false);
      this.fetchCaliber();
      this.fetchAmmunition();
      console.log('CalibersShow fetchData()2', this.loadingQueue);
    },
    fetchCaliber() {
      console.log('CalibersShow fetchCaliber()');
      const payload = {
        caliberId: this.caliberId,
      };

      return this.$store.dispatch('calibers/get', payload).then((response) => {
        console.log('CalibersShow fetchData() calibers then', response);
        const { data, meta } = response;
        this.caliber = data;

        this.loadingQueue.caliber = true;
        // this.$set(this.loadingQueue, 'caliber', true);
      });
    },
    fetchAmmunition() {
      console.log('CalibersShow fetchAmmunition()');
      const payload = {
        caliberId: this.caliberId,
      };

      return this.$store.dispatch('ammunition/all', payload).then((response) => {
        console.log('CalibersShow fetchData() ammunition then', response);
        const { data, meta } = response;
        this.ammunition.data = data;
        this.ammunition.meta = meta || {};

        this.loadingQueue.ammunition = true;
        // this.$set(this.loadingQueue, 'ammunition', true);
      });
    },
  },
};
</script>

<style scoped></style>
