<template>
  <div class="d-flex flex-column flex-column-fluid">
    <div v-if="isLoaded" id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card mb-5 mb-xl-10">
          <div class="card-body pt-9 pb-0">
            <div class="d-flex flex-wrap flex-sm-nowrap">
              <div class="me-7 mb-4">
                <div
                  class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative"
                >
                  <img :src="user.avatar" alt="image" />
                </div>
              </div>
              <div class="flex-grow-1">
                <div
                  class="d-flex justify-content-between align-items-start flex-wrap mb-2"
                >
                  <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-2">
                      <a
                        href="#"
                        class="text-gray-900 text-hover-primary fs-2 fw-bold me-1"
                        >{{ user.username }}</a
                      >
                      <a href="#" v-if="user.role == 'admin'">
                        <i class="ki-duotone ki-verify fs-1 text-primary">
                          <span class="path1"></span>
                          <span class="path2"></span>
                        </i>
                      </a>
                    </div>
                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                      <a
                        :href="`https://steamcommunity.com/profiles/${user.steam_id}`"
                        target="_blank"
                        class="d-flex align-items-center text-gray-500 text-hover-primary gap-2 mb-2"
                      >
                        <i class="bi bi-steam"></i>
                        {{ user.steam_id }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <ul
              class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold"
            >
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5 active"
                  data-bs-toggle="tab"
                  href="#main"
                  >Настройки</a
                >
              </li>
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5"
                  data-bs-toggle="tab"
                  href="#statistics"
                  >Статистика</a
                >
              </li>
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5"
                  data-bs-toggle="tab"
                  href="#inventory"
                  >Инвентарь</a
                >
              </li>
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5"
                  data-bs-toggle="tab"
                  href="#sessions"
                  >Сессии</a
                >
              </li>
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5"
                  data-bs-toggle="tab"
                  href="#activity"
                  >Активность</a
                >
              </li>
              <li class="nav-item mt-2">
                <a
                  class="nav-link text-active-primary ms-0 me-10 py-5"
                  data-bs-toggle="tab"
                  href="#referral"
                  >Реферальная система</a
                >
              </li>
            </ul>
          </div>
        </div>

        <div class="card mb-5 mb-xl-10">
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="main" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">Настройки пользователя</h3>
                </div>
              </div>

              <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form">
                  <div class="card-body border-top p-9">
                    <div class="row mb-6">
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2">Никнейм</label>
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="myName"
                          :value="user.username"
                          disabled
                        />
                      </div>
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Трейд ссылка</label
                        >
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="https://steamcommunity.com/tradeoffer/new/?partner=...."
                          :value="user.trade_url"
                        />
                      </div>

                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2">Роль</label>
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="Роль"
                          :value="user.role"
                        />
                      </div>
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2">Баланс</label>
                        <input
                          type="number"
                          name="phone"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="100"
                          v-model="balanceInput"
                        />
                      </div>
                    </div>
                    <div class="row mb-6">
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Социальная сеть</label
                        >
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="myName"
                          :value="user.social"
                          disabled
                        />
                      </div>
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Ивент монеты</label
                        >
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="last ip"
                          :value="eventInput"
                        />
                      </div>
                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Регистрационный IP</label
                        >
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="reg ip"
                          :value="user.reg_ip"
                        />
                      </div>

                      <div class="col-lg-3 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Последний IP</label
                        >
                        <input
                          type="text"
                          name="company"
                          class="form-control form-control-lg form-control-solid"
                          placeholder="last ip"
                          :value="user.last_ip"
                        />
                      </div>
                    </div>
                  </div>
                </form>
                <!--end::Form-->
              </div>
            </div>

            <div class="tab-pane fade" id="statistics" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">Статистика пользователя</h3>
                </div>
              </div>

              <div class="card-body border-top p-9" v-if="statistics">
              
                <!-- Депозиты -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Депозиты</h4>
                  <div class="row g-5">
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Всего депозитов</div>
                          <div class="fs-3 fw-bold">{{ statistics.deposits.count }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Средний депозит</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.deposits.avg * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Максимальный</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.deposits.max * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Минимальный</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.deposits.min * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-primary">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Сегодня</div>
                          <div class="fs-3 fw-bold text-primary">{{ formatMoney(statistics.deposits.today * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-info">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">За неделю</div>
                          <div class="fs-3 fw-bold text-info">{{ formatMoney(statistics.deposits.week * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-success">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">За месяц</div>
                          <div class="fs-3 fw-bold text-success">{{ formatMoney(statistics.deposits.month * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-warning">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Ожидают</div>
                          <div class="fs-3 fw-bold text-warning">{{ formatMoney(statistics.deposits.pending * 100) }} ₽</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-5" v-if="statistics.deposits.first_deposit || statistics.deposits.last_deposit">
                    <div class="col-lg-6" v-if="statistics.deposits.first_deposit">
                      <div class="d-flex align-items-center">
                        <i class="ki-duotone ki-calendar fs-2 text-primary me-3">
                          <span class="path1"></span>
                          <span class="path2"></span>
                        </i>
                        <div>
                          <div class="fs-6 fw-semibold text-gray-600">Первый депозит</div>
                          <div class="fs-5 fw-bold">{{ formatMoney(statistics.deposits.first_deposit.amount * 100) }} ₽</div>
                          <div class="fs-7 text-gray-500">{{ statistics.deposits.first_deposit.date }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6" v-if="statistics.deposits.last_deposit">
                      <div class="d-flex align-items-center">
                        <i class="ki-duotone ki-calendar-tick fs-2 text-success me-3">
                          <span class="path1"></span>
                          <span class="path2"></span>
                        </i>
                        <div>
                          <div class="fs-6 fw-semibold text-gray-600">Последний депозит</div>
                          <div class="fs-5 fw-bold">{{ formatMoney(statistics.deposits.last_deposit.amount * 100) }} ₽</div>
                          <div class="fs-7 text-gray-500">{{ statistics.deposits.last_deposit.date }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Выводы -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Выводы</h4>
                  <div class="row g-5">
                    <div class="col-lg-3">
                      <div class="card bg-light-success">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Всего выведено</div>
                          <div class="fs-2 fw-bold text-success">{{ formatMoney(statistics.withdraws.total) }} ₽</div>
                          <div class="fs-7 text-gray-500">{{ statistics.withdraws.count }} выводов</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Средний вывод</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.withdraws.avg) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Максимальный</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.withdraws.max) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-info">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">По статусам</div>
                          <div class="fs-7">
                            <div>Выведено: {{ formatMoney(statistics.withdraws.by_status.withdrawn) }} ₽</div>
                            <div>Отправляется: {{ formatMoney(statistics.withdraws.by_status.sending) }} ₽</div>
                            <div>Ожидает: {{ formatMoney(statistics.withdraws.by_status.wait) }} ₽</div>
                            <div>Готов: {{ formatMoney(statistics.withdraws.by_status.order_ready) }} ₽</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Инвентарь -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Инвентарь</h4>
                  <div class="row g-5">
                    <div class="col-lg-3">
                      <div class="card bg-light-primary">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Предметов в инвентаре</div>
                          <div class="fs-2 fw-bold text-primary">{{ statistics.inventory.count }}</div>
                          <div class="fs-7 text-gray-500">На сумму {{ formatMoney(statistics.inventory.value) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Средняя цена</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.inventory.avg_price) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Максимальная</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.inventory.max_price) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Минимальная</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.inventory.min_price) }} ₽</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Продажи -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Продажи</h4>
                  <div class="row g-5">
                    <div class="col-lg-6">
                      <div class="card bg-light-warning">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Продано предметов</div>
                          <div class="fs-2 fw-bold text-warning">{{ statistics.sales.count }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="card bg-light-success">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Сумма продаж</div>
                          <div class="fs-2 fw-bold text-success">{{ formatMoney(statistics.sales.amount) }} ₽</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Кейсы -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Кейсы</h4>
                  <div class="row g-5">
                    <div class="col-lg-3">
                      <div class="card bg-light-primary">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Открыто кейсов</div>
                          <div class="fs-2 fw-bold text-primary">{{ statistics.cases.opened_count }}</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-danger">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Потрачено</div>
                          <div class="fs-2 fw-bold text-danger">{{ formatMoney(statistics.cases.spent) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-success">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Выиграно</div>
                          <div class="fs-2 fw-bold text-success">{{ formatMoney(statistics.cases.won) }} ₽</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card" :class="statistics.cases.profit >= 0 ? 'bg-light-success' : 'bg-light-danger'">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Профит/Убыток</div>
                          <div class="fs-2 fw-bold" :class="statistics.cases.profit >= 0 ? 'text-success' : 'text-danger'">
                            {{ formatMoney(statistics.cases.profit) }} ₽
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light-info">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Примерный RTP</div>
                          <div class="fs-2 fw-bold text-info">{{ statistics.cases.rtp }}%</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3">
                      <div class="card bg-light">
                        <div class="card-body">
                          <div class="fs-6 fw-semibold text-gray-600 mb-2">Средний чек</div>
                          <div class="fs-3 fw-bold">{{ formatMoney(statistics.cases.avg_check) }} ₽</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="mt-5" v-if="statistics.cases.by_box && statistics.cases.by_box.length > 0">
                    <h5 class="fw-bold mb-3">Статистика по кейсам</h5>
                    <div class="table-responsive">
                      <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                          <tr class="fw-bold text-muted">
                            <th>Кейс</th>
                            <th>Открыто</th>
                            <th>Потрачено</th>
                            <th>Выиграно</th>
                            <th>Профит/Убыток</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="box in statistics.cases.by_box" :key="box.box_id">
                            <td>{{ box.box_name }}</td>
                            <td>{{ box.opened }}</td>
                            <td>{{ formatMoney(box.spent) }} ₽</td>
                            <td>{{ formatMoney(box.won) }} ₽</td>
                            <td :class="box.profit >= 0 ? 'text-success' : 'text-danger'">
                              {{ formatMoney(box.profit) }} ₽
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="tab-pane fade" id="inventory" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">Инвентарь пользователя</h3>
                </div>
                <div class="card-toolbar">
                  <div class="d-flex gap-2">
                    <select
                      v-model="inventoryFilter"
                      @change="loadInventory"
                      class="form-select form-select-sm w-auto"
                    >
                      <option value="">Все статусы</option>
                      <option value="STOCK">В инвентаре (STOCK)</option>
                      <option value="SELL">Продано (SELL)</option>
                      <option value="SENDING">Отправляется (SENDING)</option>
                      <option value="WAIT">Ожидает (WAIT)</option>
                      <option value="ORDER_READY">Готов (ORDER_READY)</option>
                      <option value="WITHDRAWN">Выведено (WITHDRAWN)</option>
                    </select>
                    <button
                      @click="sellAllItems"
                      class="btn btn-sm btn-warning"
                      :disabled="inventoryLoading"
                    >
                      Продать все (STOCK)
                    </button>
                    <button
                      @click="deleteAllItems"
                      class="btn btn-sm btn-danger"
                      :disabled="inventoryLoading"
                    >
                      Удалить все
                    </button>
                    <button
                      @click="loadInventory"
                      class="btn btn-sm btn-primary"
                      :disabled="inventoryLoading"
                    >
                      <i class="ki-duotone ki-arrows-circle fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                      Обновить
                    </button>
                  </div>
                </div>
              </div>

              <div class="card-body border-top p-9">
                <div v-if="inventoryLoading" class="text-center py-10">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                  </div>
                </div>

                <div v-else-if="inventoryItems && inventoryItems.length === 0" class="text-center py-10">
                  <div class="text-muted">Инвентарь пуст</div>
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                      <tr class="fw-bold text-muted">
                        <th class="min-w-50px">ID</th>
                        <th class="min-w-100px">Изображение</th>
                        <th class="min-w-200px">Предмет</th>
                        <th class="min-w-100px">Цена</th>
                        <th class="min-w-100px">Статус</th>
                        <th class="min-w-150px">Дата</th>
                        <th class="min-w-200px text-end">Действия</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="item in inventoryItems" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>
                          <img
                            :src="item.item?.image || '/images/no-image.png'"
                            alt="item"
                            class="w-50px h-50px rounded"
                            style="object-fit: cover"
                          />
                        </td>
                        <td>
                          <div class="fw-bold text-gray-800">
                            {{ item.item?.title || 'Неизвестно' }}
                          </div>
                          <div class="text-muted fs-7" v-if="item.box">
                            Из кейса: {{ item.box?.name }}
                          </div>
                        </td>
                        <td class="fw-bold">{{ formatMoney(item.price) }} ₽</td>
                        <td>
                          <select
                            :value="item.status"
                            @change="changeItemStatus(item.id, $event.target.value)"
                            class="form-select form-select-sm"
                            :class="getStatusClass(item.status)"
                          >
                            <option value="STOCK">STOCK</option>
                            <option value="SELL">SELL</option>
                            <option value="SENDING">SENDING</option>
                            <option value="WAIT">WAIT</option>
                            <option value="ORDER_READY">ORDER_READY</option>
                            <option value="TRADE_LOCK">TRADE_LOCK</option>
                            <option value="WITHDRAWN">WITHDRAWN</option>
                          </select>
                        </td>
                        <td class="text-muted fs-7">
                          {{ formatDate(item.created_at) }}
                        </td>
                        <td class="text-end">
                          <button
                            @click="sellItem(item.id)"
                            class="btn btn-sm btn-warning me-2"
                            :disabled="item.status === 'SELL'"
                            title="Продать (без начисления баланса)"
                          >
                            <i class="ki-duotone ki-dollar fs-4">
                              <span class="path1"></span>
                              <span class="path2"></span>
                            </i>
                          </button>
                          <button
                            @click="deleteItem(item.id)"
                            class="btn btn-sm btn-danger"
                            title="Удалить"
                          >
                            <i class="ki-duotone ki-trash fs-4">
                              <span class="path1"></span>
                              <span class="path2"></span>
                              <span class="path3"></span>
                              <span class="path4"></span>
                            </i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                  <!-- Пагинация -->
                  <div class="d-flex justify-content-between align-items-center mt-5" v-if="inventoryPagination">
                    <div class="text-muted">
                      Показано {{ inventoryItems.length }} из {{ inventoryPagination.total }} предметов
                    </div>
                    <div class="d-flex gap-2">
                      <button
                        @click="loadInventory(inventoryPagination.current_page - 1)"
                        class="btn btn-sm btn-light"
                        :disabled="inventoryPagination.current_page === 1"
                      >
                        Назад
                      </button>
                      <span class="d-flex align-items-center px-3">
                        Страница {{ inventoryPagination.current_page }} из {{ inventoryPagination.last_page }}
                      </span>
                      <button
                        @click="loadInventory(inventoryPagination.current_page + 1)"
                        class="btn btn-sm btn-light"
                        :disabled="inventoryPagination.current_page === inventoryPagination.last_page"
                      >
                        Вперед
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="sessions" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">Активные сессии и IP адреса</h3>
                </div>
                <div class="card-toolbar">
                  <button
                    @click="revokeAllSessions"
                    class="btn btn-sm btn-danger"
                    :disabled="sessionsLoading"
                  >
                    Завершить все сессии
                  </button>
                  <button
                    @click="loadSessions"
                    class="btn btn-sm btn-primary ms-2"
                    :disabled="sessionsLoading"
                  >
                    <i class="ki-duotone ki-arrows-circle fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                    Обновить
                  </button>
                </div>
              </div>

              <div class="card-body border-top p-9">
                <!-- Активные сессии -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">Активные сессии</h4>
                  <div v-if="sessionsLoading" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Загрузка...</span>
                    </div>
                  </div>
                  <div v-else-if="sessions && sessions.length === 0" class="text-center py-10">
                    <div class="text-muted">Нет активных сессий</div>
                  </div>
                  <div v-else class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                      <thead>
                        <tr class="fw-bold text-muted">
                          <th>Название</th>
                          <th>Создана</th>
                          <th>Последнее использование</th>
                          <th>Истекает</th>
                          <th class="text-end">Действия</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="session in sessions" :key="session.id">
                          <td>
                            <div class="fw-bold">{{ session.name }}</div>
                            <div class="text-muted fs-7" v-if="session.is_current">Текущая сессия</div>
                          </td>
                          <td>{{ formatDate(session.created_at) }}</td>
                          <td>{{ session.last_used_at ? formatDate(session.last_used_at) : 'Никогда' }}</td>
                          <td>{{ session.expires_at ? formatDate(session.expires_at) : 'Никогда' }}</td>
                          <td class="text-end">
                            <button
                              @click="revokeSession(session.id)"
                              class="btn btn-sm btn-danger"
                              title="Завершить сессию"
                            >
                              <i class="ki-duotone ki-cross fs-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                              </i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- История IP адресов -->
                <div class="mb-10">
                  <h4 class="fw-bold mb-5">История IP адресов</h4>
                  <div v-if="ipHistoryLoading" class="text-center py-10">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Загрузка...</span>
                    </div>
                  </div>
                  <div v-else>
                    <div class="row mb-5">
                      <div class="col-lg-6">
                        <div class="card bg-light-primary">
                          <div class="card-body">
                            <div class="fs-6 fw-semibold text-gray-600 mb-2">Текущий IP</div>
                            <div class="fs-3 fw-bold text-primary">{{ ipHistoryData.current_ip || 'N/A' }}</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="card bg-light-info">
                          <div class="card-body">
                            <div class="fs-6 fw-semibold text-gray-600 mb-2">IP при регистрации</div>
                            <div class="fs-3 fw-bold text-info">{{ ipHistoryData.registration_ip || 'N/A' }}</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="ipHistory && ipHistory.length === 0" class="text-center py-10">
                      <div class="text-muted">История IP адресов пуста</div>
                    </div>
                    <div v-else>
                      <div class="mb-3 text-muted fs-7" v-if="ipHistoryData.total_records">
                        Всего записей: {{ ipHistoryData.total_records }}
                      </div>
                      <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                          <thead>
                            <tr class="fw-bold text-muted">
                              <th>IP адрес</th>
                              <th>Тип</th>
                              <th>Описание</th>
                              <th>Дата</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr v-for="(ip, index) in ipHistory" :key="index">
                              <td class="fw-bold">{{ ip.ip }}</td>
                              <td>
                                <span class="badge" :class="getIpTypeClass(ip.type)">
                                  {{ getIpTypeText(ip.type) }}
                                </span>
                              </td>
                              <td>{{ ip.description }}</td>
                              <td>{{ formatDate(ip.date) }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="activity" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">История активности</h3>
                </div>
                <div class="card-toolbar">
                  <button
                    @click="loadActivityHistory"
                    class="btn btn-sm btn-primary"
                    :disabled="activityLoading"
                  >
                    <i class="ki-duotone ki-arrows-circle fs-2">
                      <span class="path1"></span>
                      <span class="path2"></span>
                    </i>
                    Обновить
                  </button>
                </div>
              </div>

              <div class="card-body border-top p-9">
                <div v-if="activityLoading" class="text-center py-10">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                  </div>
                </div>
                <div v-else-if="activityHistory && activityHistory.length === 0" class="text-center py-10">
                  <div class="text-muted">История активности пуста</div>
                </div>
                <div v-else class="table-responsive">
                  <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                      <tr class="fw-bold text-muted">
                        <th>Тип</th>
                        <th>Описание</th>
                        <th>Предмет</th>
                        <th>Сумма</th>
                        <th>Дата</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(activity, index) in activityHistory" :key="index">
                        <td>
                          <span class="badge" :class="getActivityTypeClass(activity.type)">
                            {{ getActivityTypeText(activity.type) }}
                          </span>
                        </td>
                        <td>{{ activity.description }}</td>
                        <td>{{ activity.item || '-' }}</td>
                        <td class="fw-bold" v-if="activity.price">
                          {{ formatMoney(activity.price) }} ₽
                        </td>
                        <td v-else>-</td>
                        <td class="text-muted fs-7">{{ formatDate(activity.date) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="referral" role="tabpanel">
              <div class="card-header border-0">
                <div class="card-title m-0">
                  <h3 class="fw-bold m-0">Реферальна система</h3>
                </div>
              </div>

              <div id="kt_account_settings_social_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form">
                  <div class="card-body border-top p-9">
                    <div class="row">
                      <div class="col-lg-4 mb-5 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Реферальный код</label
                        >
                        <input
                          type="text"
                          name="fname"
                          class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                          v-model="user.referral_code"
                        />
                      </div>
                      <div class="col-lg-4 mb-5 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Реферальный уровень</label
                        >
                        <input
                          type="number"
                          name="fname"
                          class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                          v-model="user.referral_level"
                          disabled
                        />
                      </div>
                      <div class="col-lg-4 mb-5 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Реферальный баланс</label
                        >
                        <input
                          type="number"
                          name="fname"
                          class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                          v-model="user.referral_balance"
                        />
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6 mb-5 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Приглашено пользователей</label
                        >
                        <input
                          type="text"
                          name="fname"
                          class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                          disabled
                          :value="user.referrals_count"
                        />
                      </div>
                      <div class="col-lg-6 mb-5 fv-row">
                        <label class="fs-6 fw-semibold form-label mb-2"
                          >Заработано на рефералах</label
                        >
                        <input
                          type="text"
                          name="fname"
                          class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                          disabled
                          :value="totalEarned"
                        />
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="card-footer d-flex justify-content-between py-6 px-9">
            <div>
              <button 
                v-if="!user.is_banned" 
                @click="banUser" 
                type="button" 
                class="btn btn-danger me-2"
              >
                Заблокировать пользователя
              </button>
              <button 
                v-else 
                @click="unbanUser" 
                type="button" 
                class="btn btn-success me-2"
              >
                Разблокировать пользователя
              </button>
              <span v-if="user.is_banned" class="badge badge-danger fs-6">
                Пользователь заблокирован
              </span>
            </div>
            <button @click="save" type="button" class="btn btn-primary">Сохранить</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { useRoute } from "vue-router";
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";
import { useSettingsStore } from "../../stores/settings.store.js";

export default {
  data() {
    return {
      isLoaded: false,
      user: {},
      statistics: null,
      route: useRoute(),
      settingsStore: useSettingsStore(),
      balance: 0,
      inventoryItems: [],
      inventoryLoading: false,
      inventoryFilter: '',
      inventoryPagination: null,
      sessions: [],
      sessionsLoading: false,
      ipHistory: [],
      ipHistoryLoading: false,
      ipHistoryData: {},
      activityHistory: [],
      activityLoading: false,
    };
  },
  mounted() {
    this.getUser();
  },
  watch: {
    user: {
      handler(newUser) {
        if (newUser && newUser.id) {
          // Загружаем инвентарь при загрузке пользователя
          this.loadInventory();
          this.loadSessions();
          this.loadIpHistory();
          this.loadActivityHistory();
        }
      },
      immediate: true,
    },
  },
  computed: {
    balanceInput: {
      get() {
        return this.user.balance / 100;
      },
      set(value) {
        this.user.balance = Math.round(value * 100);
      },
    },
    eventInput: {
      get() {
        return this.user.event_points / 100;
      },
      set(value) {
        this.user.event_points = Math.round(value * 100);
      },
    },
    totalEarned: {
      get() {
        return this.user.total_earned / 100;
      },
      set(value) {
        this.user.total_earned = Math.round(value * 100);
      },
    },
  },
  methods: {
    getUser() {
      this.settingsStore.startLoading();
      request("GET", "/api/admin/users/user", {
        id: this.route.params.id,
      })
        .then(({ data }) => {
          this.user = { ...data.user };
          this.statistics = data.statistics || null;
        })
        .finally(() => {
          this.isLoaded = true;
        });
    },

    formatMoney(amount) {
      if (!amount) return '0.00';
      return (amount / 100).toFixed(2);
    },

    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleString('ru-RU');
    },

    getStatusClass(status) {
      const classes = {
        'STOCK': 'border-primary',
        'SELL': 'border-warning',
        'SENDING': 'border-info',
        'WAIT': 'border-secondary',
        'ORDER_READY': 'border-success',
        'TRADE_LOCK': 'border-danger',
        'WITHDRAWN': 'border-dark',
      };
      return classes[status] || '';
    },

    async loadInventory(page = 1) {
      if (!this.user.id) return;
      
      this.inventoryLoading = true;
      try {
        const params = {
          user_id: this.user.id,
          page: page,
        };
        if (this.inventoryFilter) {
          params.status = this.inventoryFilter;
        }

        const { data } = await request("GET", "/api/admin/users/inventory", params);
        if (data.success) {
          this.inventoryItems = data.items.data || [];
          this.inventoryPagination = {
            current_page: data.items.current_page,
            last_page: data.items.last_page,
            total: data.items.total,
          };
        }
      } catch (error) {
        console.error("Ошибка при загрузке инвентаря:", error);
        toast.error("Ошибка при загрузке инвентаря");
      } finally {
        this.inventoryLoading = false;
      }
    },

    async sellItem(itemId) {
      if (!confirm("Вы уверены, что хотите продать этот предмет? Баланс пользователю НЕ будет начислен.")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/inventory/sell", {
          item_id: itemId,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при продаже предмета:", error);
        toast.error("Ошибка при продаже предмета");
      }
    },

    async deleteItem(itemId) {
      if (!confirm("Вы уверены, что хотите удалить этот предмет? Это действие нельзя отменить.")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/inventory/delete", {
          item_id: itemId,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при удалении предмета:", error);
        toast.error("Ошибка при удалении предмета");
      }
    },

    async sellAllItems() {
      if (!confirm("Вы уверены, что хотите продать ВСЕ предметы со статусом STOCK? Баланс пользователю НЕ будет начислен.")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/inventory/sell-all", {
          user_id: this.user.id,
          status: 'STOCK',
        });
        if (data.success) {
          toast.success(data.message);
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при продаже всех предметов:", error);
        toast.error("Ошибка при продаже всех предметов");
      }
    },

    async deleteAllItems() {
      if (!confirm("Вы уверены, что хотите удалить ВСЕ предметы? Это действие нельзя отменить.")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/inventory/delete-all", {
          user_id: this.user.id,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при удалении всех предметов:", error);
        toast.error("Ошибка при удалении всех предметов");
      }
    },

    async changeItemStatus(itemId, newStatus) {
      try {
        const { data } = await request("POST", "/api/admin/users/inventory/change-status", {
          item_id: itemId,
          status: newStatus,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        } else {
          toast.error(data.message);
          // Перезагружаем инвентарь, чтобы вернуть старое значение
          this.loadInventory(this.inventoryPagination?.current_page || 1);
        }
      } catch (error) {
        console.error("Ошибка при изменении статуса:", error);
        toast.error("Ошибка при изменении статуса");
        // Перезагружаем инвентарь, чтобы вернуть старое значение
        this.loadInventory(this.inventoryPagination?.current_page || 1);
      }
    },

    async loadSessions() {
      if (!this.user.id) return;
      
      this.sessionsLoading = true;
      try {
        const { data } = await request("GET", "/api/admin/users/sessions", {
          user_id: this.user.id,
        });
        if (data.success) {
          this.sessions = data.sessions || [];
        }
      } catch (error) {
        console.error("Ошибка при загрузке сессий:", error);
        toast.error("Ошибка при загрузке сессий");
      } finally {
        this.sessionsLoading = false;
      }
    },

    async revokeSession(tokenId) {
      if (!confirm("Вы уверены, что хотите завершить эту сессию?")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/sessions/revoke", {
          token_id: tokenId,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadSessions();
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при завершении сессии:", error);
        toast.error("Ошибка при завершении сессии");
      }
    },

    async revokeAllSessions() {
      if (!confirm("Вы уверены, что хотите завершить ВСЕ сессии пользователя?")) {
        return;
      }

      try {
        const { data } = await request("POST", "/api/admin/users/sessions/revoke-all", {
          user_id: this.user.id,
        });
        if (data.success) {
          toast.success(data.message);
          this.loadSessions();
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        console.error("Ошибка при завершении всех сессий:", error);
        toast.error("Ошибка при завершении всех сессий");
      }
    },

    async loadIpHistory() {
      if (!this.user.id) return;
      
      this.ipHistoryLoading = true;
      try {
        const { data } = await request("GET", "/api/admin/users/ip-history", {
          user_id: this.user.id,
        });
        if (data.success) {
          this.ipHistory = data.ip_history || [];
          this.ipHistoryData = {
            current_ip: data.current_ip,
            registration_ip: data.registration_ip,
            total_records: data.total_records || 0,
          };
        }
      } catch (error) {
        console.error("Ошибка при загрузке истории IP:", error);
        toast.error("Ошибка при загрузке истории IP");
      } finally {
        this.ipHistoryLoading = false;
      }
    },

    async loadActivityHistory() {
      if (!this.user.id) return;
      
      this.activityLoading = true;
      try {
        const { data } = await request("GET", "/api/admin/users/activity-history", {
          user_id: this.user.id,
          limit: 200,
        });
        if (data.success) {
          this.activityHistory = data.activity || [];
        }
      } catch (error) {
        console.error("Ошибка при загрузке истории активности:", error);
        toast.error("Ошибка при загрузке истории активности");
      } finally {
        this.activityLoading = false;
      }
    },

    getIpTypeClass(type) {
      const classes = {
        'registration': 'badge-primary',
        'last_login': 'badge-success',
        'session': 'badge-info',
        'payment': 'badge-warning',
      };
      return classes[type] || 'badge-secondary';
    },

    getIpTypeText(type) {
      const texts = {
        'registration': 'Регистрация',
        'last_login': 'Последний вход',
        'session': 'Сессия',
        'payment': 'Платеж',
      };
      return texts[type] || type;
    },

    getActivityTypeClass(type) {
      const classes = {
        'case_opened': 'badge-primary',
        'deposit': 'badge-success',
        'withdraw': 'badge-warning',
        'sale': 'badge-info',
        'referral_earning': 'badge-danger',
        'bonus': 'badge-secondary',
      };
      return classes[type] || 'badge-secondary';
    },

    getActivityTypeText(type) {
      const texts = {
        'case_opened': 'Открытие кейса',
        'deposit': 'Депозит',
        'withdraw': 'Вывод',
        'sale': 'Продажа',
        'referral_earning': 'Реферальное',
        'bonus': 'Бонус',
      };
      return texts[type] || type;
    },

    save() {
      request("POST", "/api/admin/users/save", this.user).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
        } else {
          toast.error(data.message);
        }
      });
    },

    async banUser() {
      if (!confirm("Вы уверены, что хотите заблокировать этого пользователя?")) {
        return;
      }
      try {
        const { data } = await request("POST", "/api/admin/users/ban", {
          user_id: this.user.id,
        });
        if (data.success) {
          toast.success(data.message);
          this.user.is_banned = true;
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        toast.error("Ошибка при блокировке пользователя");
      }
    },

    async unbanUser() {
      if (!confirm("Вы уверены, что хотите разблокировать этого пользователя?")) {
        return;
      }
      try {
        const { data } = await request("POST", "/api/admin/users/unban", {
          user_id: this.user.id,
        });
        if (data.success) {
          toast.success(data.message);
          this.user.is_banned = false;
        } else {
          toast.error(data.message);
        }
      } catch (error) {
        toast.error("Ошибка при разблокировке пользователя");
      }
    },
  },
};
</script>
