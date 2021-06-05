
export default {
    state: {
        loadingModalShow: false,
        loginModalShow: false,
        formCsrfFieldToken: ''
    },
    mutations: {
        handleLoadingModalCondition(state, condition) {
            state.loadingModalShow = condition;
        },
        handleLoginModalCondition(state, condition) {
            state.loginModalShow = condition;
        },
        handleSetFormCsrfFieldToken(state, csrf) {
            state.formCsrfFieldToken = csrf;
        }
    },

    actions: {
        handleLoadingModalCondition(context, condition) {
            context.commit('handleLoadingModalCondition', condition);
        },
        handleLoginModalCondition(context, condition) {
            context.commit('handleLoginModalCondition', condition);
        },
        handleSetFormCsrfFieldToken(context, csrf) {
            context.commit('handleSetFormCsrfFieldToken', csrf);
        }
    },
}
