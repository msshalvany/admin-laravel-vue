<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";

let loading = ref(false)
const sort = ref({column: "entry_date", direction: "asc"});
const q = ref('');
const status = ref(false);
const pageCountList = [10, 15, 20, 25, 30];
const pageCountListSelected = ref(pageCountList[0]);
const report = ref(null)

const pageCount = ref(0);
const totalItems = ref(0);
const pagination = ref({
  pageIndex: 0,
  pageSize: pageCountListSelected.value
})
const pageNumbers = ref([]);


const downloadExcelReport = async () => {
  status.value = true;
  try {
    const baseUrl = basUrl().value;
    const token = useCookie("jwt").value;

    if (!token) {
      console.error("Token not found");
      status.value = false;
      return;
    }

    // ایجاد URL با پارامتر `date`
    const url = new URL(`${baseUrl}/loading_records/report`);
    url.searchParams.append("date", report.value);

    const response = await fetch(url, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error(`Error fetching report: ${response.status}`);
    }

    // دریافت داده به‌صورت Blob
    const blob = await response.blob();

    // ایجاد URL برای فایل Blob
    const fileUrl = window.URL.createObjectURL(blob);

    // ایجاد یک لینک برای دانلود فایل
    const a = document.createElement("a");
    a.href = fileUrl;
    a.download = `Report_${report.value}.xlsx`; // نام فایل اکسل
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    // آزاد کردن URL Blob از حافظه
    window.URL.revokeObjectURL(fileUrl);
  } catch (error) {
    console.error("Error:", error.message || error);
  } finally {
    status.value = false;
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
const setSort = (column) => {
  if (sort.value.column === column) {
    sort.value.direction = sort.value.direction === 'asc' ? 'desc' : 'asc';
  } else {
    sort.value.column = column;
    sort.value.direction = 'asc'; // مرتب‌سازی پیش‌فرض بر اساس صعودی
  }
};


const LoadingRecord = ref([]);
const fetchLoadingRecord = async () => {
  loading.value = true;
  LoadingRecord.value = [];
  try {
    const response = await fetch(
        `${basUrl().value}/loading_records?page=${pagination.value.pageIndex}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      LoadingRecord.value = data.data;
      pageCount.value = Math.ceil(data.total / pageCountListSelected.value);
      totalItems.value = data.total;
      updatePageNumbers();
    } else {
      console.error("Error fetching", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchLoadingRecord);
watch(report, downloadExcelReport);
watch(pageCountListSelected, () => {
  pagination.value.pageIndex = 0;
  pagination.value.pageSize = pageCountListSelected.value;
  fetchLoadingRecord();
});
watch(() => sort, fetchLoadingRecord, {deep: true});
watch(() => pagination.value.pageIndex, fetchLoadingRecord);
watch(q, ()=>{
  fetchLoadingRecord()
  pagination.value.pageIndex = 0;
});
onMounted(fetchLoadingRecord);


const columns = [
  {key: "entry_date", label: "تاریخ تردد", sortable: true},
  {key: "entry_time", label: "ساعت ورود"},
  {key: "exit_time", label: "ساعت خروج"},
  {key: "locations", label: "مکان ها"},
];

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
                  <Icon name="streamline:shipping-transfer-truck-time-truck-shipping-delivery-time-waiting-delay"
                        size="18"/>
                  تاریخچه
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto mt-2 card shadow-lg p-1 rounded-2xl">
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

          <!-- گزارش گیری -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <button class="btn btn-sm btn-primary custom-input flex items-center gap-1">
              گزارش گیری
              <Icon name="tabler:report-analytics" size="18" />
            </button>
          </div>

          <!-- انتخاب بازه زمانی -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <date-picker
                v-model="report"
                range
                clearable
                format="YYYY-MM-DD"
                display-format="jMMMM jD"
                custom-input=".custom-input"
            />
          </div>

        </div>
        <table class="table min-w-max w-full">
          <thead class="bg-base-content/20">
          <tr class="text-center">
            <!-- ستون شناسه -->
            <th>
              تاریخ تردد
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'entry_date',
                            'btn-active': sort.column !== 'entry_date'
                    }"
                  @click="setSort('entry_date')"
              >
                <Icon
                    :name="sort.column === 'entry_date' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              ساعت ورود
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'entry_time',
                            'btn-active': sort.column !== 'entry_time'
                    }"
                  @click="setSort('entry_time')"
              >
                <Icon
                    :name="sort.column === 'entry_time' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              ساعت خروج
              <button
                  class="btn btn-xs rounded-4xl"
                  :class="{
                            'btn-primary': sort.column === 'exit_time',
                            'btn-active': sort.column !== 'exit_time'
                    }"
                  @click="setSort('exit_time')"
              >
                <Icon
                    :name="sort.column === 'exit_time' && sort.direction === 'asc' ? 'mdi:arrow-up' : 'mdi:arrow-down'"
                    size="18"
                />
              </button>
            </th>
            <th>
              مکان ها
            </th>
            <th>
              اطلاعات تکمیلی
            </th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-for="item in LoadingRecord"
              :key="item.id"
              class="group hover:bg-base-300 transition text-center"
          >
            <td>{{ item.entry_date }}</td>
            <td>{{ item.entry_time }}</td>
            <td>{{ item.exit_time }}</td>
            <td>
             <span v-for="(location,index) in item.locations">
               {{ location.location_name }}
               <span v-if="location.length === index">,</span>
             </span>
            </td>
            <td>
              <div class="drawer">
                <input id="my-drawer" type="checkbox" class="drawer-toggle"/>
                <div class="drawer-content">
                  <!-- Page content here -->
                  <label for="my-drawer" class="btn btn-primary drawer-button">اطلاعات تکمیلی</label>
                </div>
                <div class="drawer-side  ">
                  <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                  <ul class="menu bg-base-200 text-base-content min-h-full p-4 overflow-y-auto w-[90%] md:w-[45%] space-y-3">

                    <!-- اطلاعات راننده -->
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                      <input type="radio" name="my-accordion-2" />
                      <div class="collapse-title font-bold">اطلاعات راننده</div>
                      <div class="collapse-content text-sm space-y-2">
                        <div><strong>نام و نام خانوادگی:</strong> {{ item.driver.name }}</div>
                        <div><strong>آدرس:</strong> {{ item.driver.address }}</div>
                        <div><strong>شماره گواهی‌نامه:</strong> {{ item.driver.license_number }}</div>
                        <div>
                          <strong>امتیاز:</strong>
                          <div class="rating rating-xs rating-half mt-1">
                            <input type="radio" :name="`rating-${item.driver.id}`" class="rating-hidden" />
                            <template v-for="i in 10" :key="i">
                              <input
                                  type="radio"
                                  :name="`rating-${item.driver.id}`"
                                  class="mask mask-star-2"
                                  :class="i % 2 === 0 ? 'mask-half-2 bg-orange-500' : 'mask-half-1 bg-orange-500'"
                                  :aria-label="`${i * 0.5} star`"
                                  :checked="item.driver.average_star >= i * 0.5"
                                  disabled
                              />
                            </template>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- اطلاعات ماشین -->
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                      <input type="radio" name="my-accordion-2" />
                      <div class="collapse-title font-bold">اطلاعات ماشین</div>
                      <div class="collapse-content text-sm space-y-2">
                        <div class="flex justify-center content-center items-center"><strong>   پلاک کامیون:    </strong>
                          <div dir="ltr" class="mr-2 flex items-center w-[200px] h-[42px] border-4 border-black rounded-md shadow-md overflow-hidden font-bold">

                            <!-- نوار آبی سمت چپ -->
                            <div class="flex flex-col items-center justify-between w-8 h-full bg-blue-700 text-white p-1 text-xs">
                              <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Flag_of_Iran.svg/20px-Flag_of_Iran.svg.png" alt="Iran Flag" class="w-full h-auto">
                              <span>I.R.</span>
                            </div>

                            <!-- شماره پلاک -->
                            <div dir="rtl" class="flex-1 text-center text-xl tracking-wider">
                              {{ item.truck.plate_right }}{{ item.truck.plate_char }}{{ item.truck.plate_left }}
                            </div>

                            <!-- کد استان -->
                            <div class="flex flex-col justify-between items-center w-14 h-full border-l border-black px-1 py-1 text-[11px] leading-none">
                              <span class="">{{ item.truck.plate_city }}</span>
                              <span class="mt-auto">ایـــران</span>
                            </div>

                          </div>

                        </div>
                        <div class=" items-center gap-2 text-center">
                          <strong>رنگ:</strong>
                          <div class="status status-info animate-bounce w-5 h-5 rounded-full" :style="{ backgroundColor: item.truck.color }"></div>
                        </div>
                        <div><strong>نوع:</strong> {{ item.truck.type }}</div>
                        <div><strong>وزن:</strong> {{ item.truck.weight }}</div>
                      </div>
                    </div>

                    <!-- اطلاعات کمپانی -->
                    <div class="collapse collapse-arrow bg-base-100 border border-base-300">
                      <input type="radio" name="my-accordion-2" />
                      <div class="collapse-title font-bold">اطلاعات کمپانی</div>
                      <div class="collapse-content text-sm space-y-2">
                        <div><strong>نام:</strong> {{ item.company.name }}</div>
                        <div><strong>آدرس:</strong> {{ item.company.address }}</div>
                        <div><strong>شماره تماس:</strong> {{ item.company.phone }}</div>
                      </div>
                    </div>

                  </ul>
                </div>
              </div>

            </td>
          </tr>
          </tbody>
        </table>
        <div v-if="loading" class="w-full text-center my-4">
          <span class="loading loading-ring w-1/12"></span>
        </div>
        <div v-else-if="LoadingRecord.length === 0" class="w-full text-center my-4">
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
</template>