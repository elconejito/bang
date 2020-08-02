/* global $ */
export default {
  methods: {
    closeModal(prop) {
      $(`#${prop}`).modal('hide');
    },
    openModal(prop) {
      $(`#${prop}`).modal('show');
    },
  },
};
