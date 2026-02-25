<template></template>
<script>
import { useAuthStore } from "@/stores/auth.store.js";
import { mapActions } from "pinia";

export default {
    methods: {
        ...mapActions(useAuthStore, ["setToken"]),
        isSanctumTokenValid(token) {
            if (!token) {
                return false;
            }

            const parts = token.split("|");

            if (parts.length !== 2) {
                return false;
            }
        },
    },
    mounted() {
        const params = new URLSearchParams(window.location.search);
        const token = params.get("token");

        // if(!this.isSanctumTokenValid(token)) {
        //     return this.$router.push('/');
        // }

        this.setToken(token);
        this.$router.push("/");
    },
};
</script>
