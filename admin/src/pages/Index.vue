<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Главная" />

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">

          <div class="d-flex flex-column gap-5">

              <div class="row g-5 g-xl-5">
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.payments.paymentsToday }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма пополнений за день</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.payments.paymentsWeek }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма пополнений за неделю</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.payments.paymentsMonth }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма пополнений за месяц</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.payments.paymentsTotal }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма пополнений всего</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
              </div>

              <div class="row g-5 g-xl-5">
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.withdraws.withdrawsToday / 100 }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма выводов за день</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.withdraws.withdrawsWeek / 100 }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма выводов за неделю</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.withdraws.withdrawsMonth / 100 }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма выводов за месяц</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.withdraws.withdrawsTotal / 100 }} ₽</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Сумма выводов всего</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
              </div>

              <div class="row g-5 g-xl-5">
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.users.usersToday }}</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Регистраций за день</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.users.usersWeek }}</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Регистраций за неделю</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.users.usersMonth }}</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Регистраций за месяц</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
                  <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3">
                      <div class="card card-flush ">
                          <!--begin::Header-->
                          <div class="card-header pt-5">
                              <!--begin::Title-->
                              <div class="card-title d-flex flex-column">
                                  <!--begin::Info-->
                                  <div class="d-flex align-items-center">
                                      <!--begin::Currency-->
                                      <!--end::Currency-->
                                      <!--begin::Amount-->
                                      <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ stats.users.usersTotal }}</span>
                                      <!--end::Amount-->
                                      <!--begin::Badge-->
                                      <!--                                  <span class="badge badge-light-success fs-base">-->
                                      <!--                                    <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1">-->
                                      <!--                                        <span class="path1"></span>-->
                                      <!--                                        <span class="path2"></span>-->
                                      <!--                                    </i>2.2%-->
                                      <!--                                  </span>-->
                                      <!--end::Badge-->
                                  </div>
                                  <!--end::Info-->
                                  <!--begin::Subtitle-->
                                  <span class="text-gray-500 pt-1 fw-semibold fs-6">Всего регистраций</span>
                                  <!--end::Subtitle-->
                              </div>
                              <!--end::Title-->
                          </div>
                          <!--end::Header-->
                      </div>
                  </div>
              </div>

              <!-- Автоотключенные кейсы -->
              <div v-if="stats.auto_disabled_cases && stats.auto_disabled_cases.length > 0" class="row g-5 g-xl-5">
                  <div class="col-12">
                      <div class="card card-flush border-danger">
                          <div class="card-header bg-danger">
                              <div class="card-title text-white">
                                  <i class="ki-duotone ki-information-5 fs-2 text-white me-2">
                                      <span class="path1"></span>
                                      <span class="path2"></span>
                                      <span class="path3"></span>
                                  </i>
                                  Кейсы отключены автоматически (высокий RTP)
                              </div>
                          </div>
                          <div class="card-body">
                              <div class="alert alert-warning mb-4">
                                  <strong>Внимание!</strong> Эти кейсы были автоматически отключены из-за слишком высокой окупаемости.
                                  Проверьте настройки RTP или сбросьте статистику.
                              </div>
                              <div class="table-responsive">
                                  <table class="table table-row-bordered table-hover">
                                      <thead>
                                          <tr class="fw-bold fs-6 text-gray-800">
                                              <th>Кейс</th>
                                              <th>Текущий RTP</th>
                                              <th>Макс RTP</th>
                                              <th>Открыто раз</th>
                                              <th>Причина</th>
                                              <th>Отключен</th>
                                              <th>Действия</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <tr v-for="caseItem in stats.auto_disabled_cases" :key="caseItem.id">
                                              <td>
                                                  <div class="d-flex align-items-center">
                                                      <img :src="caseItem.image" class="me-3" style="width: 50px; height: 40px; object-fit: contain;" />
                                                      <span class="fw-bold">{{ caseItem.name }}</span>
                                                  </div>
                                              </td>
                                              <td>
                                                  <span 
                                                      class="badge fs-6"
                                                      :class="caseItem.current_rtp >= 95 ? 'badge-success' : (caseItem.current_rtp >= 90 ? 'badge-warning' : 'badge-danger')"
                                                  >{{ caseItem.current_rtp }}%</span>
                                              </td>
                                              <td>
                                                  <span class="badge badge-light-warning fs-6">{{ caseItem.max_rtp }}%</span>
                                              </td>
                                              <td>{{ caseItem.total_opened }}</td>
                                              <td class="text-muted">{{ caseItem.auto_disabled_reason }}</td>
                                              <td>{{ caseItem.auto_disabled_at }}</td>
                                              <td>
                                                  <button
                                                      @click="enableCase(caseItem.id)"
                                                      class="btn btn-sm btn-success"
                                                  >
                                                      <i class="ki-duotone ki-check-circle fs-2">
                                                          <span class="path1"></span>
                                                          <span class="path2"></span>
                                                      </i>
                                                      Включить
                                                  </button>
                                                  <router-link
                                                      :to="{ name: 'items', params: { id: caseItem.id } }"
                                                      class="btn btn-sm btn-primary ms-2"
                                                  >
                                                      <i class="ki-duotone ki-setting-2 fs-2">
                                                          <span class="path1"></span>
                                                          <span class="path2"></span>
                                                      </i>
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

              <div class="row g-5 g-xl-5">
                  <div class="col-md-12">
                      <div class="d-flex gap-3">
                          <div class="card flex-fill">
                              <div class="card-header">
                                  <div class="card-title">Пополнения за неделю</div>
                              </div>
                              <div class="card-body">
                                  <div id="kt_payment_week" style="height: 300px;"></div>
                              </div>
                          </div>
                          <div class="card flex-fill">
                              <div class="card-header">
                                  <div class="card-title">Выводы за неделю</div>
                              </div>
                              <div class="card-body">
                                  <div id="kt_withdraw_week" style="height: 300px;"></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="row g-5 g-xl-5">
                  <div class="col-md-12">
                      <div class="card">
                          <div class="card-header">
                              <div class="card-title">Регистрации за неделю</div>
                          </div>
                          <div class="card-body">
                              <div id="kt_users_week" style="height: 300px;"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>


      </div>
    </div>
  </div>
</template>
<script setup>
import Toolbar from "../components/pages/Toolbar.vue";
import {onMounted, ref} from "vue";
import {request} from "@/utils/request.js";

const stats = ref({
    payments: {
        paymentsToday: 0,
        paymentsWeek: 0,
        paymentsMonth: 0,
        paymentsTotal: 0
    },
    withdraws: {
        withdrawsToday: 0,
        withdrawsWeek: 0,
        withdrawsMonth: 0,
        withdrawsTotal: 0
    },
    users: {
        usersToday: 0,
        usersWeek: 0,
        usersMonth: 0,
        usersTotal: 0
    },
    week_payments: [],
    week_withdraws: [],
    week_users: [],
    auto_disabled_cases: []
})

const getStats = () => {
    request('GET', '/api/admin/index')
        .then(({data}) => {
            stats.value = data
            loadCharts()
        })
}

const createChart = (elementId, data, name, color, formatter, height = 300) => {
    const element = document.getElementById(elementId);

    const options = {
        series: [{
            name: name,
            data: data
        }],
        chart: {
            fontFamily: 'inherit',
            type: 'area',
            height: height,
            toolbar: {
                show: false
            }
        },
        plotOptions: {},
        legend: {
            show: false
        },
        dataLabels: {
            enabled: false
        },
        fill: {
            type: 'solid',
            opacity: 1
        },
        stroke: {
            curve: 'smooth',
            show: true,
            width: 3,
            colors: [color]
        },
        xaxis: {
            categories: ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#63626f',
                    fontSize: '12px'
                }
            },
            crosshairs: {
                position: 'front',
                stroke: {
                    color: color,
                    width: 1,
                    dashArray: 3
                }
            },
            tooltip: {
                enabled: true,
                formatter: undefined,
                offsetY: 0,
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#63626f',
                    fontSize: '12px'
                }
            }
        },
        states: {
            normal: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            hover: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: 'none',
                    value: 0
                }
            }
        },
        tooltip: {
            style: {
                fontSize: '12px'
            },
            y: {
                formatter: formatter
            }
        },
        colors: [color],
        grid: {
            borderColor: '#63626f',
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        markers: {
            strokeColor: '#63626f',
            strokeWidth: 3
        }
    };

    const chart = new ApexCharts(element, options);
    chart.render();
}

const loadCharts = () => {
    // График пополнений
    createChart('kt_payment_week', stats.value.week_payments, 'Пополнений на', '#883fff',
        function (val) { return val + '₽' });

    // График выводов
    createChart('kt_withdraw_week', stats.value.week_withdraws, 'Выводов на', '#ff6b6b',
        function (val) { return (val / 100) + '₽' });

    // График регистраций
    createChart('kt_users_week', stats.value.week_users, 'Регистраций', '#51cf66',
        function (val) { return val + ' чел.' });
}

const enableCase = async (caseId) => {
    if (!confirm('Включить этот кейс? Убедитесь, что настройки RTP корректны.')) {
        return;
    }

    try {
        const { data } = await request('POST', '/api/admin/cases/enable', { id: caseId });
        if (data.success) {
            // Обновляем статистику
            getStats();
            // Показываем успешное сообщение (можно добавить toast)
            alert(data.message);
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('Ошибка при включении кейса');
    }
};

onMounted(() => {
    getStats();
})
</script>
