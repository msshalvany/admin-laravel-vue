<script setup>
import {loaderfun} from "~/composables/statFunc.js";
import {onMounted, ref} from "vue";
import {useCookie} from "nuxt/app";
const { $toast } = useNuxtApp();

// متغیرهای فرم
const truckType = ['غیره', 'کامیون', 'تریلی', 'کامیونت', 'خاور', 'وانت']
const type = ref(null);

const plate_right = ref('');
const plate_char = ref('');
const plate_left = ref('');
const plate_city = ref('');

const color = ref('');
const weight = ref('');
const jwtCookie = useCookie('jwt');
const selectedDriver = ref();
const selectedCompany = ref(null);
const drivers = ref([]); // لیست رانندگان
const company = ref([]); // جستجو برای رانندگان
let errors = ref([])


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
        text: item.name,
        id: item.id
      }));
      console.log(drivers.value)
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
        text: item.name,
        id: item.id
      }));
      console.log(company.value)
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
        plate_city: plate_city.value,
        plate_left: plate_left.value,
        plate_char: plate_char.value,
        plate_right: plate_right.value,
        color: color.value,
        type: type.value,
        weight: weight.value,
        driver_id: selectedDriver.value,  // فقط شناسه راننده ارسال می‌شود
        company_id: selectedCompany.value,  // فقط شناسه راننده ارسال می‌شود
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      errors.value = errorData.errors
      $toast('اطلاعات را صحیح وارد کنید', {
        "theme": "colored",
        "type": "error",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
    } else {
      // پاک‌سازی فرم بعد از موفقیت
      color.value = '';
      type.value = null;
      selectedCompany.value = null
      weight.value = '';
      selectedDriver.value = null;
      $toast('کامیون با موفقیت اضافه شد', {
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
};

// بارگذاری داده‌های رانندگان هنگام بارگذاری کامپوننت
onMounted(function () {
  fetchDrivers()
  fetchCompany()
});
watch(selectedDriver, () => {
  console.log(selectedDriver.value)
})
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
      <div class="flex justify-between items-center mb-4">
        <div class="breadcrumbs text-sm">
          <ul class="flex items-center">
            <li>
              <nuxt-link to="/front-end/public">
                <Icon name="ic:baseline-home" size="18" class="ml-1"/>
                خانه
              </nuxt-link>
            </li>
            <li>
              <nuxt-link to="/truck">
                <a>
                  <Icon name="ph:truck-duotone" class="ml-2" style="vertical-align: -5px" size="18"/>
                  کامیون‌ها
                </a>
              </nuxt-link>
            </li>
            <li>
              <a>
                <Icon name="fa6-solid:truck-medical" size="14" class="ml-1"/>
                کامیون جدید
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="p-4 md:p-8 m-auto w-full max-w-6xl shadow-xl">
      <form @submit.prevent="submitForm" class="form-control w-full">
        <fieldset class="w-full bg-base-200 border border-base-300 p-6 rounded-lg shadow-xl">
          <legend class="text-xl font-semibold mb-6 flex items-center gap-2">
            <Icon name="fa6-solid:truck" size="20"/>
            ماشین جدید
          </legend>

          <!-- انتخاب راننده و کمپانی -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <SearchableSelect
                v-model="selectedDriver"
                :items="drivers"
                label="راننده"
                placeholder="انتخاب راننده"
            >
              <template #icon>
                <Icon name="healthicons:truck-driver" size="20" class="text-gray-500"/>
              </template>
            </SearchableSelect>

            <SearchableSelect
                v-model="selectedCompany"
                :items="company"
                label="کمپانی"
                placeholder="انتخاب کمپانی"
            >
              <template #icon>
                <Icon name="material-symbols:add-circle" size="20" class="text-gray-500"/>
              </template>
            </SearchableSelect>
          </div>

          <!-- نوع کامیون -->
          <div class="mt-6">
            <label class="block mb-2 text-sm font-medium">نوع کامیون:</label>
            <div class="filter flex flex-wrap gap-2 items-center">
              <input class="btn btn-error btn-square" type="reset" value="×"/>
              <input
                  class="btn btn-primary"
                  type="radio"
                  v-model="type"
                  v-for="item of truckType"
                  :key="item"
                  :value="item"
                  :aria-label="item"
              />
            </div>
          </div>
          <!-- اطلاعات کامیون -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <!-- پلاک -->
            <!--            <label class="floating-label input input-bordered flex items-center gap-2">-->
            <!--              <Icon name="solar:plate-linear" size="18" />-->
            <!--              <span>پلاک</span>-->
            <!--              <input v-model="plate_full" type="text" class="grow" placeholder="پلاک کامیون"/>-->
            <!--            </label>-->

            <!-- رنگ -->
            <label class="input input-bordered flex items-center gap-2">
              <Icon name="material-symbols:colors" size="18"/>
              <span>رنگ</span>
              <input v-model="color" type="color" class="grow"/>
            </label>

            <!-- وزن -->
            <label class="floating-label input input-bordered flex items-center gap-2">
              <Icon name="mdi:weight-kilogram" size="18"/>
              <span>وزن</span>
              <input v-model="weight" type="number" class="grow" placeholder="وزن کامیون"/>
            </label>
          </div>
          <!-- پلاک کامیون با استایل خاص -->
          <div class="mt-6">
            <label class="block mb-2 text-sm font-medium">پلاک کامیون:</label>
            <div
                class="flex items-center w-full max-w-md h-[70px]  border-4 border-black rounded-md shadow-md overflow-hidden text-center font-bold text-lg">

              <!-- نوار آبی سمت چپ -->
              <div class="flex flex-col items-center justify-between w-12 h-full bg-blue-700 text-white p-1 text-xs">
                <img
                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Flag_of_Iran.svg/20px-Flag_of_Iran.svg.png"
                    alt="پرچم ایران" class="w-full h-auto">
                <span>I.R.</span>
                <span>IRAN</span>
              </div>

              <!-- شماره پلاک -->
              <div dir="rtl" class="flex items-center justify-center flex-1 gap-1 px-2">
                <input v-model="plate_right" type="text" maxlength="2"
                       class="w-10 border border-gray-300 rounded text-center" placeholder="12"/>
                <input v-model="plate_char" type="text" maxlength="3"
                       class="w-8 border border-gray-300 rounded text-center" placeholder="الف"/>
                <input v-model="plate_left" type="text" maxlength="3"
                       class="w-16 border border-gray-300 rounded text-center" placeholder="345"/>
              </div>

              <!-- کد استان -->
              <div
                  class="flex flex-col items-center justify-between w-12 h-full border-l border-black px-1 py-1 text-[11px] leading-none">
                <input v-model="plate_city" type="text" maxlength="2"
                       class="w-full border border-gray-300 rounded text-center text-sm" placeholder="51"/>
                <span class="text-[10px] mt-auto">ایـــران</span>
              </div>
            </div>
          </div>
          <div v-if="errors.length != 0" role="alert" class="mt-2 alert alert-error alert-soft flex flex-col items-start">
          <span v-for="item in errors">
            {{ item[0] }}
          </span>
          </div>
          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary mt-6 w-full">ثبت</button>
        </fieldset>
      </form>
    </div>
  </div>
</template>
