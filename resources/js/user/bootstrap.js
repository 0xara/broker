
import 'core-js/es6/weak-map';
import 'core-js/es6/promise';
import 'core-js/es6/object';
import 'core-js/es6/array';
/* requestAnimationFrame polyfill */
import 'raf/polyfill';
import 'classlist-polyfill';
import 'intersection-observer';
import 'custom-event-polyfill';

import Vue from 'vue';
import Vuelidate from 'vuelidate';
import PortalVue from 'portal-vue';
import VueForceNextTick from 'vue-force-next-tick';
import SweetAlertIcons from 'vue-sweetalert-icons';


Vue.use(Vuelidate);
Vue.use(PortalVue);
Vue.use(VueScrollTo);
Vue.use(VueForceNextTick);
Vue.use(SweetAlertIcons);
Vue.prototype.$bus = new Vue();
