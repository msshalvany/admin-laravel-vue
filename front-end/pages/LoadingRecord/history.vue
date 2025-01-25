<script setup>
import {ref, onMounted, watch} from "vue";
import {useCookie} from "nuxt/app";
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";

const columns = [
  {key: "id", label: "#", sortable: true},
  {key: "entry_date", label: "تاریخ تردد", sortable: true},
  {key: "entry_time", label: "ساعت ورود", sortable: true},
  {key: "exit_time", label: "ساعت ورود", sortable: true},
];


const page = ref(1);
const pageCount = ref(0);
const total = ref(0);
const sort = ref({column: "created_at", direction: "asc"});
const q = ref('');
const status = ref(false);
const pageCountList = [10, 15, 20, 25, 30]
const pageCountListSelected = ref(pageCountList[0])

const LoadingRecord = ref([])


const fetchLoadingRecord = async () => {
  status.value = true;
  LoadingRecord.value = []
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


const closeModal = () => {
  selectedDriver.value = null;
  newDriverData.value = {
    name: "",
    address: "",
    license_number: "",
  };
};

watch(page, fetchLoadingRecord);
watch(pageCountListSelected, fetchLoadingRecord);
watch(sort, fetchLoadingRecord);
watch(q, fetchLoadingRecord);
onMounted(fetchLoadingRecord);
const expand = ref({
  openedRows: [LoadingRecord],
  row: {}
})
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
        </div>
        <UTable
            :rows="LoadingRecord "
            :columns="columns"
            v-model:sort="sort"
            :loading="status"
            :loading-state="{ icon: 'i-heroicons-arrow-path-20-solid', label: 'در حال پردازش...' }"
            :progress="{ color: 'primary', animation: 'carousel' }"
            v-model:expand="expand"
        >
          <template #expand="{ row }">
            <pre class="p-4">
              {{ row }}
            </pre>
          </template>
          <template #expand-action="{ row, isExpanded, toggle }">
            <button class="btn btn-primary" @click="toggle">
              مشاهده جزعیات
              <Icon name="ic:outline-remove-red-eye" size="18"></Icon>
            </button>
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
  </div>
</template>

<style scoped>

</style>
