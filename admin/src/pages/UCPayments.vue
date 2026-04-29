<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Выводы UC" />

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
                                    placeholder="Поиск по UID или Имени"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table
                            class="table align-middle rounded table-row-dashed fs-6 g-5"
                            id="uc_payments"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>PUBG UID</th>
                                    <th>Кол-во UC</th>
                                    <th>Цена (UZS)</th>
                                    <th>Статус</th>
                                    <th>Дата</th>
                                    <th>Действия</th>
                                </tr>
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
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";

export default {
    name: "UCPaymentsPage",
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
            if (this.datatable) {
                this.datatable.clear().destroy();
            }

            const table = $("#uc_payments");

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
                    data: (d) => {
                        d.system = 'uc'; // Фильтруем только UC на стороне сервера
                    }
                },
                columns: [
                    { data: "id" },
                    {
                        data: "user",
                        render: (data, type, row) => row.user.username,
                    },
                    { 
                        data: "pubg_uid",
                        render: (data) => `<strong>${data || '-'}</strong>`
                    },
                    { data: "amount" },
                    {
                        data: "price",
                        render: (data) => (data / 100).toLocaleString() + " UZS",
                    },
                    {
                        data: "status",
                        render: (data) => {
                            if (data === 0) return '<div class="badge badge-warning">Ожидает</div>';
                            if (data === 1) return '<div class="badge badge-success">Выплачено</div>';
                            if (data === 2) return '<div class="badge badge-danger">Отклонено</div>';
                            return data;
                        },
                    },
                    { 
                        data: "created_at",
                        render: (data) => new Date(data).toLocaleString()
                    },
                    { data: null },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        className: "actions",
                        render: (data, type, row) => {
                            let actions = '';
                            if (row.status === 0) {
                                actions += `
                                    <a href="#" class="btn btn-sm btn-light-success btn-icon-success me-2" data-action="confirm" data-id="${row.id}" title="Подтвердить">
                                        <i class="ki-duotone ki-check fs-2"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light-danger btn-icon-danger me-2" data-action="decline" data-id="${row.id}" title="Отклонить">
                                        <i class="ki-duotone ki-cross fs-2"></i>
                                    </a>`;
                            }
                            actions += `
                                <a href="#" class="btn btn-sm btn-light-danger btn-icon-danger" data-action="delete" data-id="${row.id}" title="Удалить">
                                    <i class="ki-duotone ki-trash fs-2"></i>
                                </a>`;
                            return actions;
                        },
                    },
                ],
            });

            this.attachEventHandlers();
            this.handleSearch();
        },

        attachEventHandlers() {
            if (!this.handlersAttached && this.datatable) {
                const table = $("#uc_payments");
                
                table.on("click", 'a[data-action="confirm"]', (e) => {
                    e.preventDefault();
                    this.confirmUC(e.currentTarget.dataset.id);
                });

                table.on("click", 'a[data-action="decline"]', (e) => {
                    e.preventDefault();
                    this.declineUC(e.currentTarget.dataset.id);
                });

                table.on("click", 'a[data-action="delete"]', (e) => {
                    e.preventDefault();
                    this.deletePayment(e.currentTarget.dataset.id);
                });

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

        async confirmUC(id) {
            try {
                const { data } = await request("POST", "/api/admin/uc/confirm", { id });
                if (data.success) {
                    this.datatable.draw(false);
                    toast.success(data.message);
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при подтверждении");
            }
        },

        async declineUC(id) {
            try {
                const { data } = await request("POST", "/api/admin/uc/decline", { id });
                if (data.success) {
                    this.datatable.draw(false);
                    toast.success(data.message);
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при отклонении");
            }
        },

        async deletePayment(id) {
            if(!confirm("Удалить запись?")) return;
            try {
                const { data } = await request("POST", "/api/admin/payments/delete", { id });
                if (data.success) {
                    this.datatable.draw(false);
                    toast.success(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при удалении");
            }
        }
    },
    beforeUnmount() {
        if (this.datatable) {
            this.datatable.clear().destroy();
        }
    },
};
</script>
