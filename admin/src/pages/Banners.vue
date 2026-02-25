<template>
  <div class="d-flex flex-column flex-column-fluid">
    <Toolbar title="Баннеры">
      <button
        type="button"
        class="btn btn-sm fw-bold btn-primary"
        @click="openCreateModal"
      >
        Добавить баннер
      </button>
    </Toolbar>

    <div id="kt_app_content" class="app-content flex-column-fluid">
      <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush">
          <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
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
                  v-model="searchQuery"
                  class="form-control form-control-solid w-250px ps-14"
                  placeholder="Поиск"
                />
              </div>
            </div>
          </div>
          <div class="card-body">
            <table
              class="table align-middle rounded table-row-dashed fs-6 g-5"
              id="banners"
            >
              <thead class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                <tr>
                  <th>ID</th>
                  <th>Изображение</th>
                  <th>Название</th>
                  <th>Текст</th>
                  <th>Ссылка</th>
                  <th>Позиция</th>
                  <th>Активен</th>
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

  <!-- Modal Create/Edit -->
  <div class="modal fade" tabindex="-1" id="banner_modal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">{{ editingBanner ? 'Редактировать баннер' : 'Добавить баннер' }}</h3>
          <div
            class="btn btn-icon btn-sm btn-active-light-primary ms-2"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <i class="ki-duotone ki-cross fs-1">
              <span class="path1"></span><span class="path2"></span>
            </i>
          </div>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveBanner">
            <div class="form-group mb-5">
              <label class="form-label">Название</label>
              <input v-model="formData.title" type="text" class="form-control" />
            </div>
            <div class="form-group mb-5">
              <label class="form-label">Текст</label>
              <textarea v-model="formData.text" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group mb-5">
              <label class="form-label">Изображение</label>
              <input
                type="file"
                ref="imageInput"
                @change="handleImageChange"
                accept="image/*"
                class="form-control"
                :required="!editingBanner"
              />
              <div v-if="formData.imagePreview" class="mt-3">
                <img :src="formData.imagePreview" alt="Preview" style="max-width: 200px; max-height: 150px;" />
              </div>
              <div v-else-if="editingBanner && currentBanner?.image" class="mt-3">
                <img :src="getImageUrl(currentBanner.image)" alt="Current" style="max-width: 200px; max-height: 150px;" />
              </div>
            </div>
            <div class="form-group mb-5">
              <label class="form-label">Ссылка</label>
              <input v-model="formData.link" type="text" class="form-control" />
            </div>
            <div class="form-group mb-5">
              <label class="form-label">Текст кнопки</label>
              <input v-model="formData.button_text" type="text" class="form-control" />
            </div>
            <div class="form-group mb-5">
              <label class="form-label">Позиция</label>
              <input v-model.number="formData.position" type="number" class="form-control" min="0" />
            </div>
            <div class="form-group mb-5">
              <div class="form-check form-switch">
                <input
                  v-model="formData.is_active"
                  class="form-check-input"
                  type="checkbox"
                  id="is_active"
                />
                <label class="form-check-label" for="is_active">Активен</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отмена</button>
              <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { request } from "@/utils/request.js";
import Cookies from "js-cookie";
import Toolbar from "../components/pages/Toolbar.vue";

