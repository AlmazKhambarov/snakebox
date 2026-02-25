<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Промокоды">
      <button
        type="button"
        class="btn btn-sm fw-bold btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#add_promo"
      >
        Добавить промокод
      </button>
      <button
        type="button"
        class="btn btn-sm fw-bold btn-primary"
        hidden=""
        data-bs-toggle="modal"
        data-bs-target="#edit_category"
      >
        Редактировать категорию
      </button>
    </Toolbar>

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush">
          <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
              <!--begin::Search-->
              <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon fs-1 position-absolute ms-4">
                  <i class="ki-duotone ki-filter-search fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                  </i>
                </span>
                <input
                  type="text"
                  data-kt-filter="search"
                  data-search-input
                  class="form-control form-control-solid w-250px ps-14"
                  placeholder="Поиск"
                />
              </div>
              <!--end::Search-->
            </div>
          </div>
          <div class="card-body">
            <table
              class="table align-middle rounded table-row-dashed fs-6 g-5"
              id="promocodes"
            >
              <thead class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                <!--begin::Table row-->
                <tr>
                  <th>ID</th>
                  <th>Код</th>
                  <th>Тип</th>
                  <th>Значение</th>
                  <th>Бесплатный скин</th>
                  <th>Бесплатный кейс</th>
                  <th>Кол-во</th>
                  <th>Макс. кол-во</th>
                  <th>Дата начала</th>
                  <th>Дата окончания</th>
                  <th>Активный</th>
                  <th>Действия</th>
                </tr>
                <!--end::Table row-->
              </thead>
              <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" tabindex="-1" id="add_promo">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Добавить промокод</h3>
          <div
            class="btn btn-icon btn-sm btn-active-light-primary ms-2"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <i class="ki-duotone ki-cross fs-1"
              ><span class="path1"></span><span class="path2"></span
            ></i>
          </div>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Код</label>
              <input v-model="newPromo.code" type="text" class="form-control" />
            </div>
            <div class="col-lg-6">
              <label class="form-label">Тип</label>
              <select class="form-select" v-model="newPromo.type">
                <option value="deposit_percent">Процент к пополнению</option>
                <option value="balance_bonus">Бонус на балансу</option>
                <option value="free_skin">Бесплатный скин</option>
                <option value="free_case">Бесплатный кейс</option>
              </select>
            </div>
          </div>

          <div class="form-group row mt-3">
            <div v-if="newPromo.type === 'deposit_percent'" class="col-lg-6">
              <label class="form-label">Процент промокода</label>
              <input v-model="newPromo.value" type="number" class="form-control" />
            </div>
            <div v-if="newPromo.type === 'balance_bonus'" class="col-lg-6">
              <label class="form-label">Бонус на баланс</label>
              <input v-model="newPromo.value" type="number" class="form-control" />
            </div>
            <div v-if="newPromo.type === 'free_skin'" class="col-lg-6">
              <label class="form-label">Бесплатный скин (id)</label>
              <input v-model="newPromo.skin_id" type="number" class="form-control" />
            </div>
            <div v-if="newPromo.type === 'free_case'" class="col-lg-6">
              <label class="form-label">Бесплатный кейс (id)</label>
              <input v-model="newPromo.case_id" type="number" class="form-control" />
            </div>
            <div class="col-lg-6">
              <label class="form-label">Статус</label>
              <select class="form-select" v-model="newPromo.is_active">
                <option value="1">Активен</option>
                <option value="0">Не активен</option>
              </select>
            </div>
          </div>
          <div class="form-group row mt-3">
            <div class="col-lg-6">
              <label class="form-label">Кол-во</label>
              <input type="number" v-model="newPromo.uses_left" class="form-control" />
            </div>
            <div class="col-lg-6">
              <label class="form-label">Макс. Кол-во</label>
              <input type="number" v-model="newPromo.max_uses" class="form-control" />
            </div>
          </div>
          <div class="form-group row mt-3">
            <div class="col-lg-6">
              <label class="form-label">Дата начала</label>
              <input type="date" v-model="newPromo.valid_from" class="form-control" />
            </div>
            <div class="col-lg-6">
              <label class="form-label">Дата окончания</label>
              <input type="date" v-model="newPromo.valid_until" class="form-control" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Закрыть
          </button>
          <button @click="create" type="submit" class="btn btn-primary">Добавить</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Toolbar from "../components/pages/Toolbar.vue";
