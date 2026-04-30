<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Предметы сайта">
      <div class="d-flex gap-2">
        <button
          type="button"
          class="btn btn-sm fw-bold btn-primary"
          data-bs-toggle="modal"
          data-bs-target="#add_item"
        >
          Добавить предмет
        </button>
        <button
          type="button"
          class="btn btn-sm fw-bold btn-primary"
          hidden=""
          data-bs-toggle="modal"
          data-bs-target="#edit_item"
        >
          Редактировать предмет
        </button>
      </div>
    </Toolbar>

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush">
          <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
              <!--begin::Search-->
              <div class="d-flex align-items-center position-relative my-1">
                <span class="svg-icon fs-1 position-absolute ms-4">
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
            <div class="d-flex align-items-center gap-2">
              <select v-model="gameFilter" @change="loadData" class="form-select form-select-solid w-150px">
                <option value="">Все игры</option>
                <option value="cs">CS</option>
                <option value="pubg">PUBG</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <table
              class="table align-middle rounded table-row-dashed fs-6 g-5"
              id="itemsList"
            >
              <thead class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                <!--begin::Table row-->
                <tr>
                  <th>ID</th>
                  <th>Название</th>
                  <th>Изображение</th>
                  <th>Цена</th>
                  <th>Игра</th>
                  <th>Редкость</th>
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

  <!-- Add Item Modal -->
  <div class="modal fade" tabindex="-1" id="add_item">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Добавить предмет</h3>
          <div
            class="btn btn-icon btn-sm btn-active-light-primary ms-2"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <i class="ki-duotone ki-cross fs-1"
              ><span class="path1"></span><span class="path2"></span
            ></i>
          </div>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Название</label>
              <div class="input-group mb-5">
                <input v-model="newItem.name" type="text" class="form-control" />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Цена</label>
              <div class="input-group mb-5">
                <input v-model="newItem.price" type="text" class="form-control" />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Игра</label>
              <div class="input-group mb-5">
                <select v-model="newItem.game" class="form-control">
                  <option value="cs">CS</option>
                  <option value="pubg">PUBG</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Редкость</label>
              <div class="input-group mb-5">
                <select v-model="newItem.rarity" class="form-control">
                  <option value="common">Common</option>
                  <option value="uncommon">Uncommon</option>
                  <option value="rare">Rare</option>
                  <option value="mythical">Mythical</option>
                  <option value="legendary">Legendary</option>
                  <option value="ancient">Ancient</option>
                  <option value="immortal">Immortal</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12 d-flex flex-column">
              <label class="form-label">Картинка предмета</label>
              <button type="button" @click="triggerNewImage" class="btn btn-primary">
                {{ newItem.icon_url === null ? "Выберите картинку" : "Картинка выбрана" }}
              </button>
              <input
                ref="editNewInput"
                hidden
                type="file"
                accept="image/*"
                @change="newImage($event)"
              />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Закрыть
          </button>
          <button @click="addItem" type="submit" class="btn btn-primary">Добавить</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Item Modal -->
  <div class="modal fade" tabindex="-1" id="edit_item">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Редактировать предмет</h3>
          <div
            class="btn btn-icon btn-sm btn-active-light-primary ms-2"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <i class="ki-duotone ki-cross fs-1"
              ><span class="path1"></span><span class="path2"></span
            ></i>
          </div>
        </div>
        <div class="modal-body">
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Название</label>
              <div class="input-group mb-5">
                <input v-model="editItem.name" type="text" class="form-control" />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Цена</label>
              <div class="input-group mb-5">
                <input v-model="editItem.price" type="text" class="form-control" />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Игра</label>
              <div class="input-group mb-5">
                <select v-model="editItem.game" class="form-control">
                  <option value="cs">CS</option>
                  <option value="pubg">PUBG</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Редкость</label>
              <div class="input-group mb-5">
                <select v-model="editItem.rarity" class="form-control">
                  <option value="common">Common</option>
                  <option value="uncommon">Uncommon</option>
                  <option value="rare">Rare</option>
                  <option value="mythical">Mythical</option>
                  <option value="legendary">Legendary</option>
                  <option value="ancient">Ancient</option>
                  <option value="immortal">Immortal</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12 d-flex flex-column">
              <label class="form-label">Картинка предмета (необязательно)</label>
              <button type="button" @click="triggerEditImage" class="btn btn-primary">
                {{ editItem.icon_url === null ? "Выберите картинку" : "Картинка выбрана" }}
              </button>
              <input
                ref="editImageInput"
                hidden
                type="file"
                accept="image/*"
                @change="editImage($event)"
              />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Закрыть
          </button>
          <button @click="updateItem" type="submit" class="btn btn-primary">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Toolbar from "../components/pages/Toolbar.vue";
import { request } from "@/utils/request.js";
import { toast } from "vue3-toastify";
import Cookies from "js-cookie";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      editImageInput: null,
      editNewInput: null,
      gameFilter: "",
      newItem: {
        name: "",
        icon_url: null,
        price: 0,
        game: "cs",
        rarity: "common",
      },
      editItem: {
        id: null,
        name: "",
        icon_url: null,
        price: 0,
        game: "cs",
        rarity: "common",
      },
      handlersAttached: false,
    };
  },
  methods: {
    async loadData() {
      let table = $("#itemsList");
      table.DataTable().clear().destroy();

      const datatable = $(table).DataTable({
        searchDelay: 500,
        processing: true,
        serverSide: true,
          ajax: {
            url: `${import.meta.env.VITE_API_URL}/api/admin/items`,
            type: "GET",
            headers: {
              Authorization: "Bearer " + Cookies.get("token"),
            },
            data: (d) => {
              d.game = this.gameFilter;
            }
          },
        columns: [
          { data: "id" },
          { data: "title" },
          {
            data: "image",
            render: (data, type, row) =>
              `<img src="${data}" alt="image" width="50" height="50">`,
          },
          {
            data: "steam_price",
            render: function (data, type, row) {
              return row.steam_price / 100 + " ₽";
            },
          },
          {
            data: "game",
            render: function (data) {
              if (data === 'pubg') {
                return '<div class="d-flex align-items-center"><img src="/images/icons/pubglogo.png" width="20" height="20" style="object-fit: contain;"></div>';
              }
              return '<div class="d-flex align-items-center"><img src="/images/icons/cs2.svg" width="20" height="20" class="me-2" style="filter: brightness(0); opacity: 0.7;"><span class="badge badge-info">CS</span></div>';
            },
          },
          {
            data: "rarity",
            render: function (data) {
              return data || '-';
            },
          },
          { data: null },
        ],
        columnDefs: [
          {
            targets: -1,
            orderable: false,
            className: 'actions',
            render: function (data, type, row) {
              return `
                <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать">
                  <i class="ki-duotone ki-mouse-square fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                </a>
                <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить">
                  <i class="ki-duotone ki-trash-square fs-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                  </i>
                </a>`;
            },
          },
        ],
      });

      if (!this.handlersAttached) {
        datatable.on("click", 'td.actions a[data-action="edit"]', (e) => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          this.openEditModal(id, datatable);
        });

        datatable.on("click", 'td.actions a[data-action="delete"]', (e) => {
          e.preventDefault();
          const id = e.currentTarget.dataset.id;
          this.deleteItem(id);
        });

        this.handlersAttached = true;
      }

      this.handleSearch(datatable);
    },

    handleSearch(datatable) {
      const search = document.querySelector("[data-search-input]");
      search.addEventListener("keyup", (e) => {
        datatable.search(e.target.value).draw();
      });
    },

    addItem() {
      const formData = new FormData();
      for (let key in this.newItem) {
        formData.append(key, this.newItem[key]);
      }

      request("POST", "/api/admin/items/create", formData, {
        headers: {
          "content-type": "multipart/form-data",
        },
      }).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
          $('div[data-bs-dismiss="modal"]').click();
          this.newItem.name = "";
          this.newItem.icon_url = null;
          this.newItem.price = 0;
          this.newItem.game = "cs";
          this.newItem.rarity = "common";

          this.loadData();
        } else {
          toast.error(data.message);
        }
      });
    },

    openEditModal(id, datatable) {
      // Get the row data from DataTable
      const rowData = datatable.rows().data().toArray().find(r => r.id == id);
      if (rowData) {
        this.editItem = {
          id: rowData.id,
          name: rowData.title,
          price: rowData.steam_price,
          game: rowData.game || 'cs',
          rarity: rowData.rarity || 'common',
          icon_url: null,
        };
        $('button[data-bs-target="#edit_item"]').click();
      }
    },

    updateItem() {
      const formData = new FormData();
      formData.append("id", this.editItem.id);
      formData.append("name", this.editItem.name);
      formData.append("price", this.editItem.price);
      formData.append("game", this.editItem.game);
      formData.append("rarity", this.editItem.rarity);
      if (this.editItem.icon_url) {
        formData.append("icon_url", this.editItem.icon_url);
      }

      request("POST", "/api/admin/items/update", formData, {
        headers: {
          "content-type": "multipart/form-data",
        },
      }).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
          $('div[data-bs-dismiss="modal"]').click();
          this.loadData();
        } else {
          toast.error(data.message);
        }
      });
    },

    deleteItem(id) {
      if (!confirm("Вы уверены, что хотите удалить этот предмет?")) return;

      request("POST", "/api/admin/items/delete", { id }).then(({ data }) => {
        if (data.success) {
          toast.success(data.message);
          this.loadData();
        } else {
          toast.error(data.message);
        }
      });
    },

    triggerNewImage() {
      if (this.$refs.editNewInput) {
        this.$refs.editNewInput.click();
      } else {
        console.warn("Ref editNewInput не найден");
      }
    },

    triggerEditImage() {
      if (this.$refs.editImageInput) {
        this.$refs.editImageInput.click();
      } else {
        console.warn("Ref editImageInput не найден");
      }
    },

    editImage(event) {
      this.editItem.icon_url = event.target.files[0];
    },

    newImage(event) {
      this.newItem.icon_url = event.target.files[0];
    },
  },
  mounted() {
    this.loadData();
  },
};
</script>
