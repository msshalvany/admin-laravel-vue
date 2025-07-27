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
const { $toast } = useNuxtApp();
let errors = ref([])

const selectedLoading = ref(null);
const DeleteLoading = (item) => {
  selectedLoading.value = item
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
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
        text: item.name,  // نام راننده برای نمایش
        id: item.id    // شناسه راننده برای ارسال
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
        text: item.name,
        id: item.id
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
        text: item.location_name,  // نام راننده برای نمایش
        id: item.id    // شناسه راننده برای ارسال
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
        let plate_full = `شماره پلاک :${item.plate_full}`
        return {
          text: plate_full,
          id: item.id,
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
      $toast('تردد با موفقیت حذف شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
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


const LoadingRecords = async () => {
  loaderfun();
  const selectedLocationIds = selectedLocation.value.map(function (item) {
    return item
  }); // استخراج آرایه از شناسه‌ها
  try {
    console.log()
    const response = await fetch(`${basUrl().value}/loading_records/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        truck_id: selectedTrucks.value,
        location_ids: selectedLocationIds,
        company_id: selectedCompany.value,
        driver_id: selectedDriver.value,
        empty_weight: selectedDriver.value,
        entry_date: entry_date.value,
        entry_time: entry_time.value
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      errors.value = errorData.errors

    } else {
      $toast('تردد به شکل موقت ثبت شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
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
    } else {
      $toast('تردد به شکل نهایی ثبت شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun();
  fetchpendingAll()
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
            <NuxtLink to="/truck/create">
              <button class="btn btn-success flex items-center">
                <span> کامیون جدید</span>
                <Icon name="material-symbols-add-circle" size="18"/>
              </button>
            </NuxtLink>
            <NuxtLink to="/drivers/create">
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
      <div class="tabs tabs-lift">
        <label class="tab">
          <input type="radio" name="my_tabs_4" checked="checked"/>
          ثبت موقت
          <icon name="material-symbols:pending-actions" size="20" />
        </label>
        <div class="tab-content bg-base-100 border-base-300 p-6">
          <div class="form-control w-full space-y-6">
            <fieldset class="w-full bg-base-200 border border-base-300 p-6 rounded-lg shadow-xl">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- کمپانی -->
                <SearchableSelect
                    v-model="selectedCompany"
                    :items="companies"
                    label="کمپانی"
                    placeholder="انتخاب کمپانی"
                >
                  <template #icon>
                    <Icon name="material-symbols:add-circle" size="20" class="text-gray-500" />
                  </template>
                </SearchableSelect>

                <!-- ماشین -->
                <SearchableSelect
                    v-model="selectedTrucks"
                    :items="trucks"
                    label="ماشین"
                    placeholder="انتخاب ماشین"
                >
                  <template #icon>
                    <Icon name="healthicons:truck-driver" size="20" class="text-gray-500" />
                  </template>
                </SearchableSelect>

                <!-- راننده -->
                <SearchableSelect
                    v-model="selectedDriver"
                    :items="drivers"
                    label="راننده"
                    placeholder="انتخاب راننده"
                >
                  <template #icon>
                    <Icon name="healthicons:truck-driver" size="20" class="text-gray-500" />
                  </template>
                </SearchableSelect>

                <!-- تاریخ تردد -->
                <div>
                  <label class="floating-label input input-bordered flex items-center gap-2">
        <span class="flex items-center">
          <Icon name="material-symbols-light:calendar-clock-rounded" size="24" />
          تاریخ تردد
        </span>
                    <input id="datePicker" type="text" class="grow" placeholder="تاریخ تردد" />
                  </label>
                  <date-picker v-model="entry_date" type="date" custom-input="#datePicker" />
                </div>

                <!-- ساعت شروع -->
                <div>
                  <label class="floating-label input input-bordered flex items-center gap-2">
        <span class="flex items-center">
          <Icon name="material-symbols-light:calendar-clock-rounded" size="24" />
          ساعت شروع
        </span>
                    <input id="timeStartPicker" type="text" class="grow" placeholder=" ساعت شروع" />
                  </label>
                  <date-picker type="time" v-model="entry_time" custom-input="#timeStartPicker" />
                </div>

                <!-- انتخاب مکان -->
                <SearchableComboBox
                    v-model="selectedLocation"
                    :items="locations"
                    label="مکان"
                    placeholder="انتخاب مکان"
                >
                  <template #icon>
                    <Icon name="ic:outline-place" size="20" class="text-gray-500" />
                  </template>
                </SearchableComboBox>
              </div>

              <!-- وزن و دکمه‌ها -->
              <div class="mt-10 flex flex-col flex-wrap lg:flex-row items-center justify-between gap-6">
                <button class="btn btn-primary btn-lg w-full lg:w-auto">
                  وزن گیری
                  <span class="loading loading-spinner"></span>
                </button>

                <div class="px-6 py-4 bg-gray-800 rounded-2xl" style="font-family: digitalNumber">
                  <div class="text-red-700 text-9xl text-center" style="letter-spacing: 6px">
                    {{ empty_weight }}
                  </div>
                </div>

                <input type="hidden" v-model="empty_weight" />
              </div>
              <div v-if="errors.length != 0" role="alert" class="alert alert-error alert-soft flex flex-col items-start">
              <span v-for="item in errors">
                {{ item[0] }}
              </span>
              </div>
              <div class="mt-6">
                <button class="w-full btn btn-primary" @click="LoadingRecords">ثبت موقت ورود</button>
              </div>
            </fieldset>
          </div>
        </div>
        <label class="tab">
          <input type="radio" name="my_tabs_4" @change="fetchpendingAll" />
          ثبت نهایی
          <icon name="material-symbols:weekend-outline" size="20" />
        </label>
        <div class="tab-content bg-base-100 border-base-300 p-6">
          <div class="form-control w-full space-y-6">
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
                    <div><b>نام کمپانی :</b>{{ item.company.name ?? ''}}</div>
                    <div><b>تاریخ ثبت:</b> {{ item.created_at }} <b> ساعت :</b> {{ item.entry_time }}</div>
                    <div><b> راننده:</b> {{ item.driver.name ?? ''}}</div>
                    <div>
                      <p><b>مکان ها :</b></p>
                      <ol v-for="loc in item.locations">
                        <li>{{ loc.location_name }}</li>
                      </ol>
                    </div>
                  </div>
                  <button class="btn btn-sm btn-primary" @click="EndSubmit(item)"onclick="ComplateConfirmation_modal.showModal()" >
                    ثبت نهایی
                    <Icon name="carbon:task-complete" size="18"/>
                  </button>
                  <button class="btn btn-error btn-sm" @click="DeleteLoading(item)" onclick="delete_modal.showModal()">
                    لغو
                    <Icon name="material-symbols:auto-delete" size="18"/>
                  </button>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>


















    </div>
    <dialog id="delete_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟</h2>
        <form method="dialog">
          <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
            <Icon name="material-symbols:close"/>
          </button>
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </form>
      </div>
    </dialog>
    <dialog id="ComplateConfirmation_modal" class="modal">
      <div class="modal-box w-11/12 max-w-5xl" style="height: 450px">
        <br>
        <div class="form-control w-full space-y-6">
          <fieldset class="w-full bg-base-200 border border-base-300 p-6 rounded-lg shadow-xl">
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
              <div class="mt-2 flex flex-wrap flex-wrap justify-between items-center">
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
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button class="btn btn-primary w-full" @click="finalSubmit">ثبت نهایی تردد</button>
            </form>
          </fieldset>
        </div>
      </div>
    </dialog>
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