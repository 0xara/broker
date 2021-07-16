import Vue from "vue";
import Vuex from 'vuex';
import global from './global'
import web from '../views/web/store';
import exchange from '../views/exchange/store';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        web,
        exchange,
        global
    }
});
