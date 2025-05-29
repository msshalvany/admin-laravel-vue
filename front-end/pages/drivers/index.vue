<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";
import {loaderfun} from "~/composables/statFunc.js";

const { $toast } = useNuxtApp();

let loading = ref(false)
const drivers = ref([]);
const sort = ref({column: "id", direction: "asc"});
const q = ref('');
const status = ref(false);
const pageCountList = [10, 15, 20, 25, 30]
const pageCountListSelected = ref(pageCountList[0])
const pageCount = ref(0);
const totalItems = ref(0);
const pagination = ref({
  pageIndex: 0,
  pageSize: pageCountListSelected.value
});
const pageNumbers = ref([]);

const updatePageNumbers = () => {
  let start = pagination.value.pageIndex - 2;
  let end = pagination.value.pageIndex + 2;

  if (start < 1) start = 1;
  if (end > pageCount.value) end = pageCount.value;

  pageNumbers.value = [];
  for (let i = start; i <= end; i++) {
    pageNumbers.value.push(i);
  }
};

const setSort = (column) => {
  if (sort.value.column === column) {
    sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value.column = column;
    sort.value.direction = 'asc'; // مرتب‌سازی پیش‌فرض بر اساس صعودی
  }
};


// برای نگه‌داری وضعیت راننده‌ای که قرار است ویرایش شود
const selectedDriver = ref(null);

// برای نگه‌داری اطلاعات ویرایش شده راننده
const newDriverData = ref({
  name: "",
  address: "",
  license_number: "",
});

// برای نگه‌داری وضعیت مودال تایید حذف
const driverToDelete = ref(null); // برای نگه‌داری راننده‌ای که باید حذف شود

