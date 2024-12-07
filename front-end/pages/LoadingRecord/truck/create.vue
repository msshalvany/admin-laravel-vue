<script setup>
import { loaderfun } from "~/composables/statFunc.js";
import { onMounted, ref } from "vue";
import { useCookie } from "nuxt/app";

// متغیرهای فرم
const plate_number = ref('');
const color = ref('');
const type = ref('');
const weight = ref('');
const jwtCookie = useCookie('jwt');
const selectedDriver = ref(null); // فقط شناسه راننده ذخیره می‌شود
const drivers = ref([]); // لیست رانندگان
const query = ref(''); // جستجو برای رانندگان

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
      // فقط نام رانندگان و شناسه‌ها را استخراج می‌کنیم
      drivers.value = data.map(item => ({
        label: item.name,  // نام راننده برای نمایش
        value: item.id    // شناسه راننده برای ارسال
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
      type.value = '';
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
onMounted(fetchDrivers);
</script>

<template>
  <div>
    <div class="breadcrumbs text-sm p-4">
      <ul>
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
          <nuxt-link to="/LoadingRecord/trucks">
            <a>
              <Icon name="healthicons:truck-driver" class="ml-1" size="18"/>
              کامیون‌ها
            </a>
          </nuxt-link>
        </li>
        <li>
          <a>
            <Icon name="fa6-solid:truck" size="18" class="ml-2"/>
            کامیون جدید
          </a>
        </li>
      </ul>
    </div>

    <div class="p-8 m-auto w-10/12">
      <form @submit.prevent="submitForm">
        <!-- انتخاب راننده با قابلیت جستجو -->
        <label class="items-center gap-4 mt-4">
          <UInputMenu v-model="selectedDriver" size="xl"
                      :options="drivers"
                      placeholder="انتخاب راننده"
                      :popper="{ arrow: true }"
                      filterable/> <!-- ویژگی جستجو فعال -->
        </label>

        <!-- پلاک کامیون -->
        <label class="input input-bordered flex items-center gap-4 mt-4">
          <Icon name="solar:plate-linear" size="18" class="ml-2"/>
          <input v-model="plate_number" type="text" class="grow" placeholder="پلاک کامیون"/>
        </label>

        <!-- رنگ کامیون -->
        <label class="input input-bordered flex items-center gap-2 mt-4 w-2/12">
          <Icon name="material-symbols:colors" size="18" class="ml-2"/>
          <span>رنگ</span>
          <input v-model="color" type="color" class="grow" placeholder="رنگ"/>
        </label>

        <!-- نوع کامیون -->
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="material-symbols:directions-car-outline" size="18" class="ml-2"/>
          <input v-model="type" type="text" class="grow" placeholder="نوع کامیون"/>
        </label>

        <!-- وزن کامیون -->
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="mdi:weight-kilogram" size="18" class="ml-2"/>
          <input v-model="weight" type="number" class="grow" placeholder="وزن کامیون"/>
        </label>

        <!-- دکمه ارسال -->
        <button type="submit" class="btn btn-primary mt-4">ثبت</button>
      </form>
    </div>
  </div>
</template>
