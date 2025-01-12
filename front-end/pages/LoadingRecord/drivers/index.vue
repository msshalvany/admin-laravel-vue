<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";

const columns = [
  {key: "id", label: "#", sortable: true},
  {key: "name", label: "نام و نام خانوادگی", sortable: true},
  {key: "address", label: "آدرس", sortable: true},
  {key: "license_number", label: "شماره گواهی‌نامه", sortable: true},
  {key: "actionsStar", label: "امتیاز"},
  {key: "actions", label: "عملیات"},
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
const sort = ref({column: "name", direction: "asc"});
const q = ref('');
const status = ref(false);
const pageCountList = [10,15,20,25,30]
const pageCountListSelected = ref(pageCountList[0])

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
  drivers.value = []
  try {
    const response = await fetch(
        `${basUrl().value}/drivers?page=${page.value}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      pageCount.value = data.per_page;
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
  newDriverData.value = {...driver}; // بارگذاری داده‌های راننده برای ویرایش
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
      AlertSuccess('اطلاعات راننده با موفقیت بروزرسانی شد')
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
      AlertSuccess("راننده با موفقیت حذف شد");
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

watch(page, fetchDrivers);
watch(pageCountListSelected, fetchDrivers);
watch(sort, fetchDrivers);

watch(q, fetchDrivers);

onMounted(fetchDrivers);
</script>


<template>
  <div>
    <div class="p-4">
      <div class="card shadow-md px-5 py-1 rounded-lg">
        <div class="flex justify-between items-center mb-4">
          <div class="breadcrumbs text-sm">
            <ul class="flex items-center">
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
                <a class="flex items-center">
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
                راننده جدید
                <Icon name="material-symbols-add-circle" size="18"/>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
      <!-- جدول رانندگان -->
      <div class="overflow-x-auto mt-2 card shadow-lg p-1 rounded-2xl">
        <div class="flex px-3 py-3.5 border-b border-gray-200">
          <UInput
              v-model="q"
              placeholder="جستجو"
              :loading="status"
          />
          <USelectMenu class="mx-2" placeholder="ردیف" v-model="pageCountListSelected" :options="pageCountList" >
            <template #leading>
              <Icon name="material-symbols-light:format-list-bulleted" size="18"/>
            </template>
          </USelectMenu>
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
          <template #actionsStar-data="{ row }">
            <div class="rating rating-lg rating-half">
              <input type="radio" name="rating-readonly" class="rating-hidden" />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-1 bg-orange-400"
                  :checked="row.average_star >= 0.5"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-2 bg-orange-400"
                  :checked="row.average_star >= 1"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-1 bg-orange-400"
                  :checked="row.average_star >= 1.5"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-2 bg-orange-400"
                  :checked="row.average_star >= 2"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-1 bg-orange-400"
                  :checked="row.average_star >= 2.5"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-2 bg-orange-400"
                  :checked="row.average_star >= 3"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-1 bg-orange-400"
                  :checked="row.average_star >= 3.5"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-2 bg-orange-400"
                  :checked="row.average_star >= 4"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-1 bg-orange-400"
                  :checked="row.average_star >= 4.5"
                  disabled
              />
              <input
                  type="radio"
                  name="rating-readonly"
                  class="mask mask-star-2 mask-half-2 bg-orange-400"
                  :checked="row.average_star >= 5"
                  disabled
              />
            </div>
          </template>

        </UTable>
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
      <div class="modal-box p-6">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <!-- عنوان -->
        <h2 class="text-lg font-bold mb-6 text-center">
          ویرایش راننده: {{ selectedDriver.name }}
        </h2>
        <!-- فرم ورودی‌ها -->
        <div class="space-y-6">
          <!-- نام و نام خانوادگی -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="mdi:user" size="18" class="ml-2"/>
          نام و نام خانوادگی
        </span>
            <input
                v-model="newDriverData.name"
                type="text"
                placeholder="نام و نام خانوادگی"
                class="grow"
            />
          </label>

          <!-- آدرس -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="mdi:map-marker-account" size="18" class="ml-2"/>
          آدرس
        </span>
            <input
                v-model="newDriverData.address"
                type="text"
                placeholder="آدرس"
                class="grow"
            />
          </label>

          <!-- شماره گواهی‌نامه -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="fa6-solid:address-card" size="18" class="ml-2"/>
          شماره گواهی‌نامه
        </span>
            <input
                v-model="newDriverData.license_number"
                type="text"
                placeholder="شماره گواهی‌نامه"
                class="grow"
            />
          </label>
        </div>
        <!-- دکمه ذخیره تغییرات -->
        <div class="modal-action mt-8" dir="ltr">
          <button class="btn btn-primary w-full" @click="updateDriver">ذخیره تغییرات</button>
        </div>
      </div>
    </div>


    <!-- مودال تایید حذف -->
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="cancelDelete">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این راننده را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
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
