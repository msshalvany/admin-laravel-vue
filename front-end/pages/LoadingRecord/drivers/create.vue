<script setup>
// تعریف متغیرها با استفاده از ref
import {loaderfun} from "~/composables/statFunc.js";

const name = ref('');
const address = ref('');
const license_number = ref('');
const jwtCookie = useCookie('jwt');
// متد ارسال فرم
const submitForm = async () => {
  loaderfun()
  try {
    const response = await fetch(`${basUrl().value}/drivers/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${jwtCookie.value}`,
      },
      body: JSON.stringify({
        name: name.value,
        address: address.value,
        license_number: license_number.value,
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError('اطلاعات را صحیح وارد کنید')
    } else {
      // پاک‌سازی فرم بعد از موفقیت
      name.value = '';
      address.value = '';
      license_number.value = '';
      AlertSuccess('راننده با موفقیت اضافه شد')
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loaderfun()
};
</script>


<template>
  <div class="p-4">
    <div class="card shadow-md px-5 py-1 rounded-lg">
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
              <nuxt-link to="/LoadingRecord/drivers">
                <a>
                  <Icon name="healthicons:truck-driver" class="ml-1" size="18"/>
                  رانندگان
                </a>
              </nuxt-link>
            </li>
            <li>
              <a>
                <Icon name="hugeicons:user" size="18" class="ml-2"/>
                راننده جدید
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="p-8 m-auto flex justify-center items-center w-10/12 shadow-xl rounded-lg">
      <form @submit.prevent="submitForm" class="form-control w-full space-y-6">
        <!-- نام و نام خانوادگی -->
        <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="mdi:user" size="18" class="ml-2" />
        نام و نام خانوادگی
      </span>
          <input v-model="name" type="text" class="grow" placeholder="نام و نام خانوادگی" />
        </label>

        <!-- آدرس -->
        <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="mdi:map-marker-account" size="18" class="ml-2" />
        آدرس
      </span>
          <input v-model="address" type="text" class="grow" placeholder="آدرس" />
        </label>

        <!-- شماره گواهی نامه -->
        <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="fa6-solid:address-card" size="18" class="ml-2" />
        شماره گواهی نامه
      </span>
          <input v-model="license_number" type="text" class="grow" placeholder="شماره گواهی نامه" />
        </label>

        <!-- دکمه ثبت -->
        <button type="submit" class="btn btn-primary w-full mt-6">ثبت</button>
      </form>
    </div>
  </div>
</template>
