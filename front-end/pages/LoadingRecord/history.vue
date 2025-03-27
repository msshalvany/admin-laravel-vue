<script setup>
import { ref, onMounted, watch } from "vue";
import { useCookie } from "nuxt/app";

const columns = [
  { key: "entry_date", label: "تاریخ تردد", sortable: true },
  { key: "entry_time", label: "ساعت ورود" },
  { key: "exit_time", label: "ساعت خروج" },
  { key: "locations", label: "مکان ها" },
];

const page = ref(1);
const pageCount = ref(0);
const total = ref(0);
const sort = ref({ column: "created_at", direction: "asc" });
const q = ref('');
const status = ref(false);
const pageCountList = [10, 15, 20, 25, 30];
const pageCountListSelected = ref(pageCountList[0]);
const report = ref(null)




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



const LoadingRecord = ref([]);

const fetchLoadingRecord = async () => {
  status.value = true;
  LoadingRecord.value = [];
  try {
    const response = await fetch(
        `${basUrl().value}/loading_records?page=${page.value}&sort=${sort.value.column}&order=${sort.value.direction}&q=${q.value}&countPage=${pageCountListSelected.value}`,
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
      pageCount.value = data.per_page;
      total.value = data.total;
    } else {
      console.error("Error fetching", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  } finally {
    status.value = false;
  }
};

watch(page, fetchLoadingRecord);
watch(pageCountListSelected, fetchLoadingRecord);
watch(sort, fetchLoadingRecord);
watch(q, fetchLoadingRecord);
onMounted(fetchLoadingRecord);
watch(report, downloadExcelReport);


const expand = ref({
  openedRows: [],
  row: {}
});

// متغیر برای باز و بسته کردن مودال
const showModal = ref(false);
const selectedRow = ref({});

// تابعی برای باز کردن مودال و ذخیره کردن ردیف انتخاب شده
const openModal = (row) => {
  selectedRow.value = row;
  showModal.value = true;
};

// تابع برای بستن مودال
const closeModal = () => {
  showModal.value = false;
};
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
      <!-- جدول رانندگان -->
      <div class="overflow-x-auto mt-2 card shadow-lg p-1 rounded-2xl">
        <div class="flex px-3 py-3.5 border-b border-gray-200">
          <UInput
              v-model="q"
              placeholder="جستجو"
              :loading="status"
          />
          <USelectMenu class="mx-2" placeholder="ردیف" v-model="pageCountListSelected" :options="pageCountList">
            <template #leading>
              <Icon name="material-symbols-light:format-list-bulleted" size="18"/>
            </template>
          </USelectMenu>
          <button class="btn btn-sm btn-primary custom-input" >
            گزارش گیری
            <Icon name="tabler:report-analytics"
                  size="18"/>
          </button>
          <date-picker
              v-model="report"
              range
              clearable
              format="YYYY-MM-DD"
              display-format="jMMMM jD"
              custom-input=".custom-input"
          />
        </div>
        <UTable
            :rows="LoadingRecord"
            :columns="columns"
            v-model:sort="sort"
            :loading="status"
            :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'در حال پردازش...' }"
            :progress="{ color: 'primary', animation: 'carousel' }"
            v-model:expand="expand"
        >
          <template #locations-data="{ row }">
            <span v-for="item in row.locations">
              {{ item.location_name }} ,
            </span>

          </template>
          <template #expand="{ row }">
            <div class="card shadow-lg p-4 mb-4">
              <!-- نمایش اطلاعات کلی تردد -->
              <h3 class="text-xl font-semibold mb-2">جزئیات تردد</h3>
              <p><strong>تاریخ ورود:</strong> {{ row.entry_date }}</p>
              <p><strong>ساعت ورود:</strong> {{ row.entry_time }}</p>
              <p><strong>ساعت خروج:</strong> {{ row.exit_time }}</p>
              <p><strong>وزن خالی:</strong> {{ row.empty_weight }} kg</p>
              <p><strong>وزن بار:</strong> {{ row.loaded_weight }} kg</p>
              <br>
              <p><strong> مشاهده اطلاعات راننده و کامیون:</strong><br><button class="btn btn-xs rounded-box btn-primary mt-4" @click="openModal(row)"><Icon name="ic:outline-remove-red-eye" size="18"/></button></p>

            </div>
          </template>

          <template #expand-action="{ row, isExpanded, toggle }">
            <button class="btn btn-primary" @click="toggle">
              مشاهده جزئیات
              <Icon name="ic:outline-remove-red-eye" size="18"></Icon>
            </button>
          </template>
        </UTable>
        <!-- مودال نمایش اطلاعات راننده و کامیون -->
        <div v-if="showModal" class="modal modal-open" :key="selectedRow.id">
          <div class="modal-box">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
              <Icon name="material-symbols:close"/>
            </button>
            <br>
            <h2 class="text-xl font-bold mb-4">اطلاعات راننده و کامیون</h2>
            <!-- اطلاعات راننده -->
            <div class="mb-4">
              <h3 class="font-semibold">اطلاعات راننده:</h3>
              <p><strong>نام راننده:</strong> {{ selectedRow.driver.name }}</p>
              <p><strong>شماره گواهینامه:</strong> {{ selectedRow.driver.license_number }}</p>
              <p><strong>آدرس راننده:</strong> {{ selectedRow.driver.address }}</p>
            </div>

            <!-- اطلاعات کامیون -->
            <div class="mb-4">
              <h3 class="font-semibold">اطلاعات کامیون:</h3>
              <p><strong>پلاک کامیون:</strong> {{ selectedRow.truck.plate_number }}</p>
              <p><strong>نوع کامیون:</strong> {{ selectedRow.truck.type }}</p>
              <p><strong>رنگ کامیون:</strong> {{ selectedRow.truck.color }}</p>
            </div>

          </div>
        </div>
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
  </div>
</template>