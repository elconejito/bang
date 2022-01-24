<template>
  <div v-if="isLoading">
    <Loading />
  </div>

  <div class="container" v-else>
    <nav class="has-breadcrumbs" aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersIndex' }">
            All Calibers
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'CalibersShow', params: { caliber_id: caliber.id } }">
            {{ caliber.label }}
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Ammunition</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>{{ ammunition.manufacturer }}</small><br />
          {{ ammunition.label }}
          <button
            type="button"
            class="btn btn-outline-info"
            @click="openModal('edit-ammunition-form')"
          >
            <font-awesome-icon icon="edit" />
          </button>
        </h1>
      </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item" v-for="(tab, i) in tabNames" :key="i">
        <span
          class="btn btn-link nav-link"
          :class="{ active: tabNameSlug(tab) === currentTab }"
          @click="setCurrentTab(tabNameSlug(tab))"
        >
          {{ tab }}
        </span>
      </li>
    </ul>

    <div class="tab-content py-3">
      <component :is="currentTabComponent" :ammunition="ammunition" :caliber="caliber" />
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
import HasNavTabs from 'mixins/HasNavTabs';
import AmmunitionDetails from 'components/ammunition/AmmunitionDetails';
import AmmunitionInventory from 'components/ammunition/AmmunitionInventory';
import AmmunitionTraining from 'components/ammunition/AmmunitionTraining';
import AmmunitionFirearms from 'components/ammunition/AmmunitionFirearms';
import AmmunitionImages from 'components/ammunition/AmmunitionImages';

export default {
  name: 'AmmunitionShow',
  components: {
    AmmunitionDetails,
    AmmunitionFirearms,
    AmmunitionImages,
    AmmunitionInventory,
    AmmunitionTraining,
    EditAmmunitionForm,
    Loading,
    Modal,
  },
  mixins: [HasLoading, HasModal, HasNavTabs],
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
    const tabMapping = {
      details: {
        active: true,
        label: 'Details',
        component: 'AmmunitionDetails',
      },
      inventory: {
        active: false,
        label: 'Inventory',
        component: 'AmmunitionInventory',
      },
      training: {
        active: false,
        label: 'Training',
        component: 'AmmunitionTraining',
      },
      firearms: {
        active: false,
        label: 'Firearms',
        component: 'AmmunitionFirearms',
      },
      images: {
        active: false,
        label: 'Images',
        component: 'AmmunitionImages',
      },
    };
    this.initTabs(tabMapping);
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

<style></style>
