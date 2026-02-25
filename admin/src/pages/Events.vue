<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Ивенты">
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#add_event"
            >
                Создать ивент
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_event"
                ref="editEventBtn"
            >
                Редактировать ивент
            </button>
        </Toolbar>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <div class="card card-flush">
                    <div class="card-body">
                        <table
                            class="table align-middle rounded table-row-dashed fs-6 g-5"
                            id="events"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Дата начала</th>
                                    <th>Дата окончания</th>
                                    <th>Активен</th>
                                    <th>Призов</th>
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

    <!-- Модальное окно создания ивента -->
    <div class="modal fade" tabindex="-1" id="add_event">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Создать ивент</h3>
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
                            <label class="form-label required">Название ивента</label>
                            <input
                                v-model="newEvent.name"
                                type="text"
                                class="form-control"
                                placeholder="Название ивента"
                            />
                        </div>
                        <div class="col-lg-6 mb-5">
                            <label class="form-label required">Дата начала</label>
                            <input
                                v-model="newEvent.start_date"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-6 mb-5">
                            <label class="form-label required">Дата окончания</label>
                            <input
                                v-model="newEvent.end_date"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input
                                    v-model="newEvent.is_active"
                                    class="form-check-input"
                                    type="checkbox"
                                    checked
                                />
                                <span class="form-check-label">Активен</span>
                            </label>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-5"></div>

                    <div class="mb-5">
                        <h4 class="mb-3">Призы за места (1-50)</h4>
                        <div class="alert alert-info">
                            <strong>Пример JSON формата для rewards:</strong>
                            <pre class="mt-2 mb-0" style="font-size: 11px;">{
  "1": {
    "item_id": 123,
    "title": "AK-47 | Redline",
    "price": 45000
  },
  "2": {
    "item_id": 456,
    "title": "AWP | Asiimov",
    "price": 35000
  }
}</pre>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div
                            v-for="position in 50"
                            :key="position"
                            class="col-lg-3 col-md-4 col-sm-6"
                        >
                            <div class="card card-flush h-100">
                                <div class="card-header pt-4">
                                    <div class="card-title">
                                        <span class="badge badge-primary fs-6">Место {{ position }}</span>
                                    </div>
                                </div>
                                <div class="card-body pt-2">
                                    <div v-if="newEvent.prizes && newEvent.prizes[position]" class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <img
                                                :src="newEvent.prizes[position].item?.image || '/images/placeholder.png'"
                                                alt=""
                                                class="me-2"
                                                style="width: 40px; height: 30px; object-fit: contain;"
                                            />
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-7">
                                                    {{ newEvent.prizes[position].item?.title || 'Не выбран' }}
                                                </div>
                                                <div class="text-muted fs-8">
                                                    {{ newEvent.prizes[position].item?.steam_price ? (newEvent.prizes[position].item.steam_price / 100) + ' ₽' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-muted mb-3">
                                        <small>Приз не назначен</small>
                                    </div>
                                    <button
                                        @click="openPrizeModalForNew(position)"
                                        type="button"
                                        class="btn btn-sm btn-light-primary w-100"
                                    >
                                        {{ newEvent.prizes && newEvent.prizes[position] ? 'Изменить' : 'Выбрать приз' }}
                                    </button>
                                </div>
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
                        @click="createEvent"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Создать
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования ивента -->
    <div class="modal fade" tabindex="-1" id="edit_event">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Редактировать ивент</h3>
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
                            <label class="form-label required">Название ивента</label>
                            <input
                                v-model="editEvent.name"
                                type="text"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-6 mb-5">
                            <label class="form-label required">Дата начала</label>
                            <input
                                v-model="editEvent.start_date"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-6 mb-5">
                            <label class="form-label required">Дата окончания</label>
                            <input
                                v-model="editEvent.end_date"
                                type="datetime-local"
                                class="form-control"
                            />
                        </div>
                        <div class="col-lg-12 mb-5">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input
                                    v-model="editEvent.is_active"
                                    class="form-check-input"
                                    type="checkbox"
                                />
                                <span class="form-check-label">Активен</span>
                            </label>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-5"></div>

                    <div class="mb-5">
                        <h4 class="mb-3">Призы за места (1-50)</h4>
                        <div class="alert alert-info">
                            <strong>Пример JSON формата для rewards:</strong>
                            <pre class="mt-2 mb-0" style="font-size: 11px;">{
  "1": {
    "item_id": 123,
    "title": "AK-47 | Redline",
    "price": 45000
  },
  "2": {
    "item_id": 456,
    "title": "AWP | Asiimov",
    "price": 35000
  }
}</pre>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div
                            v-for="position in 50"
                            :key="position"
                            class="col-lg-3 col-md-4 col-sm-6"
                        >
                            <div class="card card-flush h-100">
                                <div class="card-header pt-4">
                                    <div class="card-title">
                                        <span class="badge badge-primary fs-6">Место {{ position }}</span>
                                    </div>
                                </div>
                                <div class="card-body pt-2">
                                    <div v-if="editEvent.prizes && editEvent.prizes[position]" class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <img
                                                :src="editEvent.prizes[position].item?.image || '/images/placeholder.png'"
                                                alt=""
                                                class="me-2"
                                                style="width: 40px; height: 30px; object-fit: contain;"
                                            />
                                            <div class="flex-grow-1">
                                                <div class="fw-bold fs-7">
                                                    {{ editEvent.prizes[position].item?.title || 'Не выбран' }}
                                                </div>
                                                <div class="text-muted fs-8">
                                                    {{ editEvent.prizes[position].item?.steam_price ? (editEvent.prizes[position].item.steam_price / 100) + ' ₽' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-muted mb-3">
                                        <small>Приз не назначен</small>
                                    </div>
                                    <button
                                        @click="openPrizeModal(position)"
                                        type="button"
                                        class="btn btn-sm btn-light-primary w-100"
                                    >
                                        {{ editEvent.prizes && editEvent.prizes[position] ? 'Изменить' : 'Выбрать приз' }}
                                    </button>
                                </div>
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
                        @click="updateEvent"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно выбора приза -->
    <div class="modal fade" tabindex="-1" id="select_prize">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Выбрать приз за место {{ currentPrizePosition }}
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
                    <div class="mb-5">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label">Поиск</label>
                                <input
                                    v-model="prizeSearch"
                                    @input="loadPrizeItems"
                                    type="text"
                                    class="form-control"
                                    placeholder="Название предмета..."
                                />
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Мин. цена (₽)</label>
                                <input
                                    v-model.number="prizeMinPrice"
                                    @input="loadPrizeItems"
                                    type="number"
                                    class="form-control"
                                    placeholder="0"
                                />
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Макс. цена (₽)</label>
                                <input
                                    v-model.number="prizeMaxPrice"
                                    @input="loadPrizeItems"
                                    type="number"
                                    class="form-control"
                                    placeholder="100000"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="sticky-top bg-[#345045]">
                                <tr>
                                    <th>Изображение</th>
                                    <th>Название</th>
                                    <th>Цена</th>
                                    <th>Действие</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in prizeItems" :key="item.id">
                                    <td>
                                        <img
                                            :src="item.image"
                                            alt=""
                                            style="width: 50px; height: 35px; object-fit: contain;"
                                        />
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ item.title }}</div>
                                        <small class="text-muted">{{ item.weapon }} | {{ item.skin_name }}</small>
                                    </td>
                                    <td>{{ (item.steam_price / 100).toFixed(2) }} ₽</td>
                                    <td>
                                        <button
                                            @click="selectPrizeItem(item.id)"
                                            type="button"
                                            class="btn btn-sm btn-primary"
                                        >
                                            Выбрать
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="prizeItems.length === 0" class="text-center py-5">
                            <div class="text-muted">Предметы не найдены</div>
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
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Toolbar from "../components/pages/Toolbar.vue";
import { request } from "../helpers/request.js";
import { toast } from "vue3-toastify";
import Cookies from "js-cookie";

export default {
    components: {
        Toolbar,
    },
    data() {
        return {
            eventsTable: null,
            newEvent: {
                name: "",
                start_date: "",
                end_date: "",
                is_active: true,
                prizes: {},
            },
            isCreatingEvent: false,
            editEvent: {
                id: null,
                name: "",
                start_date: "",
                end_date: "",
                is_active: false,
                prizes: {},
            },
            currentPrizePosition: null,
            prizeItems: [],
            prizeSearch: "",
            prizeMinPrice: null,
            prizeMaxPrice: null,
        };
    },
    mounted() {
        this.initTable();
    },
    methods: {
        initTable() {
            const self = this;
            this.eventsTable = $("#events").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/events`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    dataSrc: function(json) {
                        return json.data;
                    },
                },
                columns: [
                    { data: "id" },
                    { data: "name" },
                    {
                        data: "start_date",
                        render: function (data) {
                            if (!data) return "-";
                            return new Date(data).toLocaleString("ru-RU");
                        },
                    },
                    {
                        data: "end_date",
                        render: function (data) {
                            if (!data) return "-";
                            return new Date(data).toLocaleString("ru-RU");
                        },
                    },
                    {
                        data: "is_active",
                        render: function (data) {
                            return data
                                ? '<span class="badge badge-success">Активен</span>'
                                : '<span class="badge badge-secondary">Неактивен</span>';
                        },
                    },
                    {
                        data: "id",
                        render: function (data, type, row) {
                            return '<span class="badge badge-info">' + (row.prizes_count || 0) + ' призов</span>';
                        },
                    },
                    {
                        data: "id",
                        orderable: false,
                        render: function (data) {
                            return `
                                <button
                                    onclick="window.editEvent(${data})"
                                    class="btn btn-sm btn-light-primary me-2"
                                >
                                    Редактировать
                                </button>
                                <button
                                    onclick="window.deleteEvent(${data})"
                                    class="btn btn-sm btn-light-danger"
                                >
                                    Удалить
                                </button>
                            `;
                        },
                    },
                ],
                order: [[0, "desc"]],
            });

            window.editEvent = (id) => {
                self.loadEvent(id);
            };

            window.deleteEvent = (id) => {
                if (confirm("Вы уверены, что хотите удалить этот ивент?")) {
                    self.deleteEvent(id);
                }
            };
        },

        async loadEvent(id) {
            try {
                const { data } = await request("GET", "/api/admin/events/get", {
                    id: id,
                });
                if (data.success) {
                    this.editEvent = {
                        id: data.event.id,
                        name: data.event.name,
                        start_date: this.formatDateTimeLocal(data.event.start_date),
                        end_date: this.formatDateTimeLocal(data.event.end_date),
                        is_active: data.event.is_active,
                        prizes: data.event.prizes || {},
                    };
                    const modal = new bootstrap.Modal(
                        document.getElementById("edit_event")
                    );
                    modal.show();
                } else {
                    toast.error(data.message || "Ошибка при загрузке ивента");
                }
            } catch (error) {
                console.error("Ошибка при загрузке ивента:", error);
                toast.error("Ошибка при загрузке ивента");
            }
        },

        async createEvent() {
            if (!this.newEvent.name || !this.newEvent.start_date || !this.newEvent.end_date) {
                toast.error("Заполните все обязательные поля");
                return;
            }

            // Формируем массив призов для отправки
            const prizes = {};
            if (this.newEvent.prizes) {
                Object.keys(this.newEvent.prizes).forEach(position => {
                    const prize = this.newEvent.prizes[position];
                    if (prize && prize.item_id) {
                        prizes[position] = {
                            item_id: prize.item_id,
                        };
                    }
                });
            }

            try {
                const { data } = await request("POST", "/api/admin/events/create", {
                    name: this.newEvent.name,
                    start_date: this.newEvent.start_date,
                    end_date: this.newEvent.end_date,
                    is_active: this.newEvent.is_active ? 1 : 0,
                    prizes: prizes,
                });

                if (data.success) {
                    toast.success(data.message || "Ивент создан");
                    bootstrap.Modal.getInstance(document.getElementById("add_event")).hide();
                    this.newEvent = {
                        name: "",
                        start_date: "",
                        end_date: "",
                        is_active: true,
                        prizes: {},
                    };
                    this.eventsTable.ajax.reload();
                } else {
                    toast.error(data.message || "Ошибка при создании ивента");
                }
            } catch (error) {
                console.error("Ошибка при создании ивента:", error);
                toast.error("Ошибка при создании ивента");
            }
        },

        async updateEvent() {
            if (!this.editEvent.name || !this.editEvent.start_date || !this.editEvent.end_date) {
                toast.error("Заполните все обязательные поля");
                return;
            }

            try {
                const { data } = await request("POST", "/api/admin/events/update", {
                    id: this.editEvent.id,
                    name: this.editEvent.name,
                    start_date: this.editEvent.start_date,
                    end_date: this.editEvent.end_date,
                    is_active: this.editEvent.is_active ? 1 : 0,
                });

                if (data.success) {
                    toast.success(data.message || "Ивент обновлен");
                    this.eventsTable.ajax.reload();
                } else {
                    toast.error(data.message || "Ошибка при обновлении ивента");
                }
            } catch (error) {
                console.error("Ошибка при обновлении ивента:", error);
                toast.error("Ошибка при обновлении ивента");
            }
        },

        async deleteEvent(id) {
            try {
                const { data } = await request("POST", "/api/admin/events/delete", {
                    id: id,
                });

                if (data.success) {
                    toast.success(data.message || "Ивент удален");
                    this.eventsTable.ajax.reload();
                } else {
                    toast.error(data.message || "Ошибка при удалении ивента");
                }
            } catch (error) {
                console.error("Ошибка при удалении ивента:", error);
                toast.error("Ошибка при удалении ивента");
            }
        },

        openPrizeModal(position) {
            this.currentPrizePosition = position;
            this.isCreatingEvent = false;
            this.loadPrizeItems();
            const modal = new bootstrap.Modal(
                document.getElementById("select_prize")
            );
            modal.show();
        },

        openPrizeModalForNew(position) {
            this.currentPrizePosition = position;
            this.isCreatingEvent = true;
            this.loadPrizeItems();
            const modal = new bootstrap.Modal(
                document.getElementById("select_prize")
            );
            modal.show();
        },

        async loadPrizeItems() {
            try {
                const params = {
                    position: this.currentPrizePosition,
                };

                if (this.prizeSearch) {
                    params.search = this.prizeSearch;
                }

                if (this.prizeMinPrice) {
                    params.min = Math.round(this.prizeMinPrice * 100);
                }

                if (this.prizeMaxPrice) {
                    params.max = Math.round(this.prizeMaxPrice * 100);
                }

                const { data } = await request("GET", "/api/admin/events/items", params);

                if (data.success) {
                    this.prizeItems = data.items || [];
                }
            } catch (error) {
                console.error("Ошибка при загрузке предметов:", error);
                toast.error("Ошибка при загрузке предметов");
            }
        },

        async selectPrizeItem(itemId) {
            if (!this.currentPrizePosition) {
                toast.error("Ошибка: не выбрано место");
                return;
            }

            // Если создаем новый ивент, просто сохраняем в локальном состоянии
            if (this.isCreatingEvent) {
                // Ищем предмет в уже загруженном списке
                const item = this.prizeItems.find(i => i.id === itemId);
                if (item) {
                    if (!this.newEvent.prizes) {
                        this.newEvent.prizes = {};
                    }
                    this.newEvent.prizes[this.currentPrizePosition] = {
                        item_id: item.id,
                        item: item,
                    };
                    bootstrap.Modal.getInstance(document.getElementById("select_prize")).hide();
                    toast.success("Приз выбран");
                } else {
                    toast.error("Предмет не найден");
                }
                return;
            }

            // Если редактируем существующий ивент
            if (!this.editEvent.id) {
                toast.error("Ошибка: не выбран ивент");
                return;
            }

            try {
                const { data } = await request("POST", "/api/admin/events/prize/update", {
                    event_id: this.editEvent.id,
                    position: this.currentPrizePosition,
                    item_id: itemId,
                });

                if (data.success) {
                    toast.success("Приз обновлен");
                    bootstrap.Modal.getInstance(document.getElementById("select_prize")).hide();
                    // Перезагружаем ивент для обновления призов
                    await this.loadEvent(this.editEvent.id);
                } else {
                    toast.error(data.message || "Ошибка при обновлении приза");
                }
            } catch (error) {
                console.error("Ошибка при обновлении приза:", error);
                toast.error("Ошибка при обновлении приза");
            }
        },

        formatDateTimeLocal(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },
    },
};
</script>

