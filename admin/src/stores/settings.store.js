import { defineStore } from "pinia";

export const useSettingsStore = defineStore('settingsStore', {
    state: () => ({
        loading: false
    }),
    getters: {
        isLoading: (state) => state.loading
    },
    actions: {
        async startLoading() {
            this.loading = true;
        },
        async stopLoading() {
            this.loading = false;
        },
    }
});