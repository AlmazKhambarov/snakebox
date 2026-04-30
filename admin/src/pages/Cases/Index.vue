<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Кейсы">
      <button
        type="button"
        class="btn btn-sm fw-bold btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#add_case"
      >
        Добавить кейс
      </button>
      <button
        type="button"
        class="btn btn-sm fw-bold btn-primary"
        hidden=""
        data-bs-toggle="modal"
        data-bs-target="#edit_case"
      >
        Редактировать кейс
      </button>
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
              id="cases"
            >
              <thead class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                <!--begin::Table row-->
                <tr>
                  <th>ID</th>
                  <th>Категория</th>
                  <th>Название</th>
                  <th>Ссылка</th>
                  <th>Картинка</th>
                  <th>Цена</th>
                  <!-- <th>Скидка</th> -->
                  <!-- <th>Кол-во открытий</th> -->
                  <!-- <th>Минимальный депозит</th> -->
                  <th>Активный</th>
                  <th>Виден пользователям</th>
                  <!-- <th>Максимальное кол-во открытий</th> -->
                  <!-- <th>Остаток</th> -->
                  <th>Тип</th>
                  <th>Игра</th>
                  <th>Профит</th>
                  <th>Саунд Пак</th>
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

  <div class="modal fade" tabindex="-1" id="add_case">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Добавить кейс</h3>
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
                <input
                  v-model="newCase.name"
                  type="text"
                  class="form-control"
                  placeholder="Test Case"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Ссылка</label>
              <div class="input-group mb-5">
                <input
                  v-model="newCase.url"
                  type="text"
                  class="form-control"
                  placeholder="test-case"
                />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Категория</label>
              <div class="input-group mb-5">
                <select
                  class="form-select"
                  id="select_new_category"
                  data-dropdown-parent="#add_case"
                >
                  <option></option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Цена</label>
              <div class="input-group mb-5">
                <input
                  v-model="newCase.price"
                  type="number"
                  class="form-control"
                  placeholder="5"
                />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Активный</label>
              <div class="input-group mb-5">
                <select v-model="newCase.is_active" class="form-control">
                  <option value="1">Да</option>
                  <option value="0">Нет</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Показывать</label>
              <div class="input-group mb-5">
                <select v-model="newCase.is_visible" class="form-control">
                  <option value="1">Да</option>
                  <option value="0">Нет</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Тип</label>
              <div class="input-group mb-5">
                <select v-model="newCase.type" class="form-control">
                  <option value="default">Default</option>
                  <!-- <option value="free">Free</option> -->
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Игра</label>
              <div class="input-group mb-5">
                <select v-model="newCase.game" class="form-control">
                  <option value="cs">CS</option>
                  <option value="pubg">PUBG</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Sound Pack</label>
              <div class="input-group mb-5">
                <select v-model="newCase.sound_pack" class="form-control">
                  <option value="default">Default</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row" v-if="newCase.type === 'free'">
            <div class="col-lg-12">
              <label class="form-label">Минимальный депозит</label>
              <div class="input-group mb-5">
                <input
                  v-model="newCase.min_dep"
                  type="number"
                  class="form-control"
                  placeholder="5"
                />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12 d-flex flex-column">
              <label class="form-label">Картинка кейса</label>
              <button type="button" @click="triggerNewImage" class="btn btn-primary">
                {{ newCase.image === null ? "Выберите картинку" : "Картинка выбрана" }}
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
          <button @click="createBox" type="submit" class="btn btn-primary">
            Добавить
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" tabindex="-1" id="edit_case">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Редактировать кейс</h3>
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
                <input
                  v-model="editCase.name"
                  type="text"
                  class="form-control"
                  placeholder="Снуп дог"
                />
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Ссылка</label>
              <div class="input-group mb-5">
                <input
                  v-model="editCase.url"
                  type="text"
                  class="form-control"
                  placeholder="snoopdogg"
                />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Категория</label>
              <div class="input-group mb-5">
                <select
                  class="form-select"
                  id="select_edit_category"
                  data-dropdown-parent="#edit_case"
                >
                  <option></option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Цена</label>
              <div class="input-group mb-5">
                <input
                  v-model="editCase.price"
                  type="number"
                  class="form-control"
                  placeholder=""
                />
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Активный</label>
              <div class="input-group mb-5">
                <select v-model="editCase.is_active" class="form-control">
                  <option value="1">Да</option>
                  <option value="0">Нет</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Показывать</label>
              <div class="input-group mb-5">
                <select v-model="editCase.is_visible" class="form-control">
                  <option value="1">Да</option>
                  <option value="0">Нет</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Тип</label>
              <div class="input-group mb-5">
                <select v-model="editCase.type" class="form-control">
                  <option value="default">Default</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label class="form-label">Игра</label>
              <div class="input-group mb-5">
                <select v-model="editCase.game" class="form-control">
                  <option value="cs">CS</option>
                  <option value="pubg">PUBG</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-lg-6">
              <label class="form-label">Sound Pack</label>
              <div class="input-group mb-5">
                <select v-model="editCase.sound_pack" class="form-control">
                  <option value="default">Default</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group row" v-if="editCase.type == 'free'">
            <div class="col-lg-12">
              <label class="form-label">Мин деп</label>
              <div class="input-group mb-5">
                <input
                  v-model="newCase.min_dep"
                  type="number"
                  class="form-control"
                  placeholder="5"
                />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-12 d-flex flex-column">
              <label class="form-label">Картинка кейса</label>
              <button type="button" @click="triggerEditImage" class="btn btn-primary">
                {{ editCase.image === null ? "Выберите картинку" : "Картинка выбрана" }}
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
          <button @click="saveBox" type="submit" class="btn btn-primary">
            Сохранить
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
import { useRouter } from "vue-router";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      router: useRouter(),
      editImageInput: null,
      editNewInput: null,
      newCase: {
        category_id: 0,
        name: "",
        url: "",
        image: null,
        price: 0,
        min_dep: 0,
        is_active: 1,
        is_visible: 1,
        type: "default",
        game: "cs",
        sound_pack: "default",
      },
      editCase: {
        category_id: 0,
        name: "",
        url: "",
        image: null,
        price: 0,
        is_active: 1,
        is_visible: 1,
        type: "default",
        game: "cs",
        sound_pack: "default",
      },
      handlersAttached: false,
    };
  },
  mounted() {
    this.loadData();
    this.initSelect2();
  },
  methods: {
    async loadData() {
      let datatable;
      let table = $("#cases");
      table.DataTable().clear().destroy();

      datatable = $(table).DataTable({
        searchDelay: 500,
        processing: true,
        serverSide: true,
        ajax: {
          url: `${import.meta.env.VITE_API_URL}/api/admin/cases`,
          type: "GET",
          headers: {
            Authorization: "Bearer " + Cookies.get("token"),
          },
        },
        columns: [
          { data: "id" },
          { data: "category.name" },
          { data: "name" },
          { data: "url" },
          {
            data: "image",
            render: (data, type, row) =>
              `<img src="${data}" alt="image" width="50" height="50">`,
          },
          {
            data: "price",
            render: (data) => `${(parseFloat(data) / 100).toFixed(2)} ₽`,
          },
          {
            data: "is_active",
            render: (data) =>
              data
                ? '<span class="badge badge-success">Да</span>'
                : '<span class="badge badge-danger">Нет</span>',
          },
          {
            data: "is_visible",
            render: (data) =>
              data
                ? '<span class="badge badge-success">Да</span>'
                : '<span class="badge badge-danger">Нет</span>',
          },
          {
            data: "type",
          },
          {
            data: "game",
            render: (data) => {
              if (data === 'pubg') {
                return '<div class="d-flex align-items-center"><img src="/images/icons/pubglogo.png" width="20" height="20" style="object-fit: contain;"></div>';
              }
              return '<div class="d-flex align-items-center"><img src="/images/icons/cs2.svg" width="20" height="20" class="me-2" style="filter: brightness(0); opacity: 0.7;"><span class="badge badge-info">CS</span></div>';
            },
          },
          {
            data: "profit",
            render: (data) => `${(parseFloat(data) / 100).toFixed(2)}`,
          },
          {
            data: "sound_pack",
          },

          { data: null },
        ],
        columnDefs: [
          {
            targets: -1,
            orderable: false,
            className: "actions",
            render: (data, type, row) => `
              <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Редактировать"> <i class="ki-duotone ki-mouse-square fs-1"><span class="path1"></span><span class="path2"></span></i></a>
              <a href="#" class="btn btn-icon text-hover-primary" data-action="items" data-id="${row.id}" title="Предметы"> <i class="ki-duotone ki-lots-shopping fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></a>
              <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Удалить"> <i class="ki-duotone ki-trash-square fs-1"><span class="path1"></span><span class="path2"></span></i></a>
            `,
          },
        ],
      });

      if (!this.handlersAttached) {
        datatable.on("click", 'td.actions a[data-action="edit"]', (e) => {
          e.preventDefault();
          this.getBox(e.currentTarget.dataset.id);
        });

        datatable.on("click", 'td.actions a[data-action="delete"]', (e) => {
          e.preventDefault();
          this.deleteBox(e.currentTarget.dataset.id);
        });

        datatable.on("click", 'td.actions a[data-action="items"]', (e) => {
          e.preventDefault();
          this.goToItems(e.currentTarget.dataset.id);
        });

        this.handlersAttached = true;
      }

      const handleSearch = () => {
        const search = document.querySelector("[data-search-input]");
        search.addEventListener("keyup", (e) => {
          datatable.search(e.target.value).draw();
        });
      };

      handleSearch();
    },

    createBox() {
      const newCategoryId = $("#select_new_category").find(":selected").val();
      const newItemId = $("#select_new_item").find(":selected").val();

      const formData = new FormData();
      for (let key in this.newCase) {
        if (this.newCase[key] !== null && this.newCase[key] !== undefined) {
          formData.append(key, this.newCase[key]);
        }
      }
      formData.append("category_id", newCategoryId);

      request("POST", "/api/admin/cases/create", formData).then(({ data }) => {
        if (!data.success) return toast.error(data.message);
        this.loadData();

        $('div[data-bs-dismiss="modal"]').click();
        toast.success(data.message);
      });
    },

    getBox(id) {
      request("GET", "/api/admin/cases/case", { id }).then(({ data }) => {
        if (!data.success) return toast.error(data.message);

        this.editCase = { ...data.case, image: null };


        const category = {
          id: data.case.category_id,
          text: data.case.category.name,
        };


        const categoryOption = new Option(category.text, category.id, true, true);

        $("#select_edit_category").append(categoryOption).trigger("change");

        $('button[data-bs-target="#edit_case"]').click();
      });
    },

    saveBox() {
      const newCategoryId = $("#select_edit_category").find(":selected").val();
      let newItemId = $("#select_edit_item").find(":selected").val();

      if (!newItemId || newItemId === "null" || newItemId === "0") {
        newItemId = null;
      }

      const formData = new FormData();
      for (let key in this.editCase) {
        if (this.editCase[key] !== null && this.editCase[key] !== undefined) {
          formData.append(key, this.editCase[key]);
        }
      }
      formData.append("category_id", newCategoryId);
      formData.append("item_id", newItemId);

      request("POST", "/api/admin/cases/save", formData).then(({ data }) => {
        if (!data.success) return toast.error(data.message);
        toast.success(data.message);
        this.loadData();
        $('div[data-bs-dismiss="modal"]').click();
      });
    },

    deleteBox(id) {
      request("POST", "/api/admin/cases/delete", { id }).then(({ data }) => {
        if (!data.success) return toast.error(data.message);
        toast.success(data.message);
        this.loadData();
      });
    },

    goToItems(id) {
      this.router.push({ name: "items", params: { id } });
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
      this.editCase.image = event.target.files[0];
    },

    newImage(event) {
      this.newCase.image = event.target.files[0];
    },

    initSelect2() {
      const token = Cookies.get("token");
      const categoryOptions = {
        placeholder: "Выберите категорию",
        ajax: {
          delay: 250,
          type: "GET",
          url: `${import.meta.env.VITE_API_URL}/api/admin/cases/categories`,
          headers: { Authorization: "Bearer " + token },
          data: (params) => ({
            search: params.term,
            page: params.page || 1,
          }),
          processResults: (data) => ({
            results: data.results,
            pagination: { more: data.more },
          }),
        },
      };

      $("#select_new_category").select2(categoryOptions);
      $("#select_edit_category").select2(categoryOptions);

      const itemOptions = {
        placeholder: "Выберите предмет",
        ajax: {
          delay: 250,
          type: "GET",
          url: `${import.meta.env.VITE_API_URL}/api/admin/cases/items/list`,
          headers: { Authorization: "Bearer " + token },
          data: (params) => ({
            search: params.term,
            page: params.page || 1,
          }),
          processResults: (data) => ({
            results: [{ id: 0, text: "Без предмета" }, ...(data.results || [])],
            pagination: { more: data.more },
          }),
        },
        allowClear: true,
        minimumResultsForSearch: 0,
      };

      $("#select_new_item").select2(itemOptions);
      $("#select_edit_item").select2(itemOptions);
    },
  },
};
</script>
