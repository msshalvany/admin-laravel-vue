<script setup>
import { ref, onMounted, watch } from "vue";
import { useCookie } from "nuxt/app";
import { AlertSuccess, loaderfun } from "~/composables/statFunc.js";

const columns = [
  { key: "id", label: "#", sortable: true },
  { key: "name", label: "نام و نام خانوادگی", sortable: true },
  { key: "address", label: "آدرس", sortable: true },
  { key: "license_number", label: "شماره گواهی‌نامه", sortable: true },
  { key: "actions", label: "عملیات" },
];

const items = (row) => [
  [
    {
      label: "ویرایش",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => editDriver(row),
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

const drivers = ref([]);
const page = ref(1);
const pageCount = ref(0);
const total = ref(0);
const sort = ref({ column: "name", direction: "asc" });
const q = ref("");
const status = ref(false);

// برای نگه‌داری وضعیت راننده‌ای که قرار است ویرایش شود
const selectedDriver = ref(null);

// برای نگه‌داری اطلاعات ویرایش شده راننده
const newDriverData = ref({
  name: "",
  address: "",
  license_number: "",
});

// برای نگه‌داری وضعیت مودال تایید حذف
const showDeleteConfirmation = ref(false);
const driverToDelete = ref(null); // برای نگه‌داری راننده‌ای که باید حذف شود

const fetchDrivers = async () => {
  status.value = true;
  try {
    const response = await fetch(
        `${basUrl().value}/drivers?page=${page.value}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}`,
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
      drivers.value = data.data;
      pageCount.value = data.last_page;
      total.value = data.total;
    } else {
      console.error("Error fetching drivers:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    status.value = false;
  }
};

const editDriver = (driver) => {
  selectedDriver.value = driver;
  newDriverData.value = { ...driver }; // بارگذاری داده‌های راننده برای ویرایش
};

const updateDriver = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/drivers/${selectedDriver.value.id}`,
        {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify(newDriverData.value),
        }
    );

    if (response.ok) {
      fetchDrivers();
      closeModal();
    } else {
      console.error("Error updating driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

// تابع برای شروع فرآیند حذف راننده
const initiateDelete = (id) => {
  driverToDelete.value = id;
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};

// تابع برای تایید حذف راننده
const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/drivers/${driverToDelete.value}`,
        {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      AlertSuccess("عملیات با موفقیت انجام شد");
      fetchDrivers();
      closeDeleteModal(); // بستن مودال تایید حذف
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

// تابع برای لغو عملیات حذف
const cancelDelete = () => {
  showDeleteConfirmation.value = false; // بستن مودال تایید حذف
  driverToDelete.value = null;
};

const closeModal = () => {
  selectedDriver.value = null;
  newDriverData.value = {
    name: "",
    address: "",
    license_number: "",
  };
};

const closeDeleteModal = () => {
  showDeleteConfirmation.value = false;
  driverToDelete.value = null;
};

// استفاده از واچر برای صفحه‌بندی
watch(page, fetchDrivers);

// استفاده از واچر برای مرتب‌سازی
watch(sort, fetchDrivers);

// اولین بار داده‌ها بارگذاری شود
onMounted(fetchDrivers);
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
              رانندگان
            </a>
          </li>
        </ul>
      </div>

      <!-- دکمه ایجاد راننده جدید -->
      <div class="text-left">
        <NuxtLink to="/LoadingRecord/drivers/create">
          <button class="btn btn-success">
            ایجاد راننده جدید
            <Icon name="material-symbols-add-circle" size="18"/>
          </button>
        </NuxtLink>
      </div>

      <!-- جدول رانندگان -->
      <div class="overflow-x-auto mt-4">
        <div class="flex px-3 py-3.5 border-b border-gray-200">
          <UInput
              v-model="q"
              placeholder="جستجو"
              :loading="status"
              @input="fetchDrivers"
          />
        </div>
        <UTable
            :rows="drivers"
            :columns="columns"
            v-model:sort="sort"
            :loading="status"
            :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'در حال پردازش...' }"
            :progress="{ color: 'primary', animation: 'carousel' }"
            class="w-full"
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
            @change="fetchDrivers"
        />
      </div>
    </div>

    <!-- مودال ویرایش راننده -->
    <div v-if="selectedDriver" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="closeModal">✕</button>
        <br>
        <h2 class="text-lg font-bold mb-4">
          ویرایش راننده: {{ selectedDriver.name }}
        </h2>
        <div>
          <input
              v-model="newDriverData.name"
              type="text"
              placeholder="نام و نام خانوادگی"
              class="input input-bordered w-full mb-2"
          />
          <input
              v-model="newDriverData.address"
              type="text"
              placeholder="آدرس"
              class="input input-bordered w-full mb-2"
          />
          <input
              v-model="newDriverData.license_number"
              type="text"
              placeholder="شماره گواهی‌نامه"
              class="input input-bordered w-full mb-2"
          />
        </div>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-primary w-full" @click="updateDriver">ذخیره تغییرات</button>
        </div>
      </div>
    </div>

    <!-- مودال تایید حذف -->
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="cancelDelete">✕</button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این راننده را حذف کنید؟</h2>
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
