<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";
const { $toast } = useNuxtApp();

const truckType = ['غیره', 'کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']

// برای نگه‌داری وضعیت کامیونی که قرار است ویرایش شود
const selectedTruck = ref(null);

// برای نگه‌داری اطلاعات ویرایش شده کامیون
const newTruckData = ref({
  plate_number: "",
  color: "#000000",
  type: "",
  weight: "",
});

let loading = ref(false)
const showDeleteConfirmation = ref(false);
const truckToDelete = ref(null); // برای نگه‌داری کامیونی که باید حذف شود
const editTruck = (truck) => {
  selectedTruck.value = truck;
  newTruckData.value = {...truck}; // بارگذاری داده‌های کامیون برای ویرایش
};
const deleteTruck = (id) => {
  truckToDelete.value = id;
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
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
      $toast('اطلاعات کامیون با موفقیت بروزرسانی شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        style: {
          opacity: '1',
          userSelect: 'initial',
        },
        "dangerouslyHTMLString": true
      })
      fetchTrucks();
    } else {
      console.error("Error updating truck:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};
// تابع برای شروع فرآیند حذف کامیون

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
      $toast('کامیون با موفقیت حذف شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
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


const trucks = ref([]);
const sort = ref({column: "id", direction: "asc"}); // پیش‌فرض مرتب‌سازی بر اساس شناسه (id)
const q = ref('');
const status = ref(false);
const pageCountList = [5, 10, 15, 20, 25, 30];
const pageCountListSelected = ref(pageCountList[0]);
const pagination = ref({
  pageIndex: 0,
  pageSize: pageCountListSelected.value
});
const pageCount = ref(0);
const totalItems = ref(0);

const pageNumbers = ref([]);

const fetchTrucks = async () => {
  status.value = true;
  trucks.value = [];
  loading.value = true
  try {
    const response = await fetch(
        `${basUrl().value}/trucks?page=${pagination.value.pageIndex + 1}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      pageCount.value = Math.ceil(data.total / pageCountListSelected.value);
      totalItems.value = data.total;
      updatePageNumbers();
    } else {
      console.error("Error fetching trucks:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    status.value = false;
    loading.value = false
  }
};

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

// تغییر وضعیت مرتب‌سازی برای هر ستون
const setSort = (column) => {
  if (sort.value.column === column) {
    sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value.column = column;
    sort.value.direction = 'asc'; // مرتب‌سازی پیش‌فرض بر اساس صعودی
  }
};

watch(pageCountListSelected, () => {
  pagination.value.pageIndex = 0;
  pagination.value.pageSize = pageCountListSelected.value;
  fetchTrucks();
});

watch(() => sort, fetchTrucks, {deep: true});
watch(q, ()=>{
  fetchTrucks()
  pagination.value.pageIndex = 0;
});
watch(() => pagination.value.pageIndex, fetchTrucks);
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
                <nuxt-link to="/front-end/public">
                  <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                  خانه
                </nuxt-link>
              </li>
              <li>
                <a class="flex items-center">
                  <Icon name="ph:truck-duotone" class="ml-1" style="vertical-align: -5px" size="18"/>
                  کامیون‌ها
                </a>
              </li>
            </ul>
          </div>
          <div class="text-left">
            <NuxtLink to="/truck/create">
              <button class="btn btn-success flex items-center">
                <span> کامیون جدید</span>
                <Icon name="fa6-solid:truck-medical" size="18"/>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto mt-4 card shadow-lg p-1 rounded-2xl">
        <div class="py-2 px-4 w-full flex flex-wrap items-center gap-4 bg-base-100 rounded-md shadow-md">

          <!-- جستجو -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <input v-model="q" type="text" placeholder="جستجو ..." class="input input-md w-full sm:w-48" />
          </div>

          <!-- تعداد ردیف -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <select v-model="pageCountListSelected" class="select select-md w-full sm:w-32">
              <option disabled selected>تعداد ردیف</option>
              <option v-for="item in pageCountList" :key="item" :value="item">{{ item }} ردیف</option>
            </select>
          </div>
        </div>

        <div class="mt-4 shadow-lg p-1 rounded-xl">
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

              <!-- ستون پلاک کامیون -->
              <th>
                پلاک کامیون
                <button
                    class="btn btn-xs rounded-4xl"
                    :class="{
                            'btn-primary': sort.column === 'plate_number',
                            'btn-active': sort.column !== 'plate_number'
                    }"
                    @click="setSort('plate_number')"
                >
                  <Icon
                      :name="sort.column === 'plate_number' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                      size="18"
                  />
                </button>
              </th>

              <!-- ستون رنگ -->
              <th>
                رنگ
                <button
                    class="btn btn-xs rounded-4xl"
                    :class="{
                            'btn-primary': sort.column === 'color',
                            'btn-active': sort.column !== 'color'
                    }"
                    @click="setSort('color')"
                >
                  <Icon
                      :name="sort.column === 'color' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                      size="18"
                  />
                </button>
              </th>

              <!-- ستون نوع کامیون -->
              <th>
                نوع کامیون
                <button
                    class="btn btn-xs rounded-4xl"
                    :class="{
                            'btn-primary': sort.column === 'type',
                            'btn-active': sort.column !== 'type'
                    }"
                    @click="setSort('type')"
                >
                  <Icon
                      :name="sort.column === 'type' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                      size="18"
                  />
                </button>
              </th>

              <!-- ستون وزن -->
              <th>
                وزن
                <button
                    class="btn btn-xs rounded-4xl"
                    :class="{
                            'btn-primary': sort.column === 'weight',
                            'btn-active': sort.column !== 'weight'
                    }"
                    @click="setSort('weight')"
                >
                  <Icon
                      :name="sort.column === 'weight' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                      size="18"
                  />
                </button>
              </th>

              <!-- ستون کمپانی -->
              <th>
                کمپانی
<!--                <button-->
<!--                    class="btn btn-xs rounded-4xl"-->
<!--                    :class="{-->
<!--                            'btn-primary': sort.column === 'company',-->
<!--                            'btn-active': sort.column !== 'company'-->
<!--                    }"-->
<!--                    @click="setSort('company')"-->
<!--                >-->
<!--                  <Icon-->
<!--                      :name="sort.column === 'company' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"-->
<!--                      size="18"-->
<!--                  />-->
<!--                </button>-->
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
                v-for="truck in trucks"
                :key="truck.id"
                class="group hover:bg-base-300 transition text-center"
            >
              <td>{{ truck.id }}</td>
              <td>
                <div dir="ltr" class="flex items-center w-[200px] h-[42px] border-4 border-black rounded-md shadow-md overflow-hidden font-bold">

                  <!-- نوار آبی سمت چپ -->
                  <div class="flex flex-col items-center justify-between w-8 h-full bg-blue-700 text-white p-1 text-xs">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Flag_of_Iran.svg/20px-Flag_of_Iran.svg.png" alt="Iran Flag" class="w-full h-auto">
                    <span>I.R.</span>
                  </div>

                  <!-- شماره پلاک -->
                  <div dir="rtl" class="flex-1 text-center text-xl tracking-wider">
                    {{ truck.plate_right }}{{ truck.plate_char }}{{ truck.plate_left }}
                  </div>

                  <!-- کد استان -->
                  <div class="flex flex-col justify-between items-center w-14 h-full border-l border-black px-1 py-1 text-[11px] leading-none">
                    <span class="">{{ truck.plate_city }}</span>
                    <span class="mt-auto">ایـــران</span>
                  </div>

                </div>

              </td>
              <td>
                <div class="status status-info animate-bounce w-5 h-5"
                     :style="{ backgroundColor: truck.color }"
                ></div>
              </td>
              <td>{{ truck.type }}</td>
              <td>{{ truck.weight }}</td>
              <td>{{ truck.company.name }}</td>

              <!-- ستون عملیات (پین شده و هماهنگ با هاور) -->
              <td class="sticky left-0 z-0 bg-base-100 group-hover:bg-base-300 transition">
                <div>
                  <div @click="deleteTruck(truck.id)" onclick="deleteTruck_modal.showModal()" class="tooltip"
                       data-tip="حذف">
                    <button class="btn btn-xs btn-error">
                      <Icon name="i-heroicons-trash-20-solid" size="18"/>
                    </button>
                  </div>
                  <div @click="editTruck(truck)" onclick="editLocation_modal.showModal()" class="tooltip" data-tip="ویرایش">
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
          <div v-else-if="trucks.length === 0" class="w-full text-center my-4">
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
    </div>
    <!-- مودال ویرایش کامیون -->
    <dialog id="editLocation_modal" class="modal">
      <div class="modal-box" v-if="selectedTruck">
        <div class="form-control">
          <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
            <legend class="fieldset-legend">
              ویرایش ماشین: {{ newTruckData.plate_number }}
              <Icon name="ph:truck-duotone" size="18"></Icon>
            </legend>
<!--            <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">-->
<!--          <span class="flex items-center">-->
<!--            <Icon name="solar:plate-linear" size="18" class="ml-2"/>-->
<!--            پلاک کامیون-->
<!--          </span>-->
<!--              <input v-model="newTruckData.plate_number" type="text" class="grow" placeholder="پلاک کامیون"/>-->
<!--            </label>-->


            <!-- رنگ کامیون -->
            <label class="input input-bordered flex items-center gap-2 mt-4 w-6/12">
              <Icon name="material-symbols:colors" size="18" class="ml-2"/>
              <span> رنگ</span>
              <input v-model="newTruckData.color" type="color" class="grow" placeholder="رنگ"/>
            </label>

            <!-- نوع کامیون -->
            <label class="floating-label mt-4">
          <span class="flex items-center">
            نوع ماشین
          </span>
              <select v-model="newTruckData.type" class="select">
                <option disabled selected>تعداد ردیف</option>
                <option v-for="item in truckType" :key="item">{{ item }}</option>
              </select>
            </label>

            <!-- وزن کامیون -->
            <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
          <span class="flex items-center">
            <Icon name="mdi:weight-kilogram" size="18" class="ml-2"/>
            وزن کامیون
          </span>
              <input v-model="newTruckData.weight" type="number" class="grow" placeholder="وزن کامیون"/>
            </label>


            <!-- دکمه ارسال -->
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button @click="updateTruck" class="btn btn-primary w-full">ذخیره تغییرات</button>
            </form>
          </fieldset>
        </div>
      </div>
    </dialog>

    <!-- مودال تایید حذف -->
    <dialog id="deleteTruck_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کامیون را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
              <Icon name="material-symbols:close"/>
            </button>
            <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
          </form>
        </div>
      </div>
    </dialog>
  </div>

</template>

