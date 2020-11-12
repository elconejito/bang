<template>
  <div v-if="isLoading">
    <Loading />
  </div>
  <div class="container" v-else>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'dashboard' }">
            <font-awesome-icon icon="home" />
          </router-link>
        </li>
        <li class="breadcrumb-item">
          <router-link :to="{ name: 'FirearmsIndex' }">
            All Firearms
          </router-link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ firearm.label }}</li>
      </ol>
    </nav>

    <div class="row">
      <div class="col">
        <h1>
          <small>{{ firearm.manufacturer }}</small
          ><br />
          {{ firearm.model }}
          <button
            type="button"
            class="btn btn-outline-info"
            @click="openModal('edit-firearm-form')"
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

    <div class="tab-content">
      <component :is="currentTabComponent" :firearm="firearm" />
    </div>

    <Modal modalId="edit-firearm-form">
      <template v-slot:modalTitle>Edit Firearm</template>
      <template v-slot:modalBody>
        <EditFirearmForm
          :calibers="calibers"
          :original="firearm"
          @complete="completeEditFirearm"
        />
      </template>
    </Modal>
  </div>
</template>

<script>
import HasLoading from 'mixins/HasLoading';
import HasModal from 'mixins/HasModal';
import HasNavTabs from 'mixins/HasNavTabs';
import Loading from 'components/Loading';
import Modal from 'components/Modal';
import EditFirearmForm from 'components/firearms/EditFirearmForm';
import FirearmAmmunition from 'components/firearms/FirearmAmmunition';
import FirearmDetails from 'components/firearms/FirearmDetails';
import FirearmImages from 'components/firearms/FirearmImages';
import FirearmMagazines from 'components/firearms/FirearmMagazines';

export default {
  name: 'FirearmsShow',
  components: {
    EditFirearmForm,
    FirearmAmmunition,
    FirearmDetails,
    FirearmImages,
    FirearmMagazines,
    Loading,
    Modal,
  },
  mixins: [HasLoading, HasModal, HasNavTabs],
  props: {
    firearmId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      firearm: {},
      calibers: [],
    };
  },
  mounted() {
    const tabMapping = {
      details: {
        active: true,
        label: 'Details',
        component: 'FirearmDetails',
      },
      ammunition: {
        active: false,
        label: 'Ammunition',
        component: 'FirearmAmmunition',
      },
      magazines: {
        active: false,
        label: 'Magazines',
        component: 'FirearmMagazines',
      },
      images: {
        active: false,
        label: 'Images',
        component: 'FirearmImages',
      },
    };
    this.initTabs(tabMapping);
    this.fetchData();
  },
  methods: {
    completeEditFirearm() {
      this.closeModal('edit-firearm-form');
      this.fetchFirearm();
    },
    fetchData() {
      console.log('FirearmsShow fetchData()');
      this.isLoading = true;
      this.fetchCalibers();
      this.fetchFirearm();
    },
    fetchFirearm() {
      this.$set(this.loadingQueue, 'firearm', false);

      const payload = {
        firearmId: this.firearmId,
      };

      return this.$store
        .dispatch('firearms/get', payload)
        .then((response) => {
          console.log('FirearmsShow fetchData() then', response);
          const { data } = response;
          this.firearm = data;
        })
        .catch((error) => {
          console.error('FirearmsShow fetchData() catch', error);
        })
        .finally(() => {
          this.loadingQueue.firearm = true;
        });
    },
    fetchCalibers() {
      this.$set(this.loadingQueue, 'calibers', false);

      this.$store
        .dispatch('calibers/all')
        .then((response) => {
          console.log('FirearmsIndex fetchCalibers() then', response);
          const { data } = response;
          this.calibers = data;
        })
        .catch((error) => {
          // show the error message
          console.error('FirearmsIndex fetchCalibers() then', error);
          this.error = this.$errorProcessor(error);
        })
        .finally(() => {
          this.loadingQueue.calibers = true;
        });
    },
  },
};
</script>

<style></style>
