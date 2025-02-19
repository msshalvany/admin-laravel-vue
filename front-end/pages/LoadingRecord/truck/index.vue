<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";


const truckType = ['غیره', 'کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']
const type = ref(truckType[1]);

const columns = [
  {key: "plate_number", label: "پلاک کامیون", sortable: true},
  {key: "actionsColor", label: "رنگ", sortable: true},
  {key: "type", label: "نوع کامیون", sortable: true},
  {key: "weight", label: "وزن", sortable: true},
  {key: "company.name", label: "کمپانی"},
  {key: "actions", label: "عملیات"},
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
const sort = ref({column: "plate_number", direction: "asc"});
const q = ref('');
const status = ref(false);
const pageCountList = [10,15,20,25,30]
const pageCountListSelected = ref(pageCountList[0])

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
  trucks.value = []
  try {
    const response = await fetch(
        `${basUrl().value}/trucks?page=${page.value}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      console.log(data.total)
      trucks.value = data.data;
      pageCount.value = data.per_page;
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
  newTruckData.value = {...truck}; // بارگذاری داده‌های کامیون برای ویرایش
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

watch(page, fetchTrucks);

watch(pageCountListSelected, fetchTrucks);

watch(sort, fetchTrucks);

watch(q, fetchTrucks);

onMounted(fetchTrucks);
</script>

<template>
  <div>
    <div class="p-4">
      <div class="card shadow-md px-5 rounded-lg">
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
                  <Icon name="fa6-solid:truck" class="ml-1" style="vertical-align: -5px" size="15"/>
                  کامیون‌ها
                </a>
              </li>
            </ul>
          </div>
          <div class="text-left">
            <NuxtLink to="/LoadingRecord/truck/create">
              <button class="btn btn-success flex items-center">
                <span> کامیون جدید</span>
                <Icon name="material-symbols-add-circle" size="18"/>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
      <!-- جدول کامیون‌ها -->
      <div class="overflow-x-auto mt-4 card shadow-lg p-1 rounded-2xl">
        <div class="flex px-3 py-3.5 ">
          <UInput
              v-model="q"
              placeholder="جستجو"
              :loading="status"
              :ui="{ td: { base: 'max-w-[0] truncate' }, default: { checkbox: { color: 'gray'} } }"

          />
          <USelectMenu class="mx-2" placeholder="ردیف" v-model="pageCountListSelected" :options="pageCountList" >
            <template #leading>
              <Icon name="material-symbols-light:format-list-bulleted" size="18"/>
            </template>
          </USelectMenu>
        </div>
        <UTable
            :rows="trucks"
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
                <Icon name="mingcute:settings-7-line" size="18"/>
              </button>
            </UDropdown>
          </template>
          <template #actionsColor-data="{ row }">
            <div class="status  animate-bounce" :style="`background-color:${row.color};width:20px;height: 20px`" a></div>
          </template>
        </UTable>
        <div class="flex px-3 py-3.5 border-t border-gray-200">
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
    </div>

    <!-- مودال ویرایش کامیون -->
    <div v-if="selectedTruck" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <h2 class="text-lg font-bold mb-6 mt-4 text-center">
          ویرایش کامیون: {{ selectedTruck.plate_number }}
        </h2>
        <form class="form-control">
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
          <span class="flex items-center">
            <Icon name="solar:plate-linear" size="18" class="ml-2"/>
            پلاک کامیون
          </span>
            <input v-model="newTruckData.plate_number" type="text" class="grow" placeholder="پلاک کامیون"/>
          </label>


          <!-- رنگ کامیون -->
          <label class="input input-bordered flex items-center gap-2 mt-4 w-6/12">
            <Icon name="material-symbols:colors" size="18" class="ml-2"/>
            <span> رنگ</span>
            <input v-model="newTruckData.color" type="color" class="grow" placeholder="رنگ"/>
          </label>

          <!-- نوع کامیون -->
          <USelectMenu v-model="type" size="xl"
                       :options="truckType"
                       placeholder="انتخاب نوع ماشین"
                       :popper="{ arrow: true }"
                       searchable
                       searchable-placeholder="جستجو....."
                       class="mt-4"
          />

          <!-- وزن کامیون -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
          <span class="flex items-center">
            <Icon name="mdi:weight-kilogram" size="18" class="ml-2" />
            وزن کامیون
          </span>
            <input v-model="newTruckData.weight" type="number" class="grow" placeholder="وزن کامیون" />
          </label>


          <!-- دکمه ارسال -->
          <div class="modal-action" dir="ltr">
            <button type="button" class="btn btn-primary w-full" @click="updateTruck">ذخیره تغییرات</button>
          </div>
        </form>
      </div>
    </div>

    <!-- مودال تایید حذف -->
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="cancelDelete">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کامیون را حذف کنید؟</h2>
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

::-webkit-scrollbar{
  width:10px;
  background:#000;
}

::-webkit-scrollbar-track{
  -webkit-box-shadow: inset 0px 0px 6px rgba(0,0,0,.3);
  background:#f4f4f4;
}

::-webkit-scrollbar-thumb{
  background:#1baaae;
  background-image:-webkit-linear-gradient(20deg, rgba(255,255,255,.5) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.5) 50%, rgba(255,255,255,.5) 75%, transparent 75%)
}
</style>
