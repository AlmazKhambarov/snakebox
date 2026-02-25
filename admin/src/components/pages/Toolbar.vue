<template>
  <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
      <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ title }}</h1>
      </div>
      <slot />

<!--      <div class="d-flex align-items-center gap-2 gap-lg-3">-->
<!--        &lt;!&ndash;begin::Secondary button&ndash;&gt;-->
<!--        <a href="#" class="btn btn-sm fw-bold btn-secondary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_app">Rollover</a>-->
<!--        &lt;!&ndash;end::Secondary button&ndash;&gt;-->
<!--        &lt;!&ndash;begin::Primary button&ndash;&gt;-->
<!--        <a href="#" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">Add Target</a>-->
<!--        &lt;!&ndash;end::Primary button&ndash;&gt;-->
<!--      </div>-->
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  title: String
})
</script>