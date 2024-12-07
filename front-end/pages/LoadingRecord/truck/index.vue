<script setup>
import { ref, onMounted, watch } from "vue";
import { useCookie } from "nuxt/app";
import { AlertSuccess, loaderfun } from "~/composables/statFunc.js";

const columns = [
  { key: "plate_number", label: "پلاک کامیون", sortable: true },
  { key: "color", label: "رنگ", sortable: true },
  { key: "type", label: "نوع کامیون", sortable: true },
  { key: "weight", label: "وزن", sortable: true },
  { key: "actions", label: "عملیات" },
];

const items = (row) => [
  [
    {
      label: "ویرایش",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => editTruck(row),
    },
  ],
  [
    {
      label: "حذف",
      icon: "i-heroicons-trash-20-solid",
      click: () => initiateDelete(row.id),
    },
  ],
];

const trucks = ref([]);
const page = ref(1);
const pageCount = ref(0);
const total = ref(0);
const sort = ref({ column: "plate_number", direction: "asc" });
const q = ref('');
const status = ref(false);

// برای نگه‌داری وضعیت کامیونی که قرار است ویرایش شود
const selectedTruck = ref(null);

// برای نگه‌داری اطلاعات ویرایش شده کامیون
const newTruckData = ref({
  plate_number: "",
  color: "",
  type: "",
  weight: "",
});

// برای نگه‌داری وضعیت مودال تایید حذف
const showDeleteConfirmation = ref(false);
const truckToDelete = ref(null); // برای نگه‌داری کامیونی که باید حذف شود

const fetchTrucks = async () => {
  status.value = true;
  try {
    const response = await fetch(
        `${basUrl().value}/trucks?page=${page.value}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      const data = await response.json();
      trucks.value = data.data;
      pageCount.value = data.last_page;
      total.value = data.total;
    } else {
      console.error("Error fetching trucks:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    status.value = false;
  }
};

const editTruck = (truck) => {
  selectedTruck.value = truck;
  newTruckData.value = { ...truck }; // بارگذاری داده‌های کامیون برای ویرایش
};

const updateTruck = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/trucks/${selectedTruck.value.id}`,
        {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify(newTruckData.value),
        }
    );

    if (response.ok) {
      AlertSuccess('اطلاعات کامیون با موفقیت بروزرسانی شد')
      fetchTrucks();
      closeModal();
    } else {
      console.error("Error updating truck:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

// تابع برای شروع فرآیند حذف کامیون
const initiateDelete = (id) => {
  truckToDelete.value = id;
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};

// تابع برای تایید حذف کامیون
const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/trucks/${truckToDelete.value}`,
        {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      AlertSuccess("کامیون با موفقیت حذف شد");
      fetchTrucks();
      closeDeleteModal(); // بستن مودال تایید حذف
    } else {
      console.error("Error deleting truck:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

// تابع برای لغو عملیات حذف
const cancelDelete = () => {
  showDeleteConfirmation.value = false; // بستن مودال تایید حذف
  truckToDelete.value = null;
};

const closeModal = () => {
  selectedTruck.value = null;
  newTruckData.value = {
    plate_number: "",
    color: "",
    type: "",
    weight: "",
  };
};

const closeDeleteModal = () => {
  showDeleteConfirmation.value = false;
  truckToDelete.value = null;
};

// استفاده از واچر برای صفحه‌بندی
watch(page, fetchTrucks);

// استفاده از واچر برای مرتب‌سازی
watch(sort, fetchTrucks);

watch(q, fetchTrucks);

// اولین بار داده‌ها بارگذاری شود
onMounted(fetchTrucks);
</script>

<template>
  <div>
    <div class="p-4">
      <!-- مسیر صفحه -->
      <div class="breadcrumbs text-sm">
        <ul>
          <li>
            <nuxt-link to="/">
              <Icon name="ic:baseline-home" size="18" class="ml-2"/>
              خانه
            </nuxt-link>
          </li>
          <li>
            <a>
              <Icon name="ph:truck-trailer-light" class="ml-1" size="18"/>
              تردد
            </a>
          </li>
          <li>
            <a>
              <Icon name="healthicons:truck-driver" class="ml-1" size="18"/>
              کامیون‌ها
            </a>
          </li>
        </ul>
      </div>

      <!-- دکمه ایجاد کامیون جدید -->
      <div class="text-left">
        <NuxtLink to="/LoadingRecord/truck/create">
          <button class="btn btn-success">
            ایجاد کامیون جدید
            <Icon name="material-symbols-add-circle" size="18"/>
          </button>
        </NuxtLink>
      </div>

      <!-- جدول کامیون‌ها -->
      <div class="overflow-x-auto mt-4">
        <div class="flex px-3 py-3.5 border-b border-gray-200">
          <UInput
              v-model="q"
              placeholder="جستجو"
              :loading="status"
          />
        </div>
        <UTable
            :rows="trucks"
            :columns="columns"
            v-model:sort="sort"
            :loading="status"
            :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'در حال پردازش...' }"
            :progress="{ color: 'primary', animation: 'carousel' }"
            class="w-full"
            :ui="{
              tr: { base: 'hover:bg-gray-100' }
            }"
        >
          <template #actions-data="{ row }">
            <UDropdown :items="items(row)">
              <button class="btn btn-sm btn-primary">
                عملیات
                <Icon name="hugeicons:account-setting-01" size="18"/>
              </button>
            </UDropdown>
          </template>
        </UTable>
      </div>

      <!-- صفحه‌بندی -->
      <div class="flex justify-end px-3 py-3.5 border-t border-gray-200">
        <UPagination
            v-model="page"
            :page-count="pageCount"
            :total="total"
            :ui="{
            wrapper: 'flex items-center gap-1',
            rounded: '!rounded-full min-w-[32px] justify-center',
            default: { activeButton: { variant: 'outline' } }
          }"
            @change="fetchTrucks"
        />
      </div>
    </div>

    <!-- مودال ویرایش کامیون -->
    <div v-if="selectedTruck" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="closeModal">✕</button>
        <br>
        <h2 class="text-lg font-bold mb-4">
          ویرایش کامیون: {{ selectedTruck.plate_number }}
        </h2>
        <div>
          <input
              v-model="newTruckData.plate_number"
              type="text"
              placeholder="پلاک کامیون"
              class="input input-bordered w-full mb-2"
          />
          <input
              v-model="newTruckData.color"
              type="text"
              placeholder="رنگ"
              class="input input-bordered w-full mb-2"
          />
          <input
              v-model="newTruckData.type"
              type="text"
              placeholder="نوع کامیون"
              class="input input-bordered w-full mb-2"
          />
          <input
              v-model="newTruckData.weight"
              type="number"
              placeholder="وزن"
              class="input input-bordered w-full mb-2"
          />
        </div>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-primary w-full" @click="updateTruck">ذخیره تغییرات</button>
        </div>
      </div>
    </div>

    <!-- مودال تایید حذف -->
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="cancelDelete">✕</button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کامیون را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-outline" @click="cancelDelete">لغو</button>
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
th,
td,
tr {
  text-align: center !important;
}
</style>
