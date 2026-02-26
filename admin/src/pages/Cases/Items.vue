<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Предметы">
            <div class="d-flex gap-2">
                <button
                    @click="generate"
                    type="button"
                    class="btn btn-sm fw-bold btn-primary"
                >
                    Сгенерировать предметы
                </button>
                <button
                    @click="calcChance"
                    type="button"
                    class="btn btn-sm fw-bold btn-primary"
                >
                    Рассчитать шансы предметов
                </button>
                <button
                    type="button"
                    class="btn btn-sm fw-bold btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#add_item"
                >
                    Добавить предмет
                </button>
                <button
                    @click="loadRTPData"
                    type="button"
                    class="btn btn-sm fw-bold btn-success"
                >
                    <i class="ki-duotone ki-chart-line fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Управление RTP
                </button>
            </div>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_item"
            >
                Редактировать кейс
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#rtp_modal"
            >
                RTP Modal
            </button>
        </Toolbar>

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
                            id="items"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <!--begin::Table row-->
                                <tr>
                                    <th>ID</th>
                                    <th>Изображение</th>
                                    <th>Предмет</th>
                                    <th>Цена</th>
                                    <th>Шанс выпадения</th>
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

    <div class="modal fade" tabindex="-1" id="add_item">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Добавить предмет</h3>
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
                        <div class="col-lg-6">
                            <label class="form-label">Предмет</label>
                            <div class="input-group mb-5">
                                <select
                                    class="form-select"
                                    id="select_new_item"
                                    data-dropdown-parent="#add_item"
                                >
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Шанс выпадения</label>
                            <div class="input-group mb-5">
                                <input
                                    type="text"
                                    v-model="newItem.chance"
                                    class="form-control"
                                    placeholder="Шанс выпадения"
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
                        @click="createItem"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="edit_item">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Редактировать предмет</h3>
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
                        <div class="col-lg-6">
                            <label class="form-label">Предмет</label>
                            <div class="input-group mb-5">
                                <input
                                    type="text"
                                    v-model="editItem.item.name"
                                    readonly
                                    class="form-control"
                                    placeholder="Предмет"
                                />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Шанс выпадения</label>
                            <div class="input-group mb-5">
                                <input
                                    type="text"
                                    v-model="editItem.chance"
                                    class="form-control"
                                    placeholder="Шанс выпадения"
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
                        @click="saveItem"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно управления RTP -->
    <div class="modal fade" tabindex="-1" id="rtp_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Управление RTP кейса</h3>
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
                    <!-- Текущая статистика -->
                    <div class="alert alert-info mb-5">
                        <h5 class="mb-3">Текущая статистика</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-gray-600">Текущий RTP:</div>
                                <div class="fs-2 fw-bold" :class="getRTPColor(rtpData.current_rtp, rtpData.target_rtp)">
                                    {{ rtpData.current_rtp }}%
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-gray-600">Открыто кейсов:</div>
                                <div class="fs-2 fw-bold">{{ rtpData.total_opened }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-gray-600">Потрачено:</div>
                                <div class="fs-4 fw-bold">{{ (rtpData.total_spent / 100).toFixed(2) }}₽</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-gray-600">Выиграно:</div>
                                <div class="fs-4 fw-bold">{{ (rtpData.total_won / 100).toFixed(2) }}₽</div>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки RTP -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <label class="form-label required">Целевой RTP (%)</label>
                            <input
                                v-model="rtpSettings.target_rtp"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="50"
                                max="100"
                            />
                            <small class="text-muted">Желаемый процент возврата</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Минимальный RTP (%)</label>
                            <input
                                v-model="rtpSettings.min_rtp"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="50"
                                max="100"
                            />
                            <small class="text-muted">Нижний порог</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Максимальный RTP (%)</label>
                            <input
                                v-model="rtpSettings.max_rtp"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="50"
                                max="100"
                            />
                            <small class="text-muted">Верхний порог</small>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Внимание!</strong> Система автоматически корректирует шансы выпадения предметов,
                        чтобы удерживать RTP в заданных пределах.
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        @click="resetRTP"
                        type="button"
                        class="btn btn-danger me-auto"
                    >
                        Сбросить статистику
                    </button>
                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Закрыть
                    </button>
                    <button
                        @click="updateRTP"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить настройки
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Toolbar from "../../components/pages/Toolbar.vue";
import Cookies from "js-cookie";
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";

export default {
    components: {
        Toolbar,
    },
    data() {
        return {
            newItem: {
                skin_id: null,
                chance: 0,
            },
            editItem: {
                skin_id: null,
                chance: 1,
                item: {
                    name: "",
                },
            },
            rtpData: {
                current_rtp: 95,
                target_rtp: 95,
                min_rtp: 85,
                max_rtp: 98,
                total_opened: 0,
                total_spent: 0,
                total_won: 0,
            },
            rtpSettings: {
                target_rtp: 95,
                min_rtp: 85,
                max_rtp: 98,
            },
            handlersAttached: false,
        };
    },
    mounted() {
        this.loadData();
        this.initSelect2();
    },
    methods: {
        initSelect2() {
            $("#select_new_item").select2({
                placeholder: "Выберите предмет",
                ajax: {
                    delay: 250,
                    type: "GET",
                    url: `${
                        import.meta.env.VITE_API_URL
                    }/api/admin/cases/items/all`,
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: data.results,
                            pagination: {
                                more: data.more,
                            },
                        };
                    },
                },
            });
        },
        loadData() {
            const boxId = this.$route.params.id;
            let table = $("#items");

            if ($.fn.dataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            const datatable = $(table).DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${
                        import.meta.env.VITE_API_URL
                    }/api/admin/cases/items`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    data: function (d) {
                        d.boxId = boxId;
                    },
                    dataSrc: function (json) {
                        return json.data;
                    },
                },
                columns: [
                    { data: "id" },
                    {
                        data: "item.image",
                        render: (data) =>
                            `<img src="${data}" alt="image" width="50" height="50">`,
                    },
                    {
                        data: "item.title",
                    },
                    {
                        data: "item.steam_price",
                        render: function (data, type, row) {
                            return (data / 100).toFixed(2) + " ₽";
                        },
                    },
                    {
                        data: "chance",
                        render: function (data, type, row) {
                            return row.chance + "%";
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
                  <i class="ki-duotone ki-mouse-square fs-1"><span class="path1"></span><span class="path2"></span></i>
                </a>
                <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить">
                  <i class="ki-duotone ki-trash-square fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                </a>
              `;
                        },
                    },
                ],
            });

            if (!this.handlersAttached) {
                datatable.on(
                    "click",
                    'td.actions a[data-action="edit"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.getItem(id);
                    }
                );

                datatable.on(
                    "click",
                    'td.actions a[data-action="delete"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.deleteItem(id);
                    }
                );

                this.handlersAttached = true;
            }

            const handleSearch = () => {
                const search = document.querySelector("[data-search-input]");
                if (search) {
                    search.addEventListener("keyup", (e) => {
                        datatable.search(e.target.value).draw();
                    });
                }
            };

            handleSearch();
        },
        createItem() {
            this.newItem.skin_id = $("#select_new_item")
                .find(":selected")
                .val();

            request("POST", "/api/admin/cases/items/create", {
                ...this.newItem,
                box_id: this.$route.params.id,
            }).then(({ data }) => {
                if (!data.success) {
                    toast.error(data.message);
                } else {
                    this.loadData();
                    $('div[data-bs-dismiss="modal"]').click();
                    toast.success(data.message);
                }
            });
        },
        getItem(id) {
            request("GET", "/api/admin/cases/items/item", {
                id: id,
            }).then(({ data }) => {
                if (!data.success) {
                    toast.error(data.message);
                } else {
                    const item = data.item;
                    this.editItem = data.item;
                    $('button[data-bs-target="#edit_item"]').click();
                }
            });
        },
        saveItem() {
            this.editItem.skin_id = $("#select_edit_item")
                .find(":selected")
                .val();

            request("POST", "/api/admin/cases/items/save", {
                ...this.editItem,
                box_id: this.$route.params.id,
            }).then(({ data }) => {
                if (!data.success) {
                    toast.error(data.message);
                } else {
                    this.loadData();
                    $('div[data-bs-dismiss="modal"]').click();
                    toast.success(data.message);
                }
            });
        },
        deleteItem(id) {
            request("POST", "/api/admin/cases/items/delete", {
                id: id,
            }).then(({ data }) => {
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            });
        },
        calcChance() {
            request("POST", "/api/admin/cases/items/chance", {
                id: this.$route.params.id,
            }).then(({ data }) => {
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            });
        },
        generate() {
            request("POST", "/api/admin/cases/items/generate", {
                id: this.$route.params.id,
            }).then(({ data }) => {
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            });
        },

        async loadRTPData() {
            try {
                const { data } = await request("GET", "/api/admin/cases/rtp", {
                    id: this.$route.params.id,
                });
                if (data.success) {
                    this.rtpData = data.rtp;
                    this.rtpSettings = {
                        target_rtp: data.rtp.target_rtp,
                        min_rtp: data.rtp.min_rtp,
                        max_rtp: data.rtp.max_rtp,
                    };
                    $('button[data-bs-target="#rtp_modal"]').click();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка загрузки данных RTP");
            }
        },

        async updateRTP() {
            try {
                const { data } = await request("POST", "/api/admin/cases/rtp/update", {
                    id: this.$route.params.id,
                    ...this.rtpSettings,
                });
                if (data.success) {
                    toast.success(data.message);
                    this.loadRTPData();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка обновления RTP");
            }
        },

        async resetRTP() {
            if (!confirm("Вы уверены, что хотите сбросить всю статистику RTP этого кейса?")) {
                return;
            }

            try {
                const { data } = await request("POST", "/api/admin/cases/rtp/reset", {
                    id: this.$route.params.id,
                });
                if (data.success) {
                    toast.success(data.message);
                    this.loadRTPData();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка сброса статистики");
            }
        },

        getRTPColor(current, target) {
            // С новой формулой RTP = (Потрачено / Выиграно) * 100:
            // - Высокий RTP (>= 95%) = казино в плюсе (зеленый)
            // - Средний RTP (>= 90%) = норма (желтый)
            // - Низкий RTP (< 90%) = казино в минусе (красный) - проблемный
            if (current >= 95) return "text-success";
            if (current >= 90) return "text-warning";
            return "text-danger";
        },
    },
};
</script>
