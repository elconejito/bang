import { ref } from 'vue';
import { defineStore } from 'pinia';
import { axiosInstance } from '@/plugins/axios';

const urlMap = {
  ammunitionCasing: 'ammunition-casing',
  ammunitionCondition: 'ammunition-condition',
  bulletType: 'bullet-type',
  caliberType: 'caliber-type',
  locationType: 'location-type',
  primerType: 'primer-type',
  purpose: 'purpose',
  shellLength: 'shell-length',
  shellType: 'shell-type',
  shotMaterial: 'shot-material',
};

export const useReferenceStore = defineStore('reference', () => {
  const ammunitionCasing = ref([]);
  const ammunitionCondition = ref([]);
  const bulletType = ref([]);
  const caliberType = ref([]);
  const locationType = ref([]);
  const primerType = ref([]);
  const purpose = ref([]);
  const shellLength = ref([]);
  const shellType = ref([]);
  const shotMaterial = ref([]);

  const stateMap = {
    ammunitionCasing,
    ammunitionCondition,
    bulletType,
    caliberType,
    locationType,
    primerType,
    purpose,
    shellLength,
    shellType,
    shotMaterial,
  };

  async function fetch(model) {
    const { data } = await axiosInstance.get(`/${urlMap[model]}`);
    stateMap[model].value = data.data ?? [];
  }

  async function fetchAll() {
    await Promise.all(Object.keys(urlMap).map(fetch));
  }

  return {
    ammunitionCasing,
    ammunitionCondition,
    bulletType,
    caliberType,
    locationType,
    primerType,
    purpose,
    shellLength,
    shellType,
    shotMaterial,
    fetch,
    fetchAll,
  };
});