import Cookies from "js-cookie";
import { request } from "../helpers/request.js";
import { toast } from "vue3-toastify";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      newPromo: {
        code: "",
        type: "deposit_percent",
        value: null,
        skin_id: null,
        case_id: null,
        uses_left: 0,
        max_uses: 0,
        valid_from: null,
        valid_until: null,
        is_active: 1,
      },
      handlersAttached: false,
    };
  },
  methods: {
    async loadData() {
      let table = $("#promocodes");
      table.DataTable().clear().destroy();

      const datatable = $(table).DataTable({
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
          url: `${import.meta.env.VITE_API_URL}/api/admin/promocodes`,
          type: "GET",
          headers: {
            Authorization: "Bearer " + Cookies.get("token"),
          },
        },
        columns: [
          { data: "id" },
          { data: "code" }, // добавил code вместо name
          {
            data: "type",
            render: function (data, type, row) {
              const types = {
                deposit_percent: "% к депозиту",
                balance_bonus: "Бонус на баланс",
                free_skin: "Бесплатный скин",
                free_case: "Бесплатный кейс",
              };
              return types[data] || data;
            },
          },
          {
            data: "value",
            render: function (data, type, row) {
              if (data === null) return "Нет";

              if (row.type === "deposit_percent") {
                return data + " %";
              } else if (row.type === "balance_bonus") {
                return data / 100 + " ₽";
              }
              return data;
            },
          },
          {
            data: "skin_id",
            render: function (data) {
              return data === null ? "Нет" : data;
            },
          },
          {
            data: "case_id",
            render: function (data) {
              return data === null ? "Нет" : data;
            },
          },
          { data: "uses_left" },
          { data: "max_uses" },
          {
            data: "valid_from",
            render: function (data) {
              return data === null ? "Нет" : new Date(data).toLocaleDateString();
            },
          },
          {
            data: "valid_until",
            render: function (data) {
              return data === null ? "Нет" : new Date(data).toLocaleDateString();
            },
          },
          {
            data: "is_active",
            render: function (data) {
              return data == 1 ? "Да" : "Нет";
            },
          },
          {
            data: null,
          },
        ],
        columnDefs: [
          {
            targets: -1,
            orderable: false,
            className: "actions",
            render: (data, type, row) => {
              return `
                                <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить">
                                    <i class="ki-duotone ki-trash-square fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </a>
                            `;
            },
          },
        ],
      });

      if (!this.handlersAttached) {
        datatable.on("click", 'td.actions a[data-action="delete"]', (e) => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          this.deletePromocode(id);
        });

        this.handlersAttached = true;
      }

      this.handleSearch(datatable);
    },

    handleSearch(datatable) {
      const search = document.querySelector("[data-search-input]");
      search.addEventListener("keyup", (e) => {
        datatable.search(e.target.value).draw();
      });
    },

    deletePromocode(id) {
      request("POST", "/api/admin/promocodes/delete", { id }).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
          this.loadData();
        } else {
          toast.error(data.message);
        }
      });
    },

    create() {
      request("POST", "/api/admin/promocodes/create", this.newPromo).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
          this.loadData();
          $('div[data-bs-dismiss="modal"]').click();

          // Сброс данных
          this.newPromo = {
            code: "",
            type: "deposit_percent",
            value: null,
            skin_id: null,
            case_id: null,
            uses_left: 0,
            max_uses: 0,
            valid_from: null,
            valid_until: null,
            is_active: 1,
          };
        } else {
          toast.error(data.message);
        }
      });
    },
  },
  mounted() {
    this.loadData();
  },
};
</script>
