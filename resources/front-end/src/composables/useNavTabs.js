import { ref, computed } from 'vue';
import { camelCase } from 'lodash';

export function useNavTabs() {
  const tabs = ref({});

  const currentTab = computed(() =>
    Object.keys(tabs.value).find((tab) => tabs.value[tab].active)
  );

  const currentTabComponent = computed(() => {
    const current = currentTab.value;
    return current ? tabs.value[current].component : undefined;
  });

  const tabNames = computed(() =>
    Object.keys(tabs.value).map((tab) => tabs.value[tab].label)
  );

  function initTabs(mapping) {
    tabs.value = mapping;
  }

  function setCurrentTab(name) {
    Object.keys(tabs.value).forEach((tab) => {
      tabs.value[tab].active = tab === name;
    });
  }

  function tabNameSlug(name) {
    return camelCase(name);
  }

  return { tabs, currentTab, currentTabComponent, tabNames, initTabs, setCurrentTab, tabNameSlug };
}
