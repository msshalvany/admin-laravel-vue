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
    <div class="p-8 m-auto w-10/12">
      <form @submit.prevent="submitForm">
        <label class="input input-bordered flex items-center gap-4 mt-4">
          <Icon name="mdi:user" size="18" class="ml-2"/>
          <input v-model="name" type="text" class="grow" placeholder="نام و نام خانوادگی"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
            <Icon name="mdi:map-marker-account" size="18" class="ml-2"/>
          <input v-model="address" type="text" class="grow" placeholder="آدرس"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="fa6-solid:address-card" size="18" class="ml-2"/>
          <input v-model="license_number" type="text" class="grow" placeholder="شماره گواهی نامه"/>
        </label>
        <button type="submit" class="btn btn-primary mt-4">ثبت</button>
      </form>
    </div>
  </div>
</template>
