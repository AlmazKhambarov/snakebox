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
          Загрузить предметы / Обновить цены
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
                  <th>Цена до обновления</th>
                  <!--                                <th >Действия</th>-->
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
          <div class="form-group">
            <div class="col-lg-12 d-flex flex-column">
              <label class="form-label">Картинка кейса</label>
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
      editImageInput: null,
      editNewInput: null,
      newItem: {
        name: "",
        icon_url: null,
        price: 0,
      },
      editItem: {
        name: "",
        icon_url: null,
        price: 0,
      },
      market_hash_name: "",
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
            data: "steam_price_before",
            render: function (data, type, row) {
              return row.steam_price_before / 100 + " ₽";
            },
          },
          // { data: null },
        ],
        columnDefs: [
          // {
          //     targets: -1,
          //     orderable: false,
          //     className: 'actions',
          //     render: function (data, type, row) {
          //         return `
          //             <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить">
          //                 <i class="ki-duotone ki-trash-square fs-1">
          //                     <span class="path1"></span>
          //                     <span class="path2"></span>
          //                     <span class="path3"></span>
          //                     <span class="path4"></span>
          //                 </i>
          //             </a>`;
          //     },
          // },
        ],
      });

      if (!this.handlersAttached) {
        // datatable.on("click", 'td.actions a[data-action="edit"]', (e) => {
        //     e.preventDefault();
        //     const id = e.currentTarget.dataset.id;
        //     this.editItem(id);
        // });

        // datatable.on("click", 'td.actions a[data-action="delete"]', (e) => {
        //     e.preventDefault();
        //     const id = e.currentTarget.dataset.id;
        //     this.deleteItem(id);
        // });

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
        console.log("Отправляемые данные:", key, this.newItem[key]);
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
          this.newItem.icon_url = "";
          this.newItem.price = 0;

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