export default {
  components: {
    Toolbar,
  },
  data() {
    return {
      searchQuery: "",
      editingBanner: null,
      currentBanner: null,
      formData: {
        title: "",
        text: "",
        image: null,
        imagePreview: null,
        link: "",
        button_text: "",
        position: 0,
        is_active: true,
      },
      table: null,
      handlersAttached: false,
    };
  },
  mounted() {
    this.initTable();
  },
  methods: {
    initTable() {
      const self = this;
      const frontendUrl = import.meta.env.VITE_APP_FRONTEND_URL || "";
      const datatable = $("#banners").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
          url: `${import.meta.env.VITE_API_URL}/api/admin/banners`,
          headers: {
            Authorization: "Bearer " + Cookies.get("token"),
          },
        },
        columns: [
          { data: "id" },
          {
            data: "image",
            render: function (data) {
              if (!data) return "Нет";
              const imageUrl = data.startsWith("http://") || data.startsWith("https://") || data.startsWith("data:")
                ? data
                : `${frontendUrl}${data}`;
              return `<img src="${imageUrl}" style="max-width: 100px; max-height: 60px; object-fit: contain;" />`;
            },
          },
          { data: "title" },
          {
            data: "text",
            render: function (data) {
              return data ? (data.length > 50 ? data.substring(0, 50) + "..." : data) : "—";
            },
          },
          {
            data: "link",
            render: function (data) {
              return data || "—";
            },
          },
          { data: "position" },
          {
            data: "is_active",
            render: function (data) {
              return data ? "Да" : "Нет";
            },
          },
          {
            data: null,
            orderable: false,
            render: (data, type, row) => {
              return `
                <button class="btn btn-sm btn-light-primary me-2" onclick="window.bannerComponent.editBanner(${row.id})">
                  Редактировать
                </button>
                <button class="btn btn-sm btn-light-danger" onclick="window.bannerComponent.deleteBanner(${row.id})">
                  Удалить
                </button>
              `;
            },
          },
        ],
      });

      this.table = datatable;
      window.bannerComponent = this;

      if (!this.handlersAttached) {
        datatable.on("click", 'td button[onclick*="editBanner"]', (e) => {
          e.preventDefault();
          const match = e.currentTarget.getAttribute("onclick").match(/editBanner\((\d+)\)/);
          if (match) {
            this.editBanner(parseInt(match[1]));
          }
        });

        datatable.on("click", 'td button[onclick*="deleteBanner"]', (e) => {
          e.preventDefault();
          const match = e.currentTarget.getAttribute("onclick").match(/deleteBanner\((\d+)\)/);
          if (match) {
            this.deleteBanner(parseInt(match[1]));
          }
        });

        this.handlersAttached = true;
      }
    },
    openCreateModal() {
      this.editingBanner = null;
      this.currentBanner = null;
      this.formData = {
        title: "",
        text: "",
        image: null,
        imagePreview: null,
        link: "",
        button_text: "",
        position: 0,
        is_active: true,
      };
      if (this.$refs.imageInput) {
        this.$refs.imageInput.value = "";
      }
      const modal = new bootstrap.Modal(document.getElementById("banner_modal"));
      modal.show();
    },
    async editBanner(id) {
      try {
        const { data } = await request("GET", `/api/admin/banners/get?id=${id}`);
        if (data.success) {
          this.editingBanner = id;
          this.currentBanner = data.banner;
          this.formData = {
            title: data.banner.title || "",
            text: data.banner.text || "",
            image: null,
            imagePreview: null,
            link: data.banner.link || "",
            button_text: data.banner.button_text || "",
            position: data.banner.position || 0,
            is_active: data.banner.is_active ?? true,
          };
          const modal = new bootstrap.Modal(document.getElementById("banner_modal"));
          modal.show();
        }
      } catch (error) {
        console.error("Error loading banner:", error);
        alert("Ошибка загрузки баннера");
      }
    },
    handleImageChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
          this.formData.imagePreview = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },
    async saveBanner() {
      try {
        const formData = new FormData();
        formData.append("title", this.formData.title);
        formData.append("text", this.formData.text);
        formData.append("link", this.formData.link);
        formData.append("button_text", this.formData.button_text);
        formData.append("position", this.formData.position);
        formData.append("is_active", this.formData.is_active ? 1 : 0);

        if (this.formData.image) {
          formData.append("image", this.formData.image);
        }

        if (this.editingBanner) {
          formData.append("id", this.editingBanner);
          await request("POST", "/api/admin/banners/update", formData, {
            "content-type": "multipart/form-data",
          });
        } else {
          await request("POST", "/api/admin/banners/create", formData, {
            "content-type": "multipart/form-data",
          });
        }

        this.table.ajax.reload();
        bootstrap.Modal.getInstance(document.getElementById("banner_modal")).hide();
        toastr.success(this.editingBanner ? "Баннер обновлен" : "Баннер создан");
      } catch (error) {
        console.error("Error saving banner:", error);
        toastr.error("Ошибка сохранения баннера");
      }
    },
    async deleteBanner(id) {
      if (!confirm("Вы уверены, что хотите удалить этот баннер?")) {
        return;
      }

      try {
        await request("POST", "/api/admin/banners/delete", { id });
        this.table.ajax.reload();
        toastr.success("Баннер удален");
      } catch (error) {
        console.error("Error deleting banner:", error);
        toastr.error("Ошибка удаления баннера");
      }
    },
    getImageUrl(imagePath) {
      if (!imagePath) return "";
      // Если это уже полный URL или data URL, возвращаем как есть
      if (imagePath.startsWith("http://") || imagePath.startsWith("https://") || imagePath.startsWith("data:")) {
        return imagePath;
      }
      // Иначе добавляем базовый URL
      return `${import.meta.env.VITE_APP_FRONTEND_URL || ""}${imagePath}`;
    },
  },
};
</script>

