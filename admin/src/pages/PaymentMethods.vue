<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Пополнения">
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#add_methods"
            >
                Добавить метод
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_methods"
            >
                Редактировать метод
            </button></Toolbar
        >

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
                            id="payments_methods"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <!--begin::Table row-->
                                <tr>
                                    <th>ID</th>
                                    <th>Иконка</th>
                                    <th>Название</th>
                                    <th>Система</th>
                                    <th>Метод</th>
                                    <th>Статус</th>
                                    <th>Минимальная сумма</th>
                                    <th>Максимальная сумма</th>
                                    <th>Роут</th>
                                    <th>Позиция</th>
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

    <div class="modal fade" tabindex="-1" id="add_methods">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Создать метод</h3>
                    <div
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="ki-duotone ki-cross fs-1"
                            ><span class="path1"></span
                            ><span class="path2"></span
                        ></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Название</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.name"
                                    type="text"
                                    class="form-control"
                                    placeholder="СБП"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Система</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.system"
                                    type="text"
                                    class="form-control"
                                    placeholder="GMpays"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Метод</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.method"
                                    type="text"
                                    class="form-control"
                                    placeholder="sbp"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Активность</label>
                            <select
                                v-model="newMethods.is_active"
                                class="form-control"
                            >
                                <option value="1">Активен</option>
                                <option value="0">Не активен</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Минимальная сумма</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.min_amount"
                                    type="number"
                                    class="form-control"
                                    placeholder="100"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Максимальная сумма</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.max_amount"
                                    type="number"
                                    class="form-control"
                                    placeholder="100000"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Роут</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.api_url"
                                    type="text"
                                    class="form-control"
                                    placeholder="/payment/sbp"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Позиция</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.sort_order"
                                    type="number"
                                    class="form-control"
                                    placeholder="3"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Иконка</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newMethods.icon"
                                    type="text"
                                    class="form-control"
                                    placeholder="3"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Закрыть
                    </button>
                    <button
                        @click="createMethods"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="edit_methods">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Редактировать метод</h3>
                    <div
                        class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    >
                        <i class="ki-duotone ki-cross fs-1"
                            ><span class="path1"></span
                            ><span class="path2"></span
                        ></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Название</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.name"
                                    type="text"
                                    class="form-control"
                                    placeholder="СБП"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Система</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.system"
                                    type="text"
                                    class="form-control"
                                    placeholder="GMpays"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Метод</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.method"
                                    type="text"
                                    class="form-control"
                                    placeholder="sbp"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Активность</label>
                            <select
                                v-model="editMethods.is_active"
                                class="form-control"
                            >
                                <option value="1">Активен</option>
                                <option value="0">Не активен</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Минимальная сумма</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.min_amount"
                                    type="number"
                                    class="form-control"
                                    placeholder="100"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Максимальная сумма</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.max_amount"
                                    type="number"
                                    class="form-control"
                                    placeholder="100000"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label class="form-label">Роут</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.api_url"
                                    type="text"
                                    class="form-control"
                                    placeholder="/payment/sbp"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Позиция</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.sort_order"
                                    type="number"
                                    class="form-control"
                                    placeholder="3"
                                />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">Иконка</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editMethods.icon"
                                    type="text"
                                    class="form-control"
                                    placeholder="3"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Закрыть
                    </button>
                    <button
                        @click="saveMethod"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
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
    name: "PaymentsPage",
    components: {
        Toolbar,
    },
    data() {
        return {
            datatable: null,
            handlersAttached: false,
            editMethods: {
                id: 0,
                name: "",
                system: "",
                method: "",
                is_active: 0,
                min_amount: 0,
                max_amount: 0,
                api_url: "",
                icon: "",
                sort_order: 0,
            },
            newMethods: {
                id: 0,
                name: "",
                system: "",
                method: "",
                is_active: 0,
                min_amount: 0,
                max_amount: 0,
                api_url: "",
                icon: "",
                sort_order: 0,
            },
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

            const table = $("#payments_methods");

            this.datatable = $(table).DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${
                        import.meta.env.VITE_API_URL
                    }/api/admin/payments/getMethods`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                },
                columns: [
                    { data: "id" },
                    {
                        data: "icon",
                        render: (data, type, row) => {
                            return `<img src="${
                                import.meta.env.VITE_APP_FRONTEND_URL + data
                            }" alt="image" width="90" height="50">`;
                        },
                    },
                    { data: "name" },
                    { data: "system" },
                    { data: "method" },
                    {
                        data: "is_active",
                        render: (data, type, row) => {
                            if (row.is_active === 1) {
                                return `<span class="badge badge-success">Активен</span>`;
                            }
                            return `<span class="badge badge-danger">Не активен</span>`;
                        },
                    },
                    {
                        data: "min_amount",
                        render: (data, type, row) => {
                            return row.min_amount + " ₽";
                        },
                    },
                    {
                        data: "max_amount",
                        render: (data, type, row) => {
                            return row.max_amount + " ₽";
                        },
                    },
                    { data: "api_url" },
                    { data: "sort_order" },

                    { data: null },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        className: "actions",
                        render: (data, type, row) => {
                            return `
                            <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать"> <i class="ki-duotone ki-mouse-square fs-1"><span class="path1"></span><span class="path2"></span></i></a>
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

                this.datatable.on(
                    "click",
                    'td.actions a[data-action="edit"]',
                    (e) => {
                        e.preventDefault();
                        this.getMethods(e.currentTarget.dataset.id);
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

        getMethods(id) {
            request("GET", "/api/admin/payments/methods", { id }).then(
                ({ data }) => {
                    if (!data.success) return toast.error(data.message);

                    this.editMethods = { ...data.method, image: null };


                    $('button[data-bs-target="#edit_methods"]').click();
                }
            );
        },

        async deletePayment(id) {
            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/payments/deleteMethods",
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
                toast.error("Ошибка при удалении метода");
            }
        },

        saveMethod() {
            const formData = new FormData();
            for (let key in this.editMethods) {
                formData.append(key, this.editMethods[key]);
            }

            request("POST", "/api/admin/payments/save", formData, {
                headers: {
                    "content-type": "multipart/form-data",
                },
            }).then(({ data }) => {
                if (!data.success) return toast.error(data.message);
                toast.success(data.message);
                this.loadData();
                $('div[data-bs-dismiss="modal"]').click();
            });
        },
        createMethods() {
            const formData = new FormData();
            for (let key in this.newMethods) {
                formData.append(key, this.newMethods[key]);
            }

            request("POST", "/api/admin/payments/create", formData, {
                headers: {
                    "content-type": "multipart/form-data",
                },
            }).then(({ data }) => {
                if (!data.success) return toast.error(data.message);
                this.loadData();

                $('div[data-bs-dismiss="modal"]').click();
                toast.success(data.message);
            });
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
