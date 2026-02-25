<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Выводы" />

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
                            id="withdraws"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <!--begin::Table row-->
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Откуда</th>
                                    <th>Трейд ID</th>
                                    <th>Маркет ID</th>
                                    <th>Кастомный ID</th>
                                    <th>Картинка предмета</th>
                                    <th>Предмет</th>
                                    <th>Цена</th>
                                    <th>Время</th>
                                    <th>Статус</th>
                                    <!-- <th>Действия</th> -->
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
import { toast } from "vue3-toastify";
import { request } from "../helpers/request.js";

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
    methods: {
        loadData() {
            const table = $("#withdraws");
            table.DataTable().clear().destroy();

            this.datatable = $(table).DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/withdraws`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    dataSrc: function (json) {
                        console.log("API ответ /api/admin/withdraws:", json); // Логируем ответ
                        return json.data || []; // обязательно вернуть массив данных
                    },
                },
                columns: [
                    { data: "id" },
                    {
                        data: null,
                        render: (data) => `<a href="/users/${data.user.id}">${data.user.username}</a>`,
                    },
                     { data: "from_where" },
                     { data: "trade_id" },
                     { data: "market_id" },
                     { data: "custom_id",
                        render: (data) => `<div style="max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${data}</div>`,
                      },
                    {
                        data: "item.image",
                        render: (data, type, row) =>
                            `<img src="${data}" alt="image" width="50" height="50">`,
                    },
                    {
                        data: null,
                        render: (data) => data.item?.title || "—",
                    },
                    {
                        data: "price",
                        render: (data) => `${data / 100}₽`,
                    },
                    { data: "updated_at", 
                        render: (data) => moment(data).format("DD.MM.YYYY HH:mm"),
                     },
                     { data: "status",
                        render: (data) => {
                            if (data === "WITHDRAWN") {
                                return `<div class="badge badge-success">Выведен</div>`;
                            } else if (data === "SENDING") {
                                return `<div class="badge badge-warning">Закупаем предмет</div>`;
                            } else if (data === "WAIT") {
                                return `<div class="badge badge-warning">Ожидает продавца</div>`;
                            } else if (data === "ORDER_READY") {
                                return `<div class="badge badge-warning">Готов к выводу</div>`;
                            }
                        },
                     },
                  
                    // { data: null },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        className: "actions",
                        render: (data, type, row) => `
      <a href="#" class="btn btn-icon text-hover-danger" data-action="reject" data-id="${row.id}" title="Отказать">
        <i class="ki-duotone ki-cross-square fs-1"><span class="path1"></span><span class="path2"></span></i>
      </a>
      <a href="#" class="btn btn-icon text-hover-success" data-action="sent" data-id="${row.id}" title="Отправить">
        <i class="ki-duotone ki-check-square fs-1"><span class="path1"></span><span class="path2"></span></i>
      </a>
    `,
                    },
                ],
            });

            if (!this.handlersAttached) {
                this.handlersAttached = true;

                $(table).on("click", "a[data-action]", (e) => {
                    e.preventDefault();

                    const action = e.currentTarget.dataset.action;
                    const id = e.currentTarget.dataset.id;

                    if (action === "reject") {
                        this.reject(id);
                    } else if (action === "sent") {
                        this.approve(id);
                    }
                });
            }

            this.handleSearch();
        },
        async approve(id) {
            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/withdraws/approve",
                    {
                        id: [id],
                    }
                );
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                alert("Failed to approve items");
            }
        },
        async reject(id) {
            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/withdraws/reject",
                    {
                        id: [id],
                    }
                );
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                alert("error", "Failed to withdraw items");
            }
        },
        handleSearch() {
            const search = document.querySelector("[data-search-input]");
            if (search) {
                search.addEventListener("keyup", (e) => {
                    this.datatable.search(e.target.value).draw();
                });
            }
        },
    },
    mounted() {
        this.loadData();
    },
};
</script>
