<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Розыгрыши">
            <!-- <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#add_giveaway"
            >
                Создать розыгрыш
            </button> -->
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_giveaway"
            >
                Редактировать розыгрыш
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-success"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#select_winner"
            >
                Выбрать победителя
            </button>
        </Toolbar>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <div class="card card-flush">
                    <!-- <div
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
                                    placeholder="Поиск..."
                                />
                            </div>
                        </div>
                    </div> -->
                    <div class="card-body">
                        <table
                            class="table align-middle rounded table-row-dashed fs-6 g-5"
                            id="giveaways"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <tr>
                                    <th>ID</th>
                                    <th>Предмет</th>
                                    <th>Тип</th>
                                    <th>Депозит</th>
                                    <th>Начало</th>
                                    <th>Конец</th>
                                    <th>Статус</th>
                                    <th>Участники</th>
                                    <th>Победитель</th>
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

    <!-- Модальное окно создания розыгрыша -->
    <div class="modal fade" tabindex="-1" id="add_giveaway">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Создать розыгрыш</h3>
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
                    <div class="form-group">
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Предмет</label>
                            <select
                                v-model="newGiveaway.drop_id"
                                class="form-control"
                            >
                                <option value="">Выберите предмет</option>
                                <option
                                    v-for="item in availableItems"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.title }} - {{ item.steam_price / 100 }}₽
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Тип розыгрыша</label>
                            <select
                                v-model="newGiveaway.type"
                                class="form-control"
                            >
                                <option value="hourly">Ежечасный (1 час)</option>
                                <option value="daily">Ежедневный (24 часа)</option>
                                <option value="weekly">Еженедельный (7 дней)</option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required"
                                >Минимальный депозит (₽)</label
                            >
                            <input
                                v-model="newGiveaway.min_deposit"
                                type="number"
                                class="form-control"
                                placeholder="50"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label">Дата начала</label>
                            <input
                                v-model="newGiveaway.started_at"
                                type="datetime-local"
                                class="form-control"
                            />
                            <small class="text-muted"
                                >Оставьте пустым для начала сейчас</small
                            >
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Дата окончания</label>
                            <input
                                v-model="newGiveaway.finished_at"
                                type="datetime-local"
                                class="form-control"
                            />
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
                        @click="createGiveaway"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Создать
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования розыгрыша -->
    <div class="modal fade" tabindex="-1" id="edit_giveaway">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Редактировать розыгрыш</h3>
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
                    <div class="form-group">
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Предмет</label>
                            <select
                                v-model="editGiveaway.drop_id"
                                class="form-control"
                            >
                                <option value="">Выберите предмет</option>
                                <option
                                    v-for="item in availableItems"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.title }} - {{ item.steam_price / 100 }}₽
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Тип розыгрыша</label>
                            <select
                                v-model="editGiveaway.type"
                                class="form-control"
                            >
                                <option value="hourly">Ежечасный (1 час)</option>
                                <option value="daily">Ежедневный (24 часа)</option>
                                <option value="weekly">Еженедельный (7 дней)</option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required"
                                >Минимальный депозит (₽)</label
                            >
                            <input
                                v-model="editGiveaway.min_deposit"
                                type="number"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Дата начала</label>
                            <input
                                v-model="editGiveaway.started_at"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Дата окончания</label>
                            <input
                                v-model="editGiveaway.finished_at"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-label required">Статус</label>
                            <select
                                v-model="editGiveaway.status"
                                class="form-control"
                            >
                                <option value="IN PROCESS">В процессе</option>
                                <option value="FINISHED">Завершен</option>
                                <option value="FAILED">Отменен</option>
                            </select>
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
                        @click="updateGiveaway"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно выбора победителя -->
    <div class="modal fade" tabindex="-1" id="select_winner">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Выбрать победителя (Розыгрыш #{{ currentGiveaway.id }})
                    </h3>
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
                    <div class="alert alert-info">
                        <strong>Участники розыгрыша:</strong>
                        {{ currentGiveaway.participants?.length || 0 }}
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12 mb-5">
                            <label class="form-label"
                                >Выбрать победителя вручную</label
                            >
                            <select
                                v-model="selectedWinnerId"
                                class="form-control"
                            >
                                <option value="">
                                    Выбрать случайного участника
                                </option>
                                <option
                                    v-for="participant in currentGiveaway.participants"
                                    :key="participant.id"
                                    :value="participant.user_id"
                                >
                                    {{ participant.user?.username }} (ID:
                                    {{ participant.user_id }})
                                </option>
                            </select>
                            <small class="text-muted"
                                >Оставьте пустым для случайного выбора</small
                            >
                        </div>
                    </div>

                    <div v-if="currentGiveaway.participants?.length > 0">
                        <h5 class="mb-3">Список участников:</h5>
                        <div
                            class="table-responsive"
                            style="max-height: 300px; overflow-y: auto"
                        >
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Дата участия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="participant in currentGiveaway.participants"
                                        :key="participant.id"
                                    >
                                        <td>{{ participant.user_id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img
                                                    v-if="participant.user?.avatar"
                                                    :src="participant.user.avatar"
                                                    class="rounded-circle me-2"
                                                    style="width: 30px; height: 30px"
                                                />
                                                {{ participant.user?.username }}
                                            </div>
                                        </td>
                                        <td>{{ participant.created_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="alert alert-warning">
                        Нет участников в этом розыгрыше
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
                        @click="selectWinner"
                        type="submit"
                        class="btn btn-success"
                        :disabled="!currentGiveaway.participants?.length"
                    >
                        <i class="ki-duotone ki-check-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Определить победителя
                    </button>
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
            newGiveaway: {
                drop_id: "",
                type: "hourly",
                min_deposit: 50,
                started_at: "",
                finished_at: "",
            },
            editGiveaway: {
                id: 0,
                drop_id: "",
                type: "hourly",
                min_deposit: 50,
                started_at: "",
                finished_at: "",
                status: "IN PROCESS",
            },
            currentGiveaway: {
                id: null,
                participants: [],
            },
            selectedWinnerId: "",
            availableItems: [],
            handlersAttached: false,
            datatable: null,
        };
    },
    methods: {
        async loadData() {
            const table = $("#giveaways");

            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            this.datatable = table.DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/giveaways`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    beforeSend: function() {
                        console.log('Отправка запроса к API:', `${import.meta.env.VITE_API_URL}/api/admin/giveaways`);
                        console.log('Токен:', Cookies.get("token"));
                    },
                    dataSrc: function(json) {
                        console.log('Получен ответ от API:', json);
                        return json.data;
                    },
                    error: function (xhr, error, code) {
                        console.error('DataTables error:', error, code);
                        console.error('Response status:', xhr.status);
                        console.error('Response text:', xhr.responseText);
                        if (xhr.status === 401) {
                            toast.error('Ошибка авторизации. Войдите заново.');
                        } else {
                            toast.error('Ошибка загрузки данных: ' + error);
                        }
                    }
                },
                columns: [
                    { data: "id" },
                    {
                        data: "item",
                        orderable: false,
                        searchable: false,
                        render: (data) => {
                            if (!data) return "N/A";
                            return `
                                <div class="d-flex align-items-center">
                                    <img src="${data.image}" style="width: 50px; height: 40px; object-fit: contain; margin-right: 10px;" />
                                    <span>${data.title}</span>
                                </div>
                            `;
                        },
                    },
                    {
                        data: "type",
                        render: (data) => {
                            const types = {
                                hourly: '<span class="badge badge-info">Каждый час</span>',
                                daily: '<span class="badge badge-success">Каждый день</span>',
                                weekly: '<span class="badge badge-primary">Каждую неделю</span>',
                            };
                            return types[data] || data;
                        },
                    },
                    {
                        data: "min_deposit",
                        render: (data) => `${data}₽`,
                    },
                    {
                        data: "started_at",
                        render: (data) => {
                            if (!data) return "N/A";
                            return new Date(data).toLocaleString("ru-RU");
                        },
                    },
                    {
                        data: "finished_at",
                        render: (data) => {
                            if (!data) return "N/A";
                            return new Date(data).toLocaleString("ru-RU");
                        },
                    },
                    {
                        data: "status",
                        render: (data) => {
                            const statuses = {
                                "IN PROCESS":
                                    '<span class="badge badge-warning">В процессе</span>',
                                FINISHED:
                                    '<span class="badge badge-success">Завершен</span>',
                                FAILED:
                                    '<span class="badge badge-danger">Отменен</span>',
                            };
                            return statuses[data] || data;
                        },
                    },
                    {
                        data: "participants",
                        orderable: false,
                        searchable: false,
                        render: (data) => {
                            return data ? data.length : 0;
                        },
                    },
                    {
                        data: "winner",
                        orderable: false,
                        searchable: false,
                        render: (data) => {
                            if (!data) return "—";
                            return `
                                <div class="d-flex align-items-center">
                                    ${data.avatar ? `<img src="${data.avatar}" class="rounded-circle me-2" style="width: 30px; height: 30px;" />` : ""}
                                    <span>${data.username}</span>
                                </div>
                            `;
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
                            const winnerBtn =
                                row.status === "IN PROCESS"
                                    ? `<a href="#" class="btn btn-icon text-hover-success" data-action="winner" data-id="${row.id}" title="Выбрать победителя">
                                    <i class="ki-duotone ki-award fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </a>`
                                    : "";

                            return `
                                <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать">
                                    <i class="ki-duotone ki-notepad-edit fs-1"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                                ${winnerBtn}
                                <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить">
                                    <i class="ki-duotone ki-trash-square fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </a>
                            `;
                        },
                    },
                ],
            });

            if (!this.handlersAttached) {
                this.datatable.on(
                    "click",
                    'td.actions a[data-action="edit"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.getGiveaway(id);
                    }
                );

                this.datatable.on(
                    "click",
                    'td.actions a[data-action="delete"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.deleteGiveaway(id);
                    }
                );

                this.datatable.on(
                    "click",
                    'td.actions a[data-action="winner"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.showWinnerModal(id);
                    }
                );

                this.handlersAttached = true;
            }

            const search = document.querySelector("[data-search-input]");
            if (search) {
                search.addEventListener("keyup", (e) => {
                    this.datatable.search(e.target.value).draw();
                });
            }
        },

        async loadItems() {
            try {
                const { data } = await request(
                    "GET",
                    "/api/admin/giveaways/items"
                );
                if (data.success) {
                    this.availableItems = data.items;
                }
            } catch (error) {
                console.error("Error loading items:", error);
            }
        },

        createGiveaway() {
            request("POST", "/api/admin/giveaways/create", this.newGiveaway).then(
                ({ data }) => {
                    if (data.success) {
                        toast.success(data.message);
                        this.loadData();
                        $('div[data-bs-dismiss="modal"]').click();
                        this.resetNewGiveaway();
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        getGiveaway(id) {
            request("GET", "/api/admin/giveaways/get", { id }).then(
                ({ data }) => {
                    if (data.success) {
                        this.editGiveaway = {
                            id: data.giveaway.id,
                            drop_id: data.giveaway.drop_id,
                            type: data.giveaway.type,
                            min_deposit: data.giveaway.min_deposit,
                            started_at: data.giveaway.started_at,
                            finished_at: data.giveaway.finished_at,
                            status: data.giveaway.status,
                        };
                        $('button[data-bs-target="#edit_giveaway"]').click();
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        updateGiveaway() {
            request("POST", "/api/admin/giveaways/update", this.editGiveaway).then(
                ({ data }) => {
                    if (data.success) {
                        $('div[data-bs-dismiss="modal"]').click();
                        this.loadData();
                        toast.success(data.message);
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        deleteGiveaway(id) {
            if (!confirm("Вы уверены, что хотите удалить этот розыгрыш?")) {
                return;
            }

            request("POST", "/api/admin/giveaways/delete", { id }).then(
                ({ data }) => {
                    if (data.success) {
                        this.loadData();
                        toast.success(data.message);
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        async showWinnerModal(id) {
            try {
                const { data } = await request(
                    "GET",
                    "/api/admin/giveaways/get",
                    { id }
                );
                if (data.success) {
                    this.currentGiveaway = data.giveaway;
                    this.selectedWinnerId = "";
                    $('button[data-bs-target="#select_winner"]').click();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при загрузке данных розыгрыша");
            }
        },

        selectWinner() {
            const payload = {
                id: this.currentGiveaway.id,
            };

            if (this.selectedWinnerId) {
                payload.user_id = this.selectedWinnerId;
            }

            request("POST", "/api/admin/giveaways/select-winner", payload).then(
                ({ data }) => {
                    if (data.success) {
                        toast.success(data.message);
                        $('div[data-bs-dismiss="modal"]').click();
                        this.loadData();
                        this.selectedWinnerId = "";
                        this.currentGiveaway = { id: null, participants: [] };
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        resetNewGiveaway() {
            this.newGiveaway = {
                drop_id: "",
                type: "hourly",
                min_deposit: 50,
                started_at: "",
                finished_at: "",
            };
        },
    },
    mounted() {
        this.loadData();
        this.loadItems();
    },
};
</script>

<style scoped>
.actions a:hover {
    background: #fff;
}
</style>

