<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Настройки сайта">
      <div class="d-flex align-items-center gap-2 gap-lg-3">
        <button @click="saveSettings" class="btn btn-sm fw-bold btn-primary">
          Сохранить
        </button>
      </div>
    </Toolbar>

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card mb-5 mb-xl-10">
          <div class="card-body pt-0 pb-0">
            <ul
              class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold"
            >
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5 active"
                  data-bs-toggle="tab"
                  href="#main"
                  >Основные</a
                >
              </li>
            </ul>
          </div>
        </div>

        <div class="card mb-5 mb-xl-10">
          <div class="card-body">
            <div class="tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="main" role="tabpanel">
                <h1 class="mb-3">Основные</h1>
                <div class="form-group row">
                  <div class="col-lg-4">
                    <label class="form-label">Домен сайта</label>
                    <div class="input-group-sm mb-5">
                      <input type="text" class="form-control" v-model="settings.domain" />
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <label class="form-label">Название сайта</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        v-model="settings.site_name"
                      />
                    </div>
                  </div>

                  <div class="col-lg-4">
                    <label class="form-label">Заголовок сайта (title)</label>
                    <div class="input-group-sm mb-5">
                      <input type="text" class="form-control" v-model="settings.title" />
                    </div>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-lg-4">
                    <label class="form-label">Описание для поисковых систем</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        v-model="settings.description"
                      />
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label">Ключевые слова для поисковых систем:</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        v-model="settings.keywords"
                      />
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <label class="form-label">Ключ маркета</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        v-model="settings.market_key"
                      />
                    </div>
                  </div>
                </div>

                <div class="form-group row"></div>

                <div class="form-group row">
                  <div class="col-lg-3">
                    <label class="form-label">Ссылка на Telegram</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        placeholder="https://t.me/..."
                        v-model="settings.tg_group"
                      />
                    </div>
                  </div>

                  <div class="col-lg-3">
                    <label class="form-label">Ссылка на VK</label>
                    <div class="input-group-sm mb-5">
                      <input
                        type="text"
                        class="form-control"
                        placeholder="https://vk.com/..."
                        v-model="settings.vk_group"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Toolbar from "../components/pages/Toolbar.vue";
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";
import { useSettingsStore } from "../stores/settings.store.js";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      settings: {},
    };
  },
  methods: {
    async getSettings() {
      await this.settingsStore.startLoading();
      request("GET", "/api/admin/settings").then(({ data }) => {
        this.settings = { ...data };
      });
    },
    async saveSettings() {
      request("POST", "/api/admin/settings/save", this.settings).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
        } else {
          toast.error(data.message);
        }
      });
    },
  },
  created() {
    this.settingsStore = useSettingsStore();
    this.getSettings();
  },
};
</script>
