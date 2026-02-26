<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Смена пароля" />

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card mb-5 mb-xl-10">
          <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
              <h3 class="fw-bold m-0">Изменить пароль</h3>
            </div>
          </div>

          <div class="card-body border-top p-9">
            <form @submit.prevent="handleSubmit" class="form">
              <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Текущий пароль</label>
                <div class="col-lg-8 fv-row">
                  <input
                    type="password"
                    v-model="form.current_password"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Текущий пароль"
                    required
                  />
                </div>
              </div>

              <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Новый пароль</label>
                <div class="col-lg-8 fv-row">
                  <input
                    type="password"
                    v-model="form.new_password"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Новый пароль"
                    required
                  />
                </div>
              </div>

              <div class="row mb-6">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Подтверждение пароля</label>
                <div class="col-lg-8 fv-row">
                  <input
                    type="password"
                    v-model="form.new_password_confirmation"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Подтверждение пароля"
                    required
                  />
                </div>
              </div>

              <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="!loading">Сохранить</span>
                  <span v-else>Сохранение...</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Toolbar from "../components/pages/Toolbar.vue";
import { ref } from "vue";
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";

const loading = ref(false);
const form = ref({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

const handleSubmit = async () => {
  if (form.value.new_password !== form.value.new_password_confirmation) {
    return toast.error("Пароли не совпадают");
  }

  loading.value = true;
  try {
    const { data } = await request("POST", "/api/admin/password/change", form.value);
    if (data.success) {
      toast.success(data.message);
      form.value = {
        current_password: "",
        new_password: "",
        new_password_confirmation: "",
      };
    } else {
      toast.error(data.message);
    }
  } catch (e) {
    const message = e?.data?.message || e?.message || "Ошибка при смене пароля";
    toast.error(message);
  } finally {
    loading.value = false;
  }
};
</script>
