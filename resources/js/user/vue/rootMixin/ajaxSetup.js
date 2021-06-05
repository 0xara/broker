
function handleAjaxSetup() {
    const self = this;

    this.$http.constructor.ajaxSend((instance) => {
        if(instance.name) {
            self.$bus.$emit(`${instance.name}-partial-loading`, true);
        }
    });

    this.$http.constructor.ajaxComplete((instance, val) => {
        if(instance.name) {
            self.$bus.$emit(`${instance.name}-partial-loading`, false);
        }
    });

    this.$http.constructor.ajaxStop(() => {
        if(self.$store.global.loadingModalShow) { self.$store.dispatch('handleLoadingModalCondition', false); }
    });

    this.$http.constructor.ajaxSuccess((instance, { data }) => {
        if(data && Object.prototype.hasOwnProperty.call(data, 'redirect')) {
            if(window.location.href == data.redirect) {
                window.location.reload();
            } else {
                window.location.replace(data.redirect);
            }
        } else if(data && Object.prototype.hasOwnProperty.call(data, 'success')) {

        }
    });

    this.$http.constructor.ajaxError((instance, e) => {
        if(e.request && e.request.status === 419) {
            window.location.reload();
        }
    });
}

export default handleAjaxSetup;
