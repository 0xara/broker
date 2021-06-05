import Vue from 'vue';


Vue.mixin({
    methods:{
        persianNumber(value) {
            return enToFaNumber(value);
        },
        numberToCurrency,
        csrfTkn() {
            return this.$http.getSettings().headers['X-CSRF-Token'];
        }
    }
});
