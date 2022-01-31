import { Modal } from 'bootstrap';

export default {
  methods: {
    closeModal(prop) {
      const elModal = document.getElementById(prop);
      const modal = Modal.getInstance(elModal); // Returns a Bootstrap modal instance
      modal.hide();
    },
    openModal(prop) {
      const elModal = document.getElementById(prop);
      const modal = Modal.getOrCreateInstance(elModal); // Returns a Bootstrap modal instance
      modal.show();
    },
  },
};
