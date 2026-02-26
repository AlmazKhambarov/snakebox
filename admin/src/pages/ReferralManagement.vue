<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Реферальная система">
            <button
                type="button"
                class="btn btn-sm fw-bold btn-info"
                @click="loadStatistics"
            >
                <i class="ki-duotone ki-chart-simple fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                Статистика
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_referral"
            >
                Редактировать
            </button>
        </Toolbar>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <!-- Статистика -->
                <div v-if="showStatistics" class="mb-5">
                    <!-- Основная статистика -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-primary d-block"
                                        >{{ statistics.total_users_with_referrals }}</span
                                    >
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Пользователей с рефералами</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-success d-block"
                                        >{{ (statistics.total_referral_earnings / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Всего выплачено</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-info d-block"
                                        >{{ statistics.total_users_with_referrer }}</span
                                    >
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Привлеченных пользователей</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-warning d-block"
                                        >{{ statistics.conversion_rate }}%</span
                                    >
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Конверсия в депозит</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика за периоды -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-success">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.earnings_last_7_days / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Выплачено за 7 дней</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-info">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.new_referrals_last_7_days }}</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Новых рефералов за 7 дней</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-success">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.earnings_last_30_days / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Выплачено за 30 дней</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-info">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.new_referrals_last_30_days }}</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Новых рефералов за 30 дней</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Средние показатели -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.average_referrals_per_user }}</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >Среднее рефералов на пользователя</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.avg_earnings_per_referrer / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >Средний заработок реферера</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.avg_deposit_referred_users / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >Средний депозит реферала</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная информация -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.users_with_custom_percentage }}</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >С индивидуальным процентом</span
                                    >
                                    <span class="fs-8 text-muted d-block mt-1"
                                        >Средний: {{ statistics.avg_custom_percentage }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.referrals_with_deposit }}</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >Рефералов с депозитом</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.total_deposits_from_referrals / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-500 d-block"
                                        >Общий депозит рефералов</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Топ рефереров -->
                    <div class="row g-5 mb-5">
                        <div class="col-xl-6">
                            <div class="card card-flush h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Топ по количеству рефералов</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-row-bordered">
                                            <thead>
                                                <tr class="fw-bold text-gray-600">
                                                    <th>#</th>
                                                    <th>Пользователь</th>
                                                    <th>Рефералов</th>
                                                    <th>Заработано</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(user, index) in statistics.top_referrers_by_count"
                                                    :key="user.id"
                                                >
                                                    <td>{{ index + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                v-if="user.avatar"
                                                                :src="user.avatar"
                                                                class="rounded-circle me-2"
                                                                style="width: 25px; height: 25px"
                                                            />
                                                            <span>{{ user.username }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold">{{ user.referrals_count }}</td>
                                                    <td class="text-success">{{ (user.total_earned / 100).toFixed(2) }}₽</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card card-flush h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Топ по заработку</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-row-bordered">
                                            <thead>
                                                <tr class="fw-bold text-gray-600">
                                                    <th>#</th>
                                                    <th>Пользователь</th>
                                                    <th>Заработано</th>
                                                    <th>Баланс</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(user, index) in statistics.top_referrers_by_earnings"
                                                    :key="user.id"
                                                >
                                                    <td>{{ index + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                v-if="user.avatar"
                                                                :src="user.avatar"
                                                                class="rounded-circle me-2"
                                                                style="width: 25px; height: 25px"
                                                            />
                                                            <span>{{ user.username }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="fw-bold text-success">{{ (user.total_earned / 100).toFixed(2) }}₽</td>
                                                    <td class="text-primary">{{ (user.referral_balance / 100).toFixed(2) }}₽</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика по типам начислений -->
                    <div class="row g-5 mb-5" v-if="statistics.earnings_by_type && statistics.earnings_by_type.length > 0">
                        <div class="col-12">
                            <div class="card card-flush">
                                <div class="card-header">
                                    <h3 class="card-title">Статистика по типам начислений</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered">
                                            <thead>
                                                <tr class="fw-bold text-gray-600">
                                                    <th>Тип</th>
                                                    <th>Всего выплат</th>
                                                    <th>Количество</th>
                                                    <th>Средняя выплата</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="earning in statistics.earnings_by_type"
                                                    :key="earning.type"
                                                >
                                                    <td>
                                                        <span class="badge badge-light-primary">{{ earning.type }}</span>
                                                    </td>
                                                    <td class="fw-bold text-success">{{ (earning.total / 100).toFixed(2) }}₽</td>
                                                    <td>{{ earning.count }}</td>
                                                    <td class="text-gray-600">{{ (earning.average / 100).toFixed(2) }}₽</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-flush">
                    <div class="card-body">
                        <table
                            class="table align-middle rounded table-row-dashed fs-6 g-5"
                            id="referrals"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <tr>
                                    <th>ID</th>
                                    <th>Пользователь</th>
                                    <th>Реф. код</th>
                                    <th>Уровень</th>
                                    <th>Свой %</th>
                                    <th>Рефералов</th>
                                    <th>Заработано</th>
                                    <th>Баланс</th>
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

    <!-- Модальное окно редактирования -->
    <div class="modal fade" tabindex="-1" id="edit_referral">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Управление рефералами: {{ currentUser.username }}
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
                    <!-- Основная информация -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <label class="form-label">Реферальный код</label>
                            <div class="input-group">
                                <input
                                    v-model="editData.referral_code"
                                    type="text"
                                    class="form-control"
                                    placeholder="AUTO123"
                                />
                                <button
                                    @click="generateNewCode"
                                    class="btn btn-secondary"
                                    type="button"
                                >
                                    <i class="ki-duotone ki-arrows-circle fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Уровень (1-5)</label>
                            <input
                                v-model="editData.referral_level"
                                type="number"
                                class="form-control"
                                min="1"
                                max="5"
                            />
                            <small class="text-muted"
                                >Стандартные проценты: 0.5%, 1%, 1.5%, 2%, 2.5%</small
                            >
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"
                                >Индивидуальный процент (%)</label
                            >
                            <input
                                v-model="editData.custom_referral_percentage"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="0"
                                max="100"
                                placeholder="Не установлен"
                            />
                            <small class="text-muted"
                                >Переопределяет стандартный процент уровня</small
                            >
                        </div>
                    </div>

                    <!-- Добавление баланса -->
                    <div class="separator my-5"></div>
                    <h4 class="mb-3">Начислить баланс</h4>
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Сумма (₽)</label>
                            <input
                                v-model="addBalanceData.amount"
                                type="number"
                                class="form-control"
                                step="0.01"
                                min="0.01"
                                placeholder="100"
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Описание</label>
                            <input
                                v-model="addBalanceData.description"
                                type="text"
                                class="form-control"
                                placeholder="Ручное начисление"
                            />
                        </div>
                    </div>
                    <button
                        @click="addBalance"
                        class="btn btn-success mb-5"
                        type="button"
                    >
                        <i class="ki-duotone ki-plus-circle fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Начислить
                    </button>

                    <!-- Статистика пользователя -->
                    <div class="separator my-5"></div>
                    <div class="row g-5 mb-5">
                        <div class="col-md-3">
                            <div class="border border-gray-300 border-dashed rounded p-3">
                                <div class="fs-2 fw-bold text-gray-900">
                                    {{ currentUser.referrals_count || 0 }}
                                </div>
                                <div class="fs-7 text-gray-600">Рефералов</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border border-gray-300 border-dashed rounded p-3">
                                <div class="fs-2 fw-bold text-success">
                                    {{ (currentUser.total_earned / 100).toFixed(2) }}₽
                                </div>
                                <div class="fs-7 text-gray-600">Всего заработано</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border border-gray-300 border-dashed rounded p-3">
                                <div class="fs-2 fw-bold text-primary">
                                    {{ (currentUser.referral_balance / 100).toFixed(2) }}₽
                                </div>
                                <div class="fs-7 text-gray-600">Текущий баланс</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border border-gray-300 border-dashed rounded p-3">
                                <div class="fs-2 fw-bold text-info">
                                    {{ ((currentUser.referrals_total_deposited || 0) / 100).toFixed(2) }}₽
                                </div>
                                <div class="fs-7 text-gray-600">Депозиты рефералов</div>
                            </div>
                        </div>
                    </div>

                    <!-- Список рефералов -->
                    <div v-if="currentUser.referrals && currentUser.referrals.length > 0">
                        <h5 class="mb-3">Рефералы ({{ currentUser.referrals.length }})</h5>
                        <div
                            class="table-responsive"
                            style="max-height: 300px; overflow-y: auto"
                        >
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Пользователь</th>
                                        <th>Депозит</th>
                                        <th>Дата регистрации</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="ref in currentUser.referrals"
                                        :key="ref.id"
                                    >
                                        <td>{{ ref.id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img
                                                    v-if="ref.avatar"
                                                    :src="ref.avatar"
                                                    class="rounded-circle me-2"
                                                    style="width: 30px; height: 30px"
                                                />
                                                {{ ref.username }}
                                            </div>
                                        </td>
                                        <td>{{ (ref.total_deposited / 100).toFixed(2) }}₽</td>
                                        <td>{{ ref.created_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- История начислений -->
                    <div v-if="currentUser.earnings && currentUser.earnings.length > 0" class="mt-5">
                        <h5 class="mb-3">История начислений (последние 50)</h5>
                        <div
                            class="table-responsive"
                            style="max-height: 300px; overflow-y: auto"
                        >
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Реферал</th>
                                        <th>Сумма</th>
                                        <th>%</th>
                                        <th>Описание</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="earning in currentUser.earnings"
                                        :key="earning.id"
                                    >
                                        <td>{{ earning.created_at }}</td>
                                        <td>
                                            {{ earning.referral ? earning.referral.username : '—' }}
                                        </td>
                                        <td class="text-success fw-bold">
                                            +{{ (earning.amount / 100).toFixed(2) }}₽
                                        </td>
                                        <td>{{ earning.percentage }}%</td>
                                        <td>{{ earning.description }}</td>
                                    </tr>
                                </tbody>
                            </table>
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
                        @click="updateReferral"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Сохранить изменения
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
    components: {
        Toolbar,
    },
    data() {
        return {
            currentUser: {},
            editData: {
                referral_code: "",
                referral_level: 1,
                custom_referral_percentage: null,
            },
            addBalanceData: {
                amount: null,
                description: "",
            },
            statistics: {
                total_users_with_referrer: 0,
                total_referral_earnings: 0,
                total_users_with_referrals: 0,
                average_referrals_per_user: 0,
                conversion_rate: 0,
                earnings_last_7_days: 0,
                new_referrals_last_7_days: 0,
                earnings_last_30_days: 0,
                new_referrals_last_30_days: 0,
                avg_earnings_per_referrer: 0,
                avg_deposit_referred_users: 0,
                users_with_custom_percentage: 0,
                avg_custom_percentage: 0,
                referrals_with_deposit: 0,
                total_deposits_from_referrals: 0,
                top_referrers_by_count: [],
                top_referrers_by_earnings: [],
                earnings_by_type: [],
                users_by_level: {},
                earnings_by_date: [],
            },
            showStatistics: false,
            handlersAttached: false,
            datatable: null,
        };
    },
    methods: {
        async loadData() {
            const table = $("#referrals");

            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            this.datatable = table.DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                order: [[0, "desc"]],
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/referrals`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                    error: function (xhr, error, code) {
                        console.error("DataTables error:", error, code);
                        if (xhr.status === 401) {
                            toast.error("Ошибка авторизации");
                        } else {
                            toast.error("Ошибка загрузки данных");
                        }
                    },
                },
                columns: [
                    { data: "id" },
                    {
                        data: "username",
                        render: (data, type, row) => {
                            return `
                                <div class="d-flex align-items-center">
                                    ${row.avatar ? `<img src="${row.avatar}" class="rounded-circle me-2" style="width: 30px; height: 30px;" />` : ""}
                                    <span>${data}</span>
                                </div>
                            `;
                        },
                    },
                    { data: "referral_code" },
                    {
                        data: "referral_level",
                        render: (data) => {
                            const colors = {
                                1: "secondary",
                                2: "info",
                                3: "primary",
                                4: "warning",
                                5: "success",
                            };
                            return `<span class="badge badge-${colors[data] || "secondary"}">Уровень ${data}</span>`;
                        },
                    },
                    {
                        data: "custom_referral_percentage",
                        render: (data) => {
                            return data !== null
                                ? `<span class="badge badge-success">${data}%</span>`
                                : "—";
                        },
                    },
                    { data: "referrals_count" },
                    {
                        data: "total_earned",
                        render: (data) => `${(data / 100).toFixed(2)}₽`,
                    },
                    {
                        data: "referral_balance",
                        render: (data) => `${(data / 100).toFixed(2)}₽`,
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
                                <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать">
                                    <i class="ki-duotone ki-notepad-edit fs-1"><span class="path1"></span><span class="path2"></span></i>
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
                        this.getUserData(id);
                    }
                );

                this.handlersAttached = true;
            }
        },

        async getUserData(id) {
            try {
                const { data } = await request("GET", "/api/admin/referrals/get", {
                    id,
                });
                if (data.success) {
                    this.currentUser = data.user;
                    this.editData = {
                        referral_code: data.user.referral_code,
                        referral_level: data.user.referral_level,
                        custom_referral_percentage:
                            data.user.custom_referral_percentage,
                    };
                    this.addBalanceData = {
                        amount: null,
                        description: "",
                    };
                    $('button[data-bs-target="#edit_referral"]').click();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при загрузке данных");
            }
        },

        async updateReferral() {
            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/referrals/update",
                    {
                        id: this.currentUser.id,
                        ...this.editData,
                    }
                );
                if (data.success) {
                    toast.success(data.message);
                    $('div[data-bs-dismiss="modal"]').click();
                    this.loadData();
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при обновлении");
            }
        },

        async generateNewCode() {
            if (
                !confirm(
                    "Сгенерировать новый реферальный код? Старый перестанет работать."
                )
            ) {
                return;
            }

            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/referrals/generate-code",
                    {
                        id: this.currentUser.id,
                    }
                );
                if (data.success) {
                    toast.success(data.message);
                    this.editData.referral_code = data.referral_code;
                    this.currentUser.referral_code = data.referral_code;
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при генерации кода");
            }
        },

        async addBalance() {
            if (!this.addBalanceData.amount || this.addBalanceData.amount <= 0) {
                toast.error("Укажите корректную сумму");
                return;
            }

            try {
                const { data } = await request(
                    "POST",
                    "/api/admin/referrals/add-balance",
                    {
                        user_id: this.currentUser.id,
                        amount: this.addBalanceData.amount * 100, // конвертируем в копейки
                        description: this.addBalanceData.description,
                    }
                );
                if (data.success) {
                    toast.success(data.message);
                    this.addBalanceData = { amount: null, description: "" };
                    this.getUserData(this.currentUser.id); // Обновляем данные
                } else {
                    toast.error(data.message);
                }
            } catch (error) {
                toast.error("Ошибка при начислении");
            }
        },

        async loadStatistics() {
            try {
                const { data } = await request(
                    "GET",
                    "/api/admin/referrals/statistics"
                );
                if (data.success) {
                    this.statistics = data.statistics;
                    this.showStatistics = true;
                    toast.success("Статистика загружена");
                } else {
                    toast.error("Ошибка при загрузке статистики");
                }
            } catch (error) {
                toast.error("Ошибка при загрузке статистики");
            }
        },
    },
    mounted() {
        this.loadData();
    },
};
</script>

<style scoped>
.actions a:hover {
    background: #fff;
}
</style>

