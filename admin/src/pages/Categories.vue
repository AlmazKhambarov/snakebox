<template>
    <div class="d-flex flex-column flex-column-fluid">
        <Toolbar title="Categories">
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#add_category"
            >
                Add Category
            </button>
            <button
                type="button"
                class="btn btn-sm fw-bold btn-primary"
                hidden=""
                data-bs-toggle="modal"
                data-bs-target="#edit_category"
            >
                Edit Category
            </button>
        </Toolbar>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div
                id="kt_app_content_container"
                class="app-container container-fluid"
            >
                <div class="card card-flush">
                    <div
                        class="card-header align-items-center py-5 gap-2 gap-md-5"
                    >
                        <div class="card-title">
                            <!--begin::Search-->
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
                                    placeholder="Search"
                                />
                            </div>
                            <!--end::Search-->
                        </div>
                    </div>
                    <div class="card-body">
                        <table
                            class="table align-middle rounded table-row-dashed fs-6 g-5"
                            id="categories"
                        >
                            <thead
                                class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0"
                            >
                                <!--begin::Table row-->
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Type</th>
                                    <th>Hidden</th>
                                    <th>Actions</th>
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

    <div class="modal fade" tabindex="-1" id="add_category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add Category</h3>
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
                        <div class="col-lg-12">
                            <label class="form-label">Name</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newCategory.name"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Position</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newCategory.position"
                                    type="number"
                                    class="form-control"
                                    placeholder="5"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Type</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="newCategory.type"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Hidden</label>
                            <div class="input-group mb-5">
                                <select
                                    v-model="newCategory.is_active"
                                    class="form-control"
                                >
                                    <option :value="0">No</option>
                                    <option :value="1">Yes</option>
                                </select>
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
                        Close
                    </button>
                    <button
                        @click="createCategory"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="edit_category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Category</h3>
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
                        <div class="col-lg-12">
                            <label class="form-label">Name</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editCategory.name"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Position</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editCategory.position"
                                    type="number"
                                    class="form-control"
                                    placeholder="5"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Type</label>
                            <div class="input-group mb-5">
                                <input
                                    v-model="editCategory.type"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="form-label">Hidden</label>
                            <div class="input-group mb-5">
                                <select
                                    v-model="editCategory.is_active"
                                    class="form-control"
                                >
                                    <option :value="0">Yes</option>
                                    <option :value="1">No</option>
                                </select>
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
                        Close
                    </button>
                    <button
                        @click="saveCategory"
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save
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
            newCategory: {
                name: "",
                type: "",
                position: 0,
                is_active: 1,
            },
            editCategory: {
                id: 0,
                name: "",
                type: "",
                position: 0,
                is_active: 0,
            },
            handlersAttached: false,
            datatable: null,
        };
    },
    methods: {
        async loadData() {
            const table = $("#categories");

            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            this.datatable = table.DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${import.meta.env.VITE_API_URL}/api/admin/categories`,
                    type: "GET",
                    headers: {
                        Authorization: "Bearer " + Cookies.get("token"),
                    },
                },
                columns: [
                    { data: "id" },
                    { data: "name" },
                    { data: "position" },
                    { data: "type" },
                    {
                        data: "is_active",
                        render: (data) => {
                            return data == 1 ? "No" : "Yes";
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
                            return `
                <a href="#" class="btn btn-icon text-hover-primary" data-action="edit" data-id="${row.id}" title="Edit">
                  <i class="ki-duotone ki-mouse-square fs-1"><span class="path1"></span><span class="path2"></span></i>
                </a>
                <a href="#" class="btn btn-icon text-hover-danger" data-action="delete" data-id="${row.id}" title="Delete">
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
                        this.getCategory(id);
                    }
                );

                this.datatable.on(
                    "click",
                    'td.actions a[data-action="delete"]',
                    (e) => {
                        e.preventDefault();
                        const id = e.currentTarget.dataset.id;
                        this.deleteCategory(id);
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

        createCategory() {
            request("POST", "/api/admin/categories/create", {
                name: this.newCategory.name,
                position: this.newCategory.position,
                type: this.newCategory.type,
                is_active: this.newCategory.is_active,
            }).then(({ data }) => {
                if (data.success) {
                    toast.success(data.message);
                    this.loadData();
                    $('div[data-bs-dismiss="modal"]').click();
                    this.newCategory.name = "";
                    this.newCategory.position = 0;
                    this.newCategory.is_active = 0;
                    this.newCategory.type = "";
                } else {
                    toast.error(data.message);
                }
            });
        },

        getCategory(id) {
            request("GET", "/api/admin/categories/get", { id }).then(
                ({ data }) => {
                    if (data.success) {
                        this.editCategory = data.category;
                        $('button[data-bs-target="#edit_category"]').click();
                    } else {
                        toast.error(data.message);
                    }
                }
            );
        },

        saveCategory() {
            request(
                "POST",
                "/api/admin/categories/save",
                this.editCategory
            ).then(({ data }) => {
                if (data.success) {
                    $('div[data-bs-dismiss="modal"]').click();
                    this.loadData();
                    toast.success(data.message);
                } else {
                    toast.error(data.message);
                }
            });
        },

        deleteCategory(id) {
            request("POST", "/api/admin/categories/delete", { id }).then(
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
