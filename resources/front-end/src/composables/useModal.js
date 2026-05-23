import { Modal } from 'bootstrap';

export function useModal() {
  function openModal(id) {
    const el = document.getElementById(id);
    Modal.getOrCreateInstance(el).show();
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    Modal.getInstance(el)?.hide();
  }

  return { openModal, closeModal };
}
