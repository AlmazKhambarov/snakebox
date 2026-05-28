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

        <!-- Upgrade Settings Card -->
        <div class="card mb-5 mb-xl-10">
          <div class="card-header border-0">
            <div class="card-title m-0">
              <h3 class="fw-bold m-0">⚡ Настройки Апгрейда</h3>
            </div>
            <div class="card-toolbar">
              <button @click="saveUpgradeSettings" class="btn btn-sm fw-bold btn-success">
                Сохранить апгрейд
              </button>
            </div>
          </div>
          <div class="card-body pt-0">
            <div class="alert alert-info mb-5">
              <strong>Chance Boost</strong> — базовый шанс апгрейда увеличивается на указанный %.
              Например: предмет стоит 50% от цели → базовый шанс 50%. С boost +10% → итого 60%.
              Максимум апгрейда: <strong>90%</strong>.
            </div>
            <div class="form-group row">
              <div class="col-lg-3">
                <label class="form-label required">Chance Boost (%)</label>
                <div class="input-group-sm mb-5">
                  <input
                    type="number"
                    class="form-control"
                    v-model="upgradeSettings.chance_boost"
                    min="0"
                    max="50"
                    step="0.5"
                    placeholder="0"
                  />
                </div>
                <small class="text-muted">Qo'shimcha % (0 — boost yo'q, 10 — +10%)</small>
              </div>
              <div class="col-lg-3">
                <label class="form-label required">Целевой RTP (%)</label>
                <div class="input-group-sm mb-5">
                  <input type="number" class="form-control" v-model="upgradeSettings.target_rtp"
                    min="10" max="100" step="0.1" />
                </div>
              </div>
              <div class="col-lg-3">
                <label class="form-label required">Мин. RTP (%)</label>
                <div class="input-group-sm mb-5">
                  <input type="number" class="form-control" v-model="upgradeSettings.min_rtp"
                    min="10" max="100" step="0.1" />
                </div>
              </div>
              <div class="col-lg-3">
                <label class="form-label required">Макс. RTP (%)</label>
                <div class="input-group-sm mb-5">
                  <input type="number" class="form-control" v-model="upgradeSettings.max_rtp"
                    min="10" max="100" step="0.1" />
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
      upgradeSettings: {
        chance_boost: 0,
        target_rtp: 92,
        min_rtp: 88,
        max_rtp: 96,
      },
    };
  },
  methods: {
    async getSettings() {
      await this.settingsStore.startLoading();
      request("GET", "/api/admin/settings").then(({ data }) => {
        this.settings = { ...data };
      });
    },
    async getUpgradeSettings() {
      request("GET", "/api/admin/upgrade/settings").then(({ data }) => {
        if (data.success) {
          this.upgradeSettings = { ...data.settings };
        }
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
    async saveUpgradeSettings() {
      request("POST", "/api/admin/upgrade/settings", this.upgradeSettings).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
        } else {
          toast.error(data.message || 'Xatolik yuz berdi');
        }
      });
    },
  },
  created() {
    this.settingsStore = useSettingsStore();
    this.getSettings();
    this.getUpgradeSettings();
  },
};
</script>
