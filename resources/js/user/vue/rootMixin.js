import handleAjaxSetup from './rootMixin/ajaxSetup'

export default {

    mounted() {
        this.$nextTick(() => {
            this.handleDrip();
            this.handleAjaxSetup();
        });
    },

    methods: {
        handleAjaxSetup,

        handleDrip() {
            setInterval(() => {
                this.$store.dispatch('getCsrf');
            }, 900000);
        }
    }

}
