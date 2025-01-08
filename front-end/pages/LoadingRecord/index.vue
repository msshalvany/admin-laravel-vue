<script setup>
import {basUrl} from "~/composables/states.js";
import {ref} from "vue";
import {loaderfun} from "~/composables/statFunc.js";

const entry_date = ref(null)
const entry_time = ref(null)
const companies = ref([]);
const selectedCompany = ref(null);

const trucks = ref([]);
const selectedTrucks = ref(null)

const locations = ref([])
const selectedLocation = ref(null)


const drivers = ref([])
const selectedDriver = ref(null)
const fetchCompanies = async () => {
  try {
    const response = await fetch(`${basUrl().value}/companies`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });
    if (response.ok) {
      const data = await response.json();
      companies.value = data.data.map(item => ({
        label: item.name,  // نام راننده برای نمایش
        value: item.id    // شناسه راننده برای ارسال
      }));
    }
  } catch (error) {
    console.error('Error:', error);
  }
};
const fetchDrivers = async () => {
  try {
    const response = await fetch(
        `${basUrl().value}/drivers`,
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
      drivers.value = data.data.map(item => ({
        label: item.name,
        value: item.id
      }));
    } else {
      console.error("Error fetching drivers:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};
const fetchLocation = async () => {
  try {
    const response = await fetch(`${basUrl().value}/location`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      locations.value = data.data.map(item => ({
        label: item.location_name,  // نام راننده برای نمایش
        value: item.id    // شناسه راننده برای ارسال
      }));
    } else {
      console.error('Error fetching location:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};
const fetchTrucks = async () => {
  try {
    const response = await fetch(`${basUrl().value}/trucks`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });
    if (response.ok) {
      const data = await response.json();
      trucks.value = data.data.map((item) => {
        let plate_number = `شماره پلاک :${item.plate_number}`
        return {
          label: plate_number,
          value: item.id,
          color: item.color
        }
      });
      console.log(trucks.value)
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

const LoadingRecords = async () => {
  loaderfun();
  try {
    const response = await fetch(`${basUrl().value}/loading_records/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        truck_id: selectedTrucks.value.value,
        location_id: selectedLocation.value.value,
        company_id: selectedCompany.value.value,
        driver_id: selectedDriver.value.value,
        empty_weight: selectedDriver.value.value,
        entry_date: entry_date.value,
        entry_time:entry_time.value
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError(response.error[0]);
    } else {
      AlertSuccess('کامیون با موفقیت اضافه شد');
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun();
}

onMounted(function () {
  fetchCompanies()
  fetchLocation()
  fetchTrucks()
  fetchDrivers()
})
watch(selectedCompany, function () {

})
watch(selectedTrucks, function () {
  console.log(selectedTrucks.value)
})
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 py-1 rounded-lg">
      <div class="flex flex-wrap justify-between items-center mb-4">
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
              <a>
                <Icon name="mdi:truck-fast-outline" size="18"/>
                ثبت تردد
              </a>
            </li>
          </ul>
        </div>
        <div class="text-left">
          <div class="flex">
            <NuxtLink to="/LoadingRecord/truck/create">
              <button class="btn btn-success flex items-center">
                <span> کامیون جدید</span>
                <Icon name="material-symbols-add-circle" size="18"/>
              </button>
            </NuxtLink>
            <NuxtLink to="/LoadingRecord/drivers/create">
              <button class="btn btn-error">
                راننده جدید
                <Icon name="material-symbols-add-circle" size="18"/>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <div class="xl:w-8/12 md:w-12/12 p-4 m-auto flex flex-col mt-4 shadow-xl rounded-2xl">
      <div role="tablist" class="tabs tabs-box">
        <input checked="radio" type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="ثبت موقت"/>
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
          <div class="mt-2">
            <div>
              <USelectMenu v-model="selectedCompany" size="xl"
                           :options="companies"
                           placeholder="انتخاب شرکت"
                           :popper="{ arrow: true }"
                           searchable
                           icon="octicon:organization-16"
                           searchable-placeholder="جستجو....."
                           class="p-0"
              />
            </div>
            <div class="mt-4">
              <USelectMenu v-model="selectedTrucks" size="xl"
                           :options="trucks"
                           placeholder="انتخاب ماشین"
                           :popper="{ arrow: true }"
                           searchable
                           icon="ph:truck-duotone"
                           searchable-placeholder="جستجو....."
                           class="mt-2 "
              >
                <template #option="{ option:truck }">
                  <span class="h-2 w-2 rounded-full" :style="`background-color:${truck.color}`"></span>
                  <span class="truncate">{{ truck.label }}</span>
                </template>
              </USelectMenu>
            </div>
            <div class="mt-4">
              <USelectMenu v-model="selectedDriver" size="xl"
                           :options="drivers"
                           placeholder="انتخاب راننده"
                           :popper="{ arrow: true }"
                           searchable
                           icon="healthicons:truck-driver"
                           searchable-placeholder="جستجو....."
                           class="mt-2 w-full"
              />
            </div>
            <div class="mt-6">
              <label class="floating-label input input-bordered flex items-center gap-2">
            <span class="flex items-center">
            <Icon name="material-symbols-light:calendar-clock-rounded" size="24"/>
              تاریخ تردد
            </span>
                <input id="datePicker" type="text" class="grow" placeholder="تاریخ تردد"/>
              </label>
              <date-picker
                  v-model="entry_date"
                  type="date"
                  custom-input="#datePicker"
              />
            </div>
            <div class="mt-4">
              <label class="floating-label input input-bordered flex items-center gap-2">
            <span class="flex items-center">
            <Icon name="material-symbols-light:calendar-clock-rounded" size="24"/>
              ساعت شروع
            </span>
                <input id="timeStartPicker" type="text" class="grow" placeholder=" ساعت پایان"/>
              </label>
              <date-picker
                  type="time"
                  v-model="entry_time"
                  custom-input="#timeStartPicker"
              />
            </div>
            <div class="mt-4">
              <USelectMenu v-model="selectedLocation" size="xl"
                           :options="locations"
                           placeholder="انتخاب مکان"
                           :popper="{ arrow: true }"
                           searchable
                           icon="ic:outline-place"
                           searchable-placeholder="جستجو....."
                           class="mt-2 "
              >
              </USelectMenu>
            </div>
          </div>
          <div class="mt-12 flex flex-wrap justify-between items-center">
            <button class="btn btn-primary btn-lg">
              وزن گیری
              <span class="loading loading-spinner"></span>
            </button>
            <div class="flex justify-evenly lg:w-6/12">
              <div class="flex flex-col items-center justify-center bg-black text-white rounded-lg w-20 h-24">
                <p class="text-3xl font-bold">00</p>
                <span class="text-sm mt-1">گرم</span>
              </div>
              <div class="flex flex-col items-center justify-center bg-black text-white rounded-lg w-20 h-24">
                <p class="text-3xl font-bold">00</p>
                <span class="text-sm mt-1">کیلو</span>
              </div>
              <div class="flex flex-col items-center justify-center bg-black text-white rounded-lg w-20 h-24">
                <p class="text-3xl font-bold">00</p>
                <span class="text-sm mt-1">تن</span>
              </div>
            </div>
          </div>
          <div class="mt-4">
            <button class="w-full btn btn-primary" @click="LoadingRecords">ثبت موقت ورود</button>
          </div>
        </div>
        <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="ثبت نهایی"/>
        <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
          ثبت نهایی
        </div>
      </div>


    </div>
    <!-- جدول رانندگان -->
  </div>
</template>

<style scoped>

</style>