<script setup>
import {basUrl} from "~/composables/states.js";
import {ref} from "vue";

const entry_date = ref(null)
const entry_time = ref(null)
const companies = ref([]);
const selectedCompany = ref(null);
const pendingAll = ref(null);
const trucks = ref([]);
const selectedTrucks = ref(null)
const empty_weight = ref(0.00)
const selectedStar = ref(2)
const exit_time = ref(null)
const locations = ref([])
const selectedLocation = ref([])
const showDeleteConfirmation = ref(false);
const showComplateConfirmation = ref(false);
const loaded_weight = ref(0)

const selectedLoading = ref(null);
const DeleteLoading = (item) => {
  selectedLoading.value = item
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};
const closeModal = () => {
  selectedLoading.value = null;
  showDeleteConfirmation.value = false;
  showComplateConfirmation.value = false;
};

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
        `${basUrl().value}/drivers/all`,
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
    const response = await fetch(`${basUrl().value}/trucks/all`, {
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
const fetchpendingAll = async () => {
  loaderfun()
  try {
    const response = await fetch(`${basUrl().value}/loading_records/pendingAll`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });
    if (response.ok) {
      const data = await response.json();
      pendingAll.value = data.data
      console.log()
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun()
};
const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/loading_records/destroy/${selectedLoading.value.id}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      fetchpendingAll()
      AlertSuccess("تردد با موفقیت حذف شد");
      showDeleteConfirmation.value = false;
      selectedLoading.value = null;
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

const EndSubmit = (item) => {
  selectedLoading.value = item
  showComplateConfirmation.value = true
}

const checkForm = () => {
  if (selectedCompany.value == null) {
    AlertError('کمپانی را انتخاب کنید')
  }
  if (selectedTrucks.value == null) {
    AlertError('ماشین را انتخاب کنید')
  }
  if (selectedDriver.value == null) {
    AlertError('راننده را انتخاب کنید')
  }
  if (selectedLocation.value == null) {
    AlertError('مکان را انتخاب کنید')
  }
  if (entry_date.value == null) {
    AlertError('تاریخ تردد را انتخاب کنید')
  }
  if (entry_time.value == null) {
    AlertError('زمان ورود را انتخاب کنید')
  }
}
const LoadingRecords = async () => {
  loaderfun();
  const selectedLocationIds = selectedLocation.value.map(function (item) {
    return item.value
  }); // استخراج آرایه از شناسه‌ها
  try {
    checkForm()
    const response = await fetch(`${basUrl().value}/loading_records/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        truck_id: selectedTrucks.value.value,
        location_ids: selectedLocationIds,
        company_id: selectedCompany.value.value,
        driver_id: selectedDriver.value.value,
        empty_weight: selectedDriver.value.value,
        entry_date: entry_date.value,
        entry_time: entry_time.value
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError(response.error[0]);
    } else {
      AlertSuccess('تردد به شکل موقت ثبت شد');
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun();
}

const finalSubmit = async () => {
  loaderfun();
  try {
    const response = await fetch(`${basUrl().value}/loading_records/finalStore`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        driver_star: selectedStar.value,
        exit_time: exit_time.value,
        loaded_weight: loaded_weight.value,
        id: selectedLoading.value.id
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError(response.error[0]);
    } else {
      AlertSuccess('تردد به شکل نهایی ثبت شد');
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun();
  fetchpendingAll()
  closeModal()
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
const TabItems = [{
  label: 'ثبت موقت',
  icon: 'material-symbols:pending-actions',
  key: 'pending'
}, {
  label: 'ثبت نهایی',
  icon: 'material-symbols:weekend-outline',
  key: 'final'
}]
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
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
      <UTabs @change="fetchpendingAll" :items="TabItems" class="w-full">
        <template #icon="{ item, selected }">
          <UIcon :name="item.icon" class="w-4 h-4 flex-shrink-0 me-2"
                 :class="[selected && 'text-primary-500 dark:text-primary-400']"/>
        </template>
        <template #item="{ item }">
          <div v-if="item.key === 'pending'" class="space-y-3">
            <div class="form-control w-full space-y-6">
              <fieldset class="fieldset w-xs bg-base-200 border border-base-300 p-4 rounded-box">
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
                      <input id="timeStartPicker" type="text" class="grow" placeholder=" ساعت شروع"/>
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
                                 multiple
                                 icon="ic:outline-place"
                                 searchable-placeholder="جستجو....."
                                 class="mt-2 "
                    >
                      <template #label>
                        <template v-if="selectedLocation.length">
                          <span>{{ selectedLocation.length }} مکان انتخاب شده</span>
                        </template>
                      </template>
                    </USelectMenu>
                  </div>
                </div>
                <div class="mt-12 flex flex-wrap justify-between items-center">
                  <button class="btn btn-primary btn-lg">
                    وزن گیری
                    <span class="loading loading-spinner"></span>
                  </button>
                  <div style="font-family: digitalNumber" class="mt-5 px-4 pb-6 bg-gray-800 rounded-2xl">
                    <div class="text-red-700 shadow-amber-400 text-9xl flex justify-center items-center"
                         style="letter-spacing: 6px">
                      {{ empty_weight }}
                    </div>
                  </div>
                  <input type="hidden" v-model="empty_weight">
                </div>
                <div class="mt-4">
                  <button class="w-full btn btn-primary" @click="LoadingRecords">ثبت موقت ورود</button>
                </div>
              </fieldset>
            </div>
          </div>
          <div v-else-if="item.key === 'final'" class="space-y-3">
            <div v-if="pendingAll==0">
              <div class="alert-error alert alert-soft">
                داده ای وجود ندارد !
                <Icon name="ic:baseline-error" size="18"/>
              </div>
            </div>
            <ul class="list bg-base-100 rounded-box shadow-md" v-for="item in pendingAll">
              <li class="w-full list-row">
                <div class="list-row">
                  <div class="list-col-grow">
                    <div><b>نام کمپانی :</b>{{ item.company.name }}</div>
                    <div><b>تاریخ ثبت:</b> {{ item.created_at }} <b> ساعت :</b> {{ item.entry_time }}</div>
                    <div><b> راننده:</b> {{ item.driver.name }}</div>
                    <div>
                      <p><b>مکان ها :</b></p>
                      <ol v-for="loc in item.locations">
                        <li>{{ loc.location_name }}</li>
                      </ol>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-primary" @click="EndSubmit(item)">
                    ثبت نهایی
                    <Icon name="carbon:task-complete" size="18"/>
                  </button>
                  <button class="btn btn-error btn-sm" @click="DeleteLoading(item)">
                    لغو
                    <Icon name="material-symbols:auto-delete" size="18"/>
                  </button>
                </div>
              </li>
            </ul>
          </div>
        </template>
      </UTabs>
    </div>
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </div>
      </div>
    </div>
    <div v-if="showComplateConfirmation" class="modal modal-open">
      <div class="modal-box w-11/12 max-w-5xl" style="height: 450px">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <div class="form-control w-full space-y-6">
          <fieldset class="fieldset w-xs bg-base-200 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend">
              ثبت نهایی
              <Icon name="material-symbols:weekend-outline" size="18"></Icon>
            </legend>
            <div>
              <div class="mt-4">
                <label class="floating-label input input-bordered flex items-center gap-2">
            <span class="flex items-center">
            <Icon name="material-symbols-light:calendar-clock-rounded" size="24"/>
              ساعت پایان
            </span>
                  <input id="timeEndPicker" type="text" class="grow" placeholder=" ساعت پایان"/>
                </label>
                <date-picker
                    type="time"
                    v-model="exit_time"
                    custom-input="#timeEndPicker"
                />
              </div>
              <div class="mt-2 flex flex-wrap justify-between items-center">
                <button class="btn btn-primary btn-lg">
                  وزن گیری
                  <span class="loading loading-spinner"></span>
                </button>
                <div style="font-family: digitalNumber" class="mt-4 px-4 pb-6 bg-gray-800 rounded-2xl">
                  <div class="text-red-700 shadow-amber-400 text-9xl flex justify-center items-center"
                       style="letter-spacing: 6px">
                    {{ loaded_weight }}
                  </div>
                </div>
                <input type="hidden" v-model="loaded_weight">
              </div>
            </div>
            <div class="mt-3 flex flex-wrap">
              <div>
                امتیاز راننده :
              </div>
              <div class="rating rating-lg rating-half">
                <input v-model="selectedStar" type="radio" name="rating-11" class="rating-hidden"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-1 bg-orange-400" aria-label="0.5 star" value="0.5"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-2 bg-orange-400" aria-label="1 star" value="1"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-1 bg-orange-400" aria-label="1.5 star" value="1.5"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-2 bg-orange-400" aria-label="2 star" value="2"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-1 bg-orange-400" aria-label="2.5 star" value="2.5"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-2 bg-orange-400" aria-label="3 star" value="3"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-1 bg-orange-400" aria-label="3.5 star" value="3.5"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-2 bg-orange-400" aria-label="4 star" value="4"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-1 bg-orange-400" aria-label="4.5 star" value="4.5"/>
                <input v-model="selectedStar" type="radio" name="rating-11"
                       class="mask mask-star-2 mask-half-2 bg-orange-400" aria-label="5 star" value="5"/>
              </div>
            </div>
            <div class="mt-4 absolute text-center bottom-4" style="width: 94%;">
              <button class="btn btn-primary w-full" @click="finalSubmit">ثبت نهایی تردد</button>
            </div>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media only screen and (max-width: 600px) {
  .list-row {
    display: flex !important;
    flex-wrap: wrap !important;
    margin: 0 0 12px 0;
  }
}
</style>