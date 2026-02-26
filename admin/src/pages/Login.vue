<template>
  <div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
      <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
          <div class="w-lg-500px p-10">
            <form @submit.prevent="handleLogin" class="form w-100">
              <div class="text-center mb-11">
                <h1 class="text-gray-900 fw-bolder mb-3">SNAKEBOX</h1>
                <div class="text-gray-500 fw-semibold fs-6">Панель администратора</div>
              </div>

              <div v-if="error" class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-duotone ki-shield-tick fs-2hx text-danger me-4">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
                <div class="d-flex flex-column">
                  <span>{{ error }}</span>
                </div>
              </div>

              <div class="fv-row mb-8">
                <input
                  v-model="username"
                  type="text"
                  placeholder="Логин"
                  class="form-control bg-transparent"
                  autocomplete="username"
                  required
                />
              </div>

              <div class="fv-row mb-8">
                <input
                  v-model="password"
                  type="password"
                  placeholder="Пароль"
                  class="form-control bg-transparent"
                  autocomplete="current-password"
                  required
                />
              </div>

              <div class="d-grid mb-10">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="loading"
                >
                  <span v-if="!loading" class="indicator-label">Войти</span>
                  <span v-else class="indicator-progress d-block">
                    Подождите...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                  </span>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.store.js';

const router = useRouter();
const authStore = useAuthStore();

const username = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

async function handleLogin() {
  error.value = '';
  loading.value = true;

  try {
    const result = await authStore.login(username.value, password.value);
    if (result.success) {
      router.push({ name: 'main' });
    } else {
      error.value = result.message || 'Ошибка авторизации';
    }
  } catch (e) {
    error.value = e?.message || 'Ошибка соединения с сервером';
  } finally {
    loading.value = false;
  }
}
</script>