const fetchDrivers = async () => {
  status.value = true;
  drivers.value = []
  loading.value = true
  try {
    const response = await fetch(
        `${basUrl().value}/drivers?page=${pagination.value.pageIndex}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      pageCount.value = Math.ceil(data.total / pageCountListSelected.value);
      totalItems.value = data.total;
      updatePageNumbers();
    } else {
      console.error("Error fetching drivers:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    status.value = false;
    loading.value = false
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
      $toast('اطلاعات راننده با موفقیت بروزرسانی شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
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
      $toast("راننده با موفقیت حذف شد", {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
      fetchDrivers();
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

const closeModal = () => {
  selectedDriver.value = null;
  newDriverData.value = {
    name: "",
    address: "",
    license_number: "",
  };
};


watch(pageCountListSelected, () => {
  pagination.value.pageIndex = 0;
  pagination.value.pageSize = pageCountListSelected.value;
  fetchDrivers();
});
watch(() => sort, fetchDrivers, {deep: true});
watch(() => pagination.value.pageIndex, fetchDrivers);
watch(q, () => {
  fetchDrivers()
  pagination.value.pageIndex = 0;
});
onMounted(fetchDrivers);
</script>


<template>
  <div>
    <div class="p-4">
      <div class="card shadow-md px-5 rounded-lg">
        <div class="flex justify-between items-center mb-4">
          <div class="breadcrumbs text-sm">
            <ul class="flex items-center">
              <li>
                <nuxt-link to="/front-end/public">
                  <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                  خانه
                </nuxt-link>
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
            <NuxtLink to="/drivers/create">
              <button class="btn btn-success">
                راننده جدید
                <Icon name="streamline:user-add-plus" size="18"/>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
      <!-- جدول رانندگان -->
      <div class="overflow-x-auto mt-2 card shadow-lg p-1 rounded-2xl">
        <div class="py-2 px-4 w-full flex flex-wrap items-center gap-4 bg-base-100 rounded-md shadow-md">

          <!-- جستجو -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <input v-model="q" type="text" placeholder="جستجو ..." class="input input-md w-full sm:w-48"/>
          </div>

          <!-- تعداد ردیف -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <select v-model="pageCountListSelected" class="select select-md w-full sm:w-32">
              <option disabled selected>تعداد ردیف</option>
              <option v-for="item in pageCountList" :key="item" :value="item">{{ item }} ردیف</option>
            </select>
          </div>
        </div>
        <table class="table min-w-max w-full">
          <thead class="bg-base-content/20">
          <tr class="text-center">
            <!-- ستون شناسه -->
            <th>
              شناسه
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'id',
                            'btn-active': sort.column !== 'id'
                    }"
                  @click="setSort('id')"
              >
                <Icon
                    :name="sort.column === 'id' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              نام و نام خانوادگی
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'name',
                            'btn-active': sort.column !== 'name'
                    }"
                  @click="setSort('name')"
              >
                <Icon
                    :name="sort.column === 'name' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              آدرس
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'address',
                            'btn-active': sort.column !== 'address'
                    }"
                  @click="setSort('address')"
              >
                <Icon
                    :name="sort.column === 'address' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              شماره گواهی‌نامه
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'license_number',
                            'btn-active': sort.column !== 'license_number'
                    }"
                  @click="setSort('license_number')"
              >
                <Icon
                    :name="sort.column === 'license_number' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              امتیاز
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'average_star',
                            'btn-active': sort.column !== 'average_star'
                    }"
                  @click="setSort('average_star')"
              >
                <Icon
                    :name="sort.column === 'average_star' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <!-- ستون عملیات (پین شده) -->
            <th class="sticky left-0 bg-base-300">
              عملیات
              <Icon name="cib:pinboard" size="24"/>
            </th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-for="driver in drivers"
              :key="driver.id"
              class="group hover:bg-base-300 transition text-center"
          >
            <td>{{ driver.id }}</td>
            <td>{{ driver.name }}</td>
            <td>{{ driver.address }}</td>
            <td>{{ driver.license_number }}</td>
            <td>
              <div class="rating rating-xs rating-half">
                <input type="radio" :name="`rating-${driver.id}`" class="rating-hidden"/>

                <template v-for="i in 10" :key="i">
                  <input
                      type="radio"
                      :name="`rating-${driver.id}`"
                      class="mask mask-star-2"
                      :class="i % 2 === 0 ? 'mask-half-2 bg-orange-500' : 'mask-half-1 bg-orange-500'"
                      :aria-label="`${i * 0.5} star`"
                      :checked="driver.average_star >= i * 0.5"
                      disabled
                  />
                </template>
              </div>
            </td>

            <!-- ستون عملیات (پین شده و هماهنگ با هاور) -->
            <td class="sticky left-0 z-0 bg-base-100 group-hover:bg-base-300 transition">
              <div class="flex">
                <div @click="driverToDelete = driver.id" onclick="confirmDelete_modal.showModal()" class="tooltip"
                     data-tip="حذف">
                  <button class="btn btn-xs btn-error">
                    <Icon name="i-heroicons-trash-20-solid" size="18"/>
                  </button>
                </div>
                <div @click="editDriver(driver)" onclick="edit_modal.showModal()" class="tooltip" data-tip="ویرایش">
                  <button class="btn btn-xs btn-info">
                    <Icon name="i-heroicons-pencil-square-20-solid" size="18"/>
                  </button>
                </div>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
        <div v-if="loading" class="w-full text-center my-4">
          <span class="loading loading-ring w-1/12"></span>
        </div>
        <div v-else-if="drivers.length === 0" class="w-full text-center my-4">
          داده ای یافت نشد
          <Icon name="material-symbols:search-off-rounded" size="30"></Icon>
        </div>
        <div class="join">
          <!-- دکمه صفحه اول -->
          <button
              class="join-item btn btn-square"
              :disabled="pagination.pageIndex === 0"
              @click="pagination.pageIndex = 0"
          >
            &lt;&lt;
          </button>

          <!-- دکمه صفحه قبلی -->
          <button
              class="join-item btn btn-square"
              :disabled="pagination.pageIndex === 0"
              @click="pagination.pageIndex = pagination.pageIndex - 1"
          >
            &lt;
          </button>

          <!-- دکمه‌های صفحات -->
          <button
              v-for="page in pageNumbers"
              :key="page"
              class="join-item btn"
              :class="{
                  'btn-active': pagination.pageIndex === page - 1,
                  'btn-primary': pagination.pageIndex === page - 1
                }"
              @click="pagination.pageIndex = page - 1"
          >
            {{ page }}
          </button>

          <!-- دکمه چند نقطه -->
          <span v-if="pageCount > 5 && pagination.pageIndex < pageCount - 3"
                class="join-item btn btn-square">...</span>

          <!-- دکمه صفحه بعد -->
          <button
              class="join-item btn btn-square"
              :disabled="pagination.pageIndex === pageCount - 1"
              @click="pagination.pageIndex = pagination.pageIndex + 1"
          >
            &gt;
          </button>

          <!-- دکمه صفحه آخر -->
          <button
              class="join-item btn btn-square"
              :disabled="pagination.pageIndex === pageCount - 1"
              @click="pagination.pageIndex = pageCount - 1"
          >
            &gt;&gt;
          </button>
        </div>
      </div>
    </div>

    <!-- مودال ویرایش راننده -->
    <dialog id="edit_modal" class="modal">
      <div class="modal-box p-6" v-if="selectedDriver">
        <div class="form-control w-full space-y-6">
          <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
            <legend class="fieldset-legend">
              ویرایش راننده: {{ selectedDriver.name }}
              <Icon name="ic:outline-place" size="18"></Icon>
            </legend>

            <div class="space-y-6">
              <!-- نام و نام خانوادگی -->
              <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
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
              <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
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
              <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
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
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button class="btn btn-primary w-full" @click="updateDriver">ذخیره تغییرات</button>
            </form>
          </fieldset>
        </div>
      </div>
    </dialog>


    <!-- مودال تایید حذف -->
    <dialog id="confirmDelete_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این راننده را حذف کنید؟</h2>
        <form method="dialog">
          <div class="modal-action" dir="ltr">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
              <Icon name="material-symbols:close"/>
            </button>
            <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
          </div>
        </form>
      </div>
    </dialog>
  </div>
</template>
