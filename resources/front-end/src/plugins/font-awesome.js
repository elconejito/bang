import { library } from '@fortawesome/fontawesome-svg-core';
import {
  faAngleLeft,
  faAngleRight,
  faBars,
  faEdit,
  faExclamationCircle,
  faExpand,
  faExternalLinkAlt,
  faPlusCircle,
  faSave,
  faTimes,
  faTrash,
  faWindowClose,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default ({ Vue }) => {
  // Add all the icons used. We are being explicit so as not to bloat the final bundle
  library.add(
    faAngleLeft,
    faAngleRight,
    faBars,
    faEdit,
    faExclamationCircle,
    faExpand,
    faExternalLinkAlt,
    faPlusCircle,
    faSave,
    faTimes,
    faTrash,
    faWindowClose
  );
  // Add the Font-Awesome Vue Component
  Vue.component('font-awesome-icon', FontAwesomeIcon);
};
