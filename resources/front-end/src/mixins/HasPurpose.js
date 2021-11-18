export default {
  computed: {
    purposes() {
      return this.$store.getters['reference/purpose'];
    },
  },
  methods: {
    getPurposeLabel(id) {
      const purpose = this.purposes.find((p) => p.id === Number(id)) ?? { label: 'unknown' };

      return purpose.label;
    },
  },
};
