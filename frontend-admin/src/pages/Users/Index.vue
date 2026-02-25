<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Пользователи" />

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush">
          <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div
              class="card-title w-100 d-flex align-items-center justify-content-between"
            >
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
              id="users"
            >
              <thead class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                <!--begin::Table row-->
                <tr>
                  <th>ID</th>
                  <th>Изображение</th>
                  <th>Никнейм</th>
                  <th>Баланс</th>
                  <th>Роль</th>
                  <th>Сумма ставок</th>
                  <th>Заработал</th>
                  <th>Реф. баланс</th>
                  <th>Реф. уровень</th>
                  <th>Кол. во рефералов</th>
                  <th>Сумма депозитов</th>
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

  <!--  <div class="page-body">-->
  <!--    <div class="container-xl">-->
  <!--      <DataTable-->
  <!--          :columns="columns"-->
  <!--          :ajax="loadData"-->
  <!--          :data="tableData"-->
  <!--          :options="options"-->
  <!--          ref="table"-->
  <!--          class="table table-striped"-->
  <!--          style="width: 100%"-->
  <!--      >-->
  <!--        <thead>-->
  <!--        <tr>-->
  <!--          <th>ID</th>-->
  <!--          <th>Логин</th>-->
  <!--          <th>Email</th>-->
  <!--        </tr>-->
  <!--        </thead>-->
  <!--      </DataTable>-->
  <!--    </div>-->
  <!--  </div>-->
</template>
<script>
import Toolbar from "../../components/pages/Toolbar.vue";
import Cookies from "js-cookie";
import { useRouter } from "vue-router";
import { useSettingsStore } from "../../stores/settings.store.js";
import { request } from "../../helpers/request.js";
import { toast } from "vue3-toastify";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      handlersAttached: false,
      datatable: null,
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    loadData() {
      const tableElement = document.getElementById("users");
      let table = $(tableElement);
      table.dataTable().fnDestroy();

      this.datatable = $(table).DataTable({
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
          url: `${import.meta.env.VITE_API_URL}/api/admin/users`,
          type: "GET",
          headers: {
            Authorization: "Bearer " + Cookies.get("token"),
          },
        },
        columns: [
          { data: "id" },
          {
            data: "avatar",
            render: (data, type, row) =>
              `<img src="${data}" alt="image" width="50" height="50" style="border-radius:20%">`,
          },
          { data: "username" },
          {
            data: "balance",
            render: function (data, type, row) {
              return row.balance / 100 + " ₽";
            },
          },
          {
            data: "role",
          },
          {
            data: "total_bet",
            render: function (data, type, row) {
              return row.total_bet / 100 + " ₽";
            },
          },
          {
            data: "total_earned",
            render: function (data, type, row) {
              return row.total_earned / 100 + " ₽";
            },
          },
          {
            data: "referral_balance",
            render: function (data, type, row) {
              return row.referral_balance / 100 + " ₽";
            },
          },
          {
            data: "referral_level",
          },
          {
            data: "referrals_count",
          },
          {
            data: "total_deposited",
            render: function (data, type, row) {
              return row.total_deposited / 100 + " ₽";
            },
          },
          { data: null },
        ],
        columnDefs: [
          {
            targets: -1,
            orderable: false,
            className: "actions",
            render: function (data, type, row) {
              return `
                                <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать">
                                    <i class="ki-duotone ki-mouse-square fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                            `;
            },
          },
        ],
      });

      if (!this.handlersAttached) {
        this.datatable.on("click", 'td.actions a[data-action="edit"]', (e) => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          this.goToUser(id);
        });

        this.handlersAttached = true;
      }

      this.handleSearch();
    },

    handleSearch() {
      const search = document.querySelector("[data-search-input]");
      if (search) {
        search.addEventListener("keyup", (e) => {
          this.datatable.search(e.target.value).draw();
        });
      }
    },

    goToUser(id) {
      this.$router.push({ name: "users.id", params: { id } });
    },
  },
};
</script>
