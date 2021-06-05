import Vue from 'vue';

import modal from 'vue-modal';
import alert from "../components/alert";
import activeFlag from "../components/activeFlag";
import sortButton from "../components/sortButton";
import svg from "./../components/svg/svg";


Vue.component('modal', modal);

Vue.component('pagination', (resolve) => {
    require.ensure([], () => {
        resolve(require('vue-pagination'));
    }, '/components/pagination');
});

Vue.component('numeral-input', (resolve) => {
    require.ensure(['vue-numeral-input'], () => {
        resolve(require('vue-numeral-input'));
    }, '/components/numeralcomponents');
});

Vue.component('currency-input', (resolve) => {
    require.ensure(['vue-numeral-input'], () => {
        resolve(require('vue-numeral-input'));
    }, '/components/numeralcomponents');
});

Vue.component('alert', alert);
Vue.component('active-flag', activeFlag);
Vue.component('sort-btn', sortButton);

Vue.component('inline-svg', svg);
Vue.component('icon', () => import(/* webpackChunkName: "js/svg/icon" */'./../components/svg/icon'));
