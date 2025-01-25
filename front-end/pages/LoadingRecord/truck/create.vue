<script setup>
import {loaderfun} from "~/composables/statFunc.js";
import {onMounted, ref} from "vue";
import {useCookie} from "nuxt/app";
import {setInterval} from "#app/compat/interval.js";

// متغیرهای فرم
const truckType = ['غیره', 'کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']
const type = ref(null);
const plate_number = ref('');
const color = ref('');
const weight = ref('');
const jwtCookie = useCookie('jwt');
const selectedDriver = ref(null);
const selectedCompany = ref(null);
const drivers = ref([]); // لیست رانندگان
const company = ref([]); // جستجو برای رانندگان


// تابع برای دریافت رانندگان و استخراج نام‌ها
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

const fetchCompany = async () => {
  try {
    const response = await fetch(
        `${basUrl().value}/companies`,
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
      company.value = data.data.map(item => ({
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

// متد ارسال فرم
const submitForm = async () => {
  loaderfun();
  try {
    const response = await fetch(`${basUrl().value}/trucks/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${jwtCookie.value}`,
      },
      body: JSON.stringify({
        plate_number: plate_number.value,
        color: color.value,
        type: type.value,
        weight: weight.value,
        driver_id: selectedDriver.value.value,  // فقط شناسه راننده ارسال می‌شود
        company_id: selectedCompany.value.value,  // فقط شناسه راننده ارسال می‌شود
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError('اطلاعات را صحیح وارد کنید');
    } else {
      // پاک‌سازی فرم بعد از موفقیت
      plate_number.value = '';
      color.value = '';
      type.value = null;
      selectedCompany.value = null
      weight.value = '';
      selectedDriver.value = null;
      AlertSuccess('کامیون با موفقیت اضافه شد');
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun();
};

// بارگذاری داده‌های رانندگان هنگام بارگذاری کامپوننت
onMounted(function () {
  fetchDrivers()
  fetchCompany()
});
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
      <div class="flex justify-between items-center mb-4">
        <div class="breadcrumbs text-sm">
          <ul class="flex items-center">
            <li>
              <nuxt-link to="/">
                <Icon name="ic:baseline-home" size="18" class="ml-1"/>
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
              <nuxt-link to="/LoadingRecord/truck">
                <a>
                  <Icon name="fa6-solid:truck" class="ml-2" style="vertical-align: -5px" size="15"/>
                  کامیون‌ها
                </a>
              </nuxt-link>
            </li>
            <li>
              <a>
                <Icon name="fa6-solid:truck-medical" size="15" class="ml-1"/>
                کامیون جدید
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="p-8 m-auto flex justify-center content-center w-10/12 shadow-xl">
      <form @submit.prevent="submitForm" class="form-control w-full">
        <fieldset class="fieldset w-xs bg-base-200 border border-base-300 p-4 rounded-box">
          <legend class="fieldset-legend">
            ماشین جدید
            <Icon name="fa6-solid:truck" class="ml-2" style="vertical-align: -5px" size="15"/>
          </legend>
          <!-- انتخاب راننده با قابلیت جستجو -->
          <USelectMenu v-model="selectedDriver" size="xl"
                       :options="drivers"
                       placeholder="انتخاب راننده"
                       :popper="{ arrow: true }"
                       searchable
                       icon="healthicons:truck-driver"
                       searchable-placeholder="جستجو....."
                       class="mt-2 w-full"
          />
          <USelectMenu v-model="selectedCompany" size="xl"
                       :options="company"
                       placeholder="انتخاب کمپانی"
                       :popper="{ arrow: true }"
                       searchable
                       icon="fa6-solid:truck"
                       searchable-placeholder="جستجو....."
                       class="mt-4"
          />

          <!--        <USelectMenu v-model="type" size="xl"-->
          <!--                     :options="truckType"-->
          <!--                     placeholder="انتخاب نوع ماشین"-->
          <!--                     :popper="{ arrow: true }"-->
          <!--                     searchable-->
          <!--                     searchable-placeholder="جستجو....."-->
          <!--                     class="mt-4 w-full"-->
          <!--        />-->
          <div class="mt-4">
            <div class="filter flex items-center justify-center">
              <label class="ml-2" for=""> نوع کامیون : </label>
              <input class="btn btn-square" type="reset" value="×"/>
              <input
                  class="btn"
                  type="radio"
                  v-model="type"
                  v-for="item of truckType"
                  :value="item"
                  :aria-label="item"
              />
            </div>
          </div>
          <!-- پلاک کامیون -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
          <span class="flex items-center">
            <Icon name="solar:plate-linear" size="18" class="ml-2"/>
            پلاک کامیون
          </span>
            <input v-model="plate_number" type="text" class="grow" placeholder="پلاک کامیون"/>
          </label>

          <!-- رنگ کامیون -->
          <label class="input input-bordered flex items-center gap-2 mt-4 w-3/12">
            <Icon name="material-symbols:colors" size="18" class="ml-2"/>
            <span>رنگ</span>
            <input v-model="color" type="color" class="grow" placeholder="رنگ"/>
          </label>

          <!-- نوع کامیون -->
          <!--        <label class="input input-bordered flex items-center gap-2 mt-4 w-full">-->
          <!--          <Icon name="material-symbols:directions-car-outline" size="18" class="ml-2"/>-->
          <!--          <input v-model="type" type="text" class="grow" placeholder="نوع کامیون"/>-->
          <!--        </label>-->

          <!-- وزن کامیون -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
          <span class="flex items-center">
            <Icon name="mdi:weight-kilogram" size="18" class="ml-2"/>
            وزن کامیون
          </span>
            <input v-model="weight" type="number" class="grow" placeholder="وزن کامیون"/>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary mt-4">ثبت</button>
        </fieldset>
      </form>
    </div>
  </div>
</template>
