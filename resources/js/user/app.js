import "./bootstrap"
import Vue from "vue";
import store from './store'
import HttpPlugin from "vue-http";
import SData from 'vue-sdata';

Vue.use(SData, { name: 'jsData', id: 'js' });

Vue.use(HttpPlugin, {driver: 'unfetch'}, () => {
    const el = document.querySelector('meta[name=_token]');
    let token;
    if(el) {
        token = el.getAttribute('content');
        el.parentElement.removeChild(el);
        store.dispatch('handleSetFormCsrfFieldToken',token);
    }

    const headers = {
        'X-Requested-With': 'XMLHttpRequest',
        //'X-CSRF-Token': token
    };

    // Check for CSRF token
    let csrf = RegExp('XSRF-TOKEN[^;]+').exec(document.cookie);
    csrf = decodeURIComponent(csrf ? csrf.toString().replace(/^[^=]+./, '') : '');

    if (csrf) {
        return {
            headers: { 'X-XSRF-TOKEN': csrf, ...headers }
        };
    }

    return {};
});

import './vue/globalMixins';
import './vue/globalComponents';
import rootMixin from  './vue/rootMixin';

import './views/web/index';
import './views/exchange/index';

const app = new Vue({

    el: '#app',

    store,

    mixins: [rootMixin]

});

store.$app = app;
