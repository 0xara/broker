import Vue from "vue";
import Vuex from 'vuex';
import global from './global'
import web from '../views/web/store';
import broker from '../views/broker/store';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        web,
        broker,
        global
    }
});
