
window._ = require('lodash');

import Popper from 'popper.js/dist/umd/popper.js';
import '@fortawesome/fontawesome-free/js/all';

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = require('jquery');
  window.Popper = Popper;

  require('bootstrap');
  require('dropzone');
  Dropzone.autoDiscover = false;
} catch (e) {}


/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  console.info('CSRF token found');
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
