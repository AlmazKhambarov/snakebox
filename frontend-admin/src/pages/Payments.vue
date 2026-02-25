<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Пополнения" />

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <div class="card card-flush">
                    <div
                        class="card-header align-items-center py-5 gap-2 gap-md-5"
                    >
                        <div class="card-title">
                            <!--begin::Search-->
                            <div
                                class="d-flex align-items-center position-relative my-1"
                            >
                                <span
                                    class="svg-icon fs-1 position-absolute ms-4"
                                >
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
                            id="users_payments"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <!--begin::Table row-->
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Промокод</th>
                                    <th>Система</th>
                                    <th>Метод</th>
                                    <th>Сумма</th>
                                    <th>Статус платежа</th>
                                    <th>Транзакция</th>
                                    <th>Информация</th>
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
</template>
<script>
import Toolbar from "../components/pages/Toolbar.vue";
import Cookies from "js-cookie";
import { request } from "../helpers/request.js";
import { toast } from "vue3-toastify";

export default {
    name: "PaymentsPage",
    components: {
        Toolbar,
    },
    data() {
        return {
            datatable: null,
            handlersAttached: false,
        };
    },
    mounted() {
        this.loadData();
    },
    methods: {
        async loadData() {
            // Уничтожаем существующую таблицу если есть
            if (this.datatable) {
                this.datatable.clear().destroy();
            }

            const table = $("#users_payments");

            this.datatable = $(table).DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/payments`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                },
                columns: [
                    { data: "id" },
                    {
                        data: "user",
                        render: (data, type, row) => {
                            return row.user.username;
                        },
                    },
                    {
                        data: "promocode",
                        render: (data, type, row) => {
                            return row.promo === null ? "Нет" : row.promo.code;
                        },
                    },
                    { data: "system" },
                    { data: "method" },
                    {
                        data: "amount",
                        render: (data, type, row) => {
                            return row.amount / 100 + " ₽";
                        },
                    },
                    {
                        data: "status",
                        render: (data, type, row) => {
                            return row.status === 0
                                ? '<div class="badge badge-warning">Ожидает оплату</div>'
                                : '<div class="badge badge-success">Оплачен</div>';
                        },
                    },
                    {
                        data: "transaction_id",
                    },
                    {
                        data: "metadata",
                        render: (data, type, row) => {
                            if (row.metadata) {
                                const info = [];
                                if (row.metadata.user_id) {
                                    info.push(`Время: ${row.metadata.time}`);
                                }
                                return info.length > 0
                                    ? info.join("<br>")
                                    : "-";
                            }
                            return "-";
                        },
                    },
                    { data: null },
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

            this.attachEventHandlers();
            this.handleSearch();
        },

        attachEventHandlers() {
            if (!this.handlersAttached && this.datatable) {
                this.datatable.on(
                    "click",
                    'td.actions a[data-action="delete"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.deletePayment(id);
                    }
                );

                this.handlersAttached = true;
            }
        },

        handleSearch() {
            const search = document.querySelector("[data-search-input]");
            if (search && this.datatable) {
                search.addEventListener("keyup", (e) => {
                    this.datatable.search(e.target.value).draw();
                });
            }
        },

        async deletePayment(id) {
            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/payments/delete",
                    {
                        id: id,
                    }
                );

                if (data.success) {
                    this.loadData();
                    toast.success(data.message);
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                console.error("Error deleting payment:", error);
                toast.error("Ошибка при удалении платежа");
            }
        },
    },

    beforeUnmount() {
        // Очищаем DataTable при размонтировании компонента
        if (this.datatable) {
            this.datatable.clear().destroy();
        }
    },
};
</script>
