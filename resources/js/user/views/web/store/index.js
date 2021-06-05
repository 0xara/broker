
import Http from "vue-http";

export default {
    state: {
        isLogin: null,
        user: null,
        isMiddlewareLoading: false,
        sitekey: ''
    },
    mutations: {
        setIsLogin(state, condition) {
            state.isLogin = condition;
        },
        setUser(state, user) {
            state.user = user;
        },
        setMiddlewareLoading(state, condition) {
            state.isMiddlewareLoading = condition;
        },
        setGrcSiteKey(state, siteKey) {
            state.sitekey = siteKey;
        }
    },
    actions: {
        registerUser(context, data) {
            return this._vm.$http.post('/register',data).then((data) => {
                setCsrfToken();
                return data;
            });
        },
        loginUser(context, data) {
            return this._vm.$http.post('/login',data).then((data) => {
                setCsrfToken();
                return data;
            });
        },
        logoutUser(context) {
            return this._vm.$http.post('/logout').then(() => {
                context.commit('setIsLogin', false);
                context.commit('setUser', null);
            });
        },
        passwordResetUser(context, data) {
            return this._vm.$http.post('/password/reset',data);
        },
        getUser(context) {
            return this._vm.$http.get('/api/v1/user',{}).then(({ data }) => {
                context.commit('setIsLogin', true);
                context.commit('setUser', data);
                return data;
            }).catch((e) => {
                context.commit('setIsLogin', false);
                context.commit('setUser', null);
                throw e;
            });
        },
        getCsrf() {
            return this._vm.$http.get('/sanctum/csrf-cookie').then(() => {
                setCsrfToken();
            });
        },
        setGrcSiteKey(context, siteKey) {
            context.commit('setGrcSiteKey', siteKey);
        }
    },
    modules: {
    }
}

function setCsrfToken() {
    // Check for CSRF token
    let csrf = RegExp('XSRF-TOKEN[^;]+').exec(document.cookie);
    csrf = decodeURIComponent(csrf ? csrf.toString().replace(/^[^=]+./, '') : '');

    if(csrf) {
        Http.defaults.headers = {
            ...(Http.defaults.headers || {}),
            'X-XSRF-TOKEN': csrf,
        };
    }
}
