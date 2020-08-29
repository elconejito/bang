// Import Core FontAwesome code
import { library } from '@fortawesome/fontawesome-svg-core';
// Import icons from Solid FA library
import {
  faAngleLeft,
  faAngleRight,
  faBars,
  faBatteryEmpty,
  faCircleNotch,
  faEdit,
  faExclamationTriangle,
  faExpand,
  faExternalLinkAlt,
  faHome,
  faPlusCircle,
  faSave,
  faSlidersH,
  faSpinner,
  faSort,
  faTimes,
  faTrash,
  faWindowClose,
} from '@fortawesome/free-solid-svg-icons';

// Import icons from Solid FA library
import { faGithubSquare } from '@fortawesome/free-brands-svg-icons';

// Import the FA Vue Component
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default ({ Vue }) => {
  // Add all the icons used. We are being explicit so as not to bloat the final bundle
  library.add(
    faAngleLeft,
    faAngleRight,
    faBars,
    faBatteryEmpty,
    faCircleNotch,
    faEdit,
    faExclamationTriangle,
    faExpand,
    faExternalLinkAlt,
    faGithubSquare,
    faHome,
    faPlusCircle,
    faSave,
    faSlidersH,
    faSpinner,
    faSort,
    faTimes,
    faTrash,
    faWindowClose
  );
  // Add the Font-Awesome Vue Component
  Vue.component('font-awesome-icon', FontAwesomeIcon);
};
