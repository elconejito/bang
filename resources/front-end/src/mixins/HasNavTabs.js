import * as _ from 'lodash';

export default {
  data() {
    return {
      tabs: {},
    };
  },
  computed: {
    currentTab() {
      return Object.keys(this.tabs).find((tab) => {
        return this.tabs[tab].active;
      });
    },
    currentTabComponent() {
      const current = this.currentTab;

      return current ? this.tabs[current].component : undefined;
    },
    tabNames() {
      return Object.keys(this.tabs).map((tab) => {
        return this.tabs[tab].label;
      });
    },
  },
  methods: {
    initTabs(mapping) {
      this.tabs = mapping;
    },
    setCurrentTab(name) {
      Object.keys(this.tabs).forEach((tab) => {
        this.tabs[tab].active = tab === name;
      });
    },
    tabNameSlug(name) {
      return _.camelCase(name);
    },
  },
};
