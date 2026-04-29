<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Статистика кейсов">
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                @click="loadStatistics"
            >
                <i class="ki-duotone ki-arrows-circle fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Обновить
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-danger ms-2"
                @click="resetStatistics"
            >
                <i class="ki-duotone ki-trash fs-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                Сбросить
            </button>
        </Toolbar>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <div v-if="loading" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                </div>

                <div v-else-if="statistics">
                    <!-- Проблемные кейсы -->
                    <div
                        class="row g-5 g-xl-8 mb-5"
                        v-if="statistics.problematic_cases && statistics.problematic_cases.length > 0"
                    >
                        <div class="col-12">
                            <div class="card card-flush border-danger">
                                <div class="card-header bg-danger">
                                    <div class="card-title text-white">
                                        <i class="ki-duotone ki-information-5 fs-2 text-white me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Проблемные кейсы (низкий RTP - кейс сильно выдает пользователям)
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>Текущий RTP</th>
                                                    <th>Макс RTP</th>
                                                    <th>Целевой RTP</th>
                                                    <th>Открыто</th>
                                                    <th>Статус</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.problematic_cases"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span 
                                                            class="badge fs-6"
                                                            :class="caseItem.current_rtp >= 95 ? 'badge-success' : (caseItem.current_rtp >= 90 ? 'badge-warning' : 'badge-danger')"
                                                        >{{
                                                            caseItem.current_rtp
                                                        }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-warning fs-6">{{
                                                            caseItem.max_rtp
                                                        }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-info fs-6">{{
                                                            caseItem.target_rtp
                                                        }}%</span>
                                                    </td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                    <td>
                                                        <span
                                                            v-if="caseItem.is_active"
                                                            class="badge badge-success"
                                                            >Активен</span
                                                        >
                                                        <span v-else class="badge badge-danger">Отключен</span>
                                                    </td>
                                                    <td>
                                                        <router-link
                                                            :to="{ name: 'items', params: { id: caseItem.id } }"
                                                            class="btn btn-sm btn-primary"
                                                        >
                                                            Настроить RTP
                                                        </router-link>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Основная статистика -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-primary d-block">{{
                                        statistics.total_cases
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Всего кейсов</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-success d-block">{{
                                        statistics.active_cases
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Активных кейсов</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-info d-block">{{
                                        statistics.total_opened
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Всего открыто</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100">
                                <div class="card-body">
                                    <span
                                        class="fs-2hx fw-bold d-block"
                                        :class="
                                            statistics.overall_rtp >= 95
                                                ? 'text-success'
                                                : statistics.overall_rtp >= 90
                                                  ? 'text-warning'
                                                  : 'text-danger'
                                        "
                                        >{{ statistics.overall_rtp }}%</span
                                    >
                                    <span class="fs-6 fw-semibold text-gray-500 d-block"
                                        >Общий RTP</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Финансовая статистика -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-primary">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.total_spent / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Всего потрачено</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-warning">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.total_won / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Всего выиграно</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div
                                class="card card-flush h-100"
                                :class="
                                    statistics.total_profit >= 0
                                        ? 'bg-light-success'
                                        : 'bg-light-danger'
                                "
                            >
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ (statistics.total_profit / 100).toFixed(2) }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Прибыль</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-info">
                                <div class="card-body">
                                    <span class="fs-3 fw-bold text-gray-900 d-block"
                                        >{{ statistics.averages?.avg_win_per_open ? (statistics.averages.avg_win_per_open / 100).toFixed(2) : '0.00' }}₽</span
                                    >
                                    <span class="fs-7 fw-semibold text-gray-600 d-block"
                                        >Средний выигрыш</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительная статистика -->
                    <div class="row g-5 g-xl-8 mb-5" v-if="statistics.averages">
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-success">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-success d-block">{{
                                        statistics.averages.profitable_cases || 0
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-600 d-block"
                                        >Прибыльных кейсов</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-danger">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-danger d-block">{{
                                        statistics.averages.unprofitable_cases || 0
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-600 d-block"
                                        >Убыточных кейсов</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-success">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-success d-block">{{
                                        statistics.averages.cases_with_high_rtp || 0
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-600 d-block"
                                        >Кейсов с высоким RTP (выше целевого)</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="card card-flush h-100 bg-light-danger">
                                <div class="card-body">
                                    <span class="fs-2hx fw-bold text-danger d-block">{{
                                        statistics.averages.cases_with_low_rtp || 0
                                    }}</span>
                                    <span class="fs-6 fw-semibold text-gray-600 d-block"
                                        >Кейсов с низким RTP (<90%)</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика по периодам -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Статистика по периодам</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Период</th>
                                                    <th>Открыто</th>
                                                    <th>Потрачено</th>
                                                    <th>Выиграно</th>
                                                    <th>Прибыль</th>
                                                    <th>RTP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Сегодня</strong></td>
                                                    <td>{{ statistics.periods.today.total_opened }}</td>
                                                    <td>{{ (statistics.periods.today.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (statistics.periods.today.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="
                                                            statistics.periods.today.profit >= 0
                                                                ? 'text-success'
                                                                : 'text-danger'
                                                        "
                                                    >
                                                        {{ (statistics.periods.today.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td
                                                        :class="
                                                            statistics.periods.today.rtp >= 95
                                                                ? 'text-success'
                                                                : statistics.periods.today.rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ statistics.periods.today.rtp }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Последние 7 дней</strong></td>
                                                    <td>{{ statistics.periods.last_7_days.total_opened }}</td>
                                                    <td>{{ (statistics.periods.last_7_days.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (statistics.periods.last_7_days.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="
                                                            statistics.periods.last_7_days.profit >= 0
                                                                ? 'text-success'
                                                                : 'text-danger'
                                                        "
                                                    >
                                                        {{ (statistics.periods.last_7_days.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td
                                                        :class="
                                                            statistics.periods.last_7_days.rtp >= 95
                                                                ? 'text-success'
                                                                : statistics.periods.last_7_days.rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ statistics.periods.last_7_days.rtp }}%
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Последние 30 дней</strong></td>
                                                    <td>{{ statistics.periods.last_30_days.total_opened }}</td>
                                                    <td>{{ (statistics.periods.last_30_days.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (statistics.periods.last_30_days.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="
                                                            statistics.periods.last_30_days.profit >= 0
                                                                ? 'text-success'
                                                                : 'text-danger'
                                                        "
                                                    >
                                                        {{ (statistics.periods.last_30_days.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td
                                                        :class="
                                                            statistics.periods.last_30_days.rtp >= 95
                                                                ? 'text-success'
                                                                : statistics.periods.last_30_days.rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ statistics.periods.last_30_days.rtp }}%
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Топ кейсов -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Топ кейсов по открытиям</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>Открыто</th>
                                                    <th>RTP</th>
                                                    <th>Прибыль</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.top_by_opens"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                    <td
                                                        :class="
                                                            caseItem.current_rtp >= 95
                                                                ? 'text-success'
                                                                : caseItem.current_rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ caseItem.current_rtp }}%
                                                    </td>
                                                    <td
                                                        :class="
                                                            caseItem.profit >= 0
                                                                ? 'text-success'
                                                                : 'text-danger'
                                                        "
                                                    >
                                                        {{ (caseItem.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Топ кейсов по прибыльности</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>Прибыль</th>
                                                    <th>RTP</th>
                                                    <th>Открыто</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.top_by_profit"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-success fw-bold">
                                                        {{ (caseItem.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td
                                                        :class="
                                                            caseItem.current_rtp >= 95
                                                                ? 'text-success'
                                                                : caseItem.current_rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ caseItem.current_rtp }}%
                                                    </td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика по категориям -->
                    <div class="row g-5 g-xl-8 mb-5" v-if="statistics.category_stats && statistics.category_stats.length > 0">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Статистика по категориям</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Категория</th>
                                                    <th>Кейсов</th>
                                                    <th>Активных</th>
                                                    <th>Открыто</th>
                                                    <th>Потрачено</th>
                                                    <th>Выиграно</th>
                                                    <th>Прибыль</th>
                                                    <th>Средний RTP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="category in statistics.category_stats"
                                                    :key="category.id"
                                                >
                                                    <td class="fw-bold">{{ category.name }}</td>
                                                    <td>{{ category.cases_count }}</td>
                                                    <td>{{ category.active_cases }}</td>
                                                    <td>{{ category.total_opened }}</td>
                                                    <td>{{ (category.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (category.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="
                                                            category.profit >= 0
                                                                ? 'text-success'
                                                                : 'text-danger'
                                                        "
                                                    >
                                                        {{ (category.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td
                                                        :class="
                                                            category.avg_rtp >= 95
                                                                ? 'text-success'
                                                                : category.avg_rtp >= 90
                                                                  ? 'text-warning'
                                                                  : 'text-danger'
                                                        "
                                                    >
                                                        {{ category.avg_rtp }}%
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Топ кейсов с высоким RTP (лучшие) -->
                    <div class="row g-5 g-xl-8 mb-5" v-if="statistics.best_by_rtp && statistics.best_by_rtp.length > 0">
                        <div class="col-12">
                            <div class="card card-flush border-success">
                                <div class="card-header bg-success">
                                    <div class="card-title text-white">
                                        <i class="ki-duotone ki-award fs-2 text-white me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Топ кейсов с высоким RTP (самые прибыльные)
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>RTP</th>
                                                    <th>Открыто</th>
                                                    <th>Потрачено</th>
                                                    <th>Выиграно</th>
                                                    <th>Прибыль</th>
                                                    <th>Статус</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.best_by_rtp"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span 
                                                            class="badge fs-6"
                                                            :class="caseItem.current_rtp >= 95 ? 'badge-success' : (caseItem.current_rtp >= 90 ? 'badge-warning' : 'badge-danger')"
                                                        >{{ caseItem.current_rtp }}%</span>
                                                    </td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                    <td>{{ (caseItem.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (caseItem.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="caseItem.profit >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold'"
                                                    >
                                                        {{ (caseItem.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="caseItem.is_active"
                                                            class="badge badge-success"
                                                            >Активен</span
                                                        >
                                                        <span v-else class="badge badge-danger">Отключен</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Топ кейсов с низким RTP (проблемные) -->
                    <div class="row g-5 g-xl-8 mb-5" v-if="statistics.worst_by_rtp && statistics.worst_by_rtp.length > 0">
                        <div class="col-12">
                            <div class="card card-flush border-danger">
                                <div class="card-header bg-danger">
                                    <div class="card-title text-white">
                                        <i class="ki-duotone ki-information-5 fs-2 text-white me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Топ кейсов с низким RTP (проблемные - сильно выдают пользователям)
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-row-bordered table-hover">
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>RTP</th>
                                                    <th>Открыто</th>
                                                    <th>Потрачено</th>
                                                    <th>Выиграно</th>
                                                    <th>Убыток</th>
                                                    <th>Статус</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.worst_by_rtp"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span 
                                                            class="badge fs-6"
                                                            :class="caseItem.current_rtp >= 95 ? 'badge-success' : (caseItem.current_rtp >= 90 ? 'badge-warning' : 'badge-danger')"
                                                        >{{ caseItem.current_rtp }}%</span>
                                                    </td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                    <td>{{ (caseItem.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (caseItem.total_won / 100).toFixed(2) }}₽</td>
                                                    <td class="text-danger fw-bold">
                                                        {{ (caseItem.loss / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="caseItem.is_active"
                                                            class="badge badge-success"
                                                            >Активен</span
                                                        >
                                                        <span v-else class="badge badge-danger">Отключен</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Детальная статистика по всем кейсам -->
                    <div class="row g-5 g-xl-8 mb-5">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Детальная статистика по всем кейсам</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-row-bordered table-hover"
                                            id="all_cases_table"
                                        >
                                            <thead>
                                                <tr class="fw-bold fs-6 text-gray-800">
                                                    <th>Кейс</th>
                                                    <th>Категория</th>
                                                    <th>Цена</th>
                                                    <th>Открыто</th>
                                                    <th>Потрачено</th>
                                                    <th>Выиграно</th>
                                                    <th>Прибыль</th>
                                                    <th>RTP</th>
                                                    <th>Целевой RTP</th>
                                                    <th>Средний выигрыш</th>
                                                    <th>Статус</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="caseItem in statistics.all_cases"
                                                    :key="caseItem.id"
                                                >
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img
                                                                :src="getImageUrl(caseItem.image)"
                                                                class="me-3"
                                                                style="width: 40px; height: 30px; object-fit: contain;"
                                                            />
                                                            <span class="fw-bold">{{ caseItem.name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ caseItem.category || '—' }}</td>
                                                    <td>{{ (caseItem.price / 100).toFixed(2) }}₽</td>
                                                    <td>{{ caseItem.total_opened }}</td>
                                                    <td>{{ (caseItem.total_spent / 100).toFixed(2) }}₽</td>
                                                    <td>{{ (caseItem.total_won / 100).toFixed(2) }}₽</td>
                                                    <td
                                                        :class="
                                                            caseItem.profit >= 0
                                                                ? 'text-success fw-bold'
                                                                : 'text-danger fw-bold'
                                                        "
                                                    >
                                                        {{ (caseItem.profit / 100).toFixed(2) }}₽
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge"
                                                            :class="
                                                                caseItem.current_rtp >= 150
                                                                    ? 'badge-success'
                                                                    : caseItem.current_rtp >= 100
                                                                      ? 'badge-warning'
                                                                      : 'badge-danger'
                                                            "
                                                            >{{ caseItem.current_rtp }}%</span
                                                        >
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-light-info">{{
                                                            caseItem.target_rtp
                                                        }}%</span>
                                                    </td>
                                                    <td>{{ (caseItem.avg_win / 100).toFixed(2) }}₽</td>
                                                    <td>
                                                        <span
                                                            v-if="caseItem.is_active"
                                                            class="badge badge-success"
                                                            >Активен</span
                                                        >
                                                        <span v-else class="badge badge-danger">Отключен</span>
                                                        <span
                                                            v-if="caseItem.auto_disabled"
                                                            class="badge badge-warning ms-1"
                                                            >Авто</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import Toolbar from "../../components/pages/Toolbar.vue";
import Cookies from "js-cookie";
import { toast } from "vue3-toastify";

export default {
    name: "CasesStatistics",
    components: {
        Toolbar,
    },
    setup() {
        const statistics = ref(null);
        const loading = ref(false);
        const frontendUrl = import.meta.env.VITE_APP_FRONTEND_URL || '';

        const getImageUrl = (imagePath) => {
            if (!imagePath) return '';
            if (imagePath.startsWith('http')) return imagePath;
            return frontendUrl + imagePath;
        };

        const loadStatistics = async () => {
            loading.value = true;
            try {
                const response = await fetch(
                    `${import.meta.env.VITE_API_URL}/api/admin/cases/statistics`,
                    {
                        method: "GET",
                        headers: {
                            Authorization: "Bearer " + Cookies.get("token"),
                        },
                    }
                );

                const data = await response.json();

                if (data.success) {
                    statistics.value = data.statistics;
                } else {
                    toast.error(data.message || "Ошибка загрузки статистики");
                }
            } catch (error) {
                console.error("Error loading statistics:", error);
                toast.error("Ошибка загрузки статистики");
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            loadStatistics();
        });

        const resetStatistics = async () => {
            if (!confirm("Вы уверены, что хотите сбросить всю статистику кейсов? Это действие необратимо.")) {
                return;
            }
            
            loading.value = true;
            try {
                const response = await fetch(
                    `${import.meta.env.VITE_API_URL}/api/admin/cases/statistics/reset`,
                    {
                        method: "POST",
                        headers: {
                            Authorization: "Bearer " + Cookies.get("token"),
                        },
                    }
                );

                const data = await response.json();

                if (data.success) {
                    toast.success(data.message || "Статистика сброшена");
                    loadStatistics();
                } else {
                    toast.error(data.message || "Ошибка сброса статистики");
                }
            } catch (error) {
                toast.error("Ошибка сброса статистики");
            } finally {
                loading.value = false;
            }
        };

        return {
            statistics,
            loading,
            loadStatistics,
            resetStatistics,
            getImageUrl,
        };
    },
};
</script>

