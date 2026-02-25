import {defineStore} from "pinia";

export const useSettingsStore = defineStore('settingsStore', {
    state: () => ({
        loading: true
    }),
    getters: {
        isLoading: (state) => state.loading
    },
    actions: {
        async startLoading() {
            $('.preloader').show();
        },
        async stopLoading() {
            $('.preloader').fadeOut();
            // this.loading = false
        },
    }
});