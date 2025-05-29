<script setup>
// تعریف متغیرها با استفاده از ref
import {loaderfun} from "~/composables/statFunc.js";
const { $toast } = useNuxtApp();

const name = ref('');
const address = ref('');
const license_number = ref('');
const jwtCookie = useCookie('jwt');

let errors = ref([])

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
      name.value = '';
      address.value = '';
      license_number.value = '';
      $toast('راننده با موفقیت اضافه شد', {
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
  loaderfun()
};
</script>


<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
      <div class="flex justify-between items-center mb-4">
        <div class="breadcrumbs text-sm">
          <ul class="flex items-center">
            <li>
              <nuxt-link to="/front-end/public">
                <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                خانه
              </nuxt-link>
            </li>
            <li>
              <nuxt-link to="/drivers">
                <a>
                  <Icon name="healthicons:truck-driver" class="ml-1" size="18"/>
                  رانندگان
                </a>
              </nuxt-link>
            </li>
            <li>
              <a>
                <Icon name="streamline:user-add-plus" size="18" class="ml-2"/>
                راننده جدید
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="p-8 m-auto flex justify-center items-center w-10/12 shadow-xl rounded-lg">
      <form @submit.prevent="submitForm" class="form-control w-full space-y-6">
        <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
          <legend class="fieldset-legend">
            راننده جدید
            <Icon name="healthicons:truck-driver" class="ml-1" size="18"/>
          </legend>
          <!-- نام و نام خانوادگی -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="mdi:user" size="18" class="ml-2"/>
        نام و نام خانوادگی
      </span>
            <input v-model="name" type="text" class="grow" placeholder="نام و نام خانوادگی"/>
          </label>

          <!-- آدرس -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="mdi:map-marker-account" size="18" class="ml-2"/>
        آدرس
      </span>
            <input v-model="address" type="text" class="grow" placeholder="آدرس"/>
          </label>

          <!-- شماره گواهی نامه -->
          <label class="floating-label input input-bordered flex items-center gap-4 mt-4 w-full">
      <span class="flex items-center">
        <Icon name="fa6-solid:address-card" size="18" class="ml-2"/>
        شماره گواهی نامه
      </span>
            <input v-model="license_number" type="text" class="grow" placeholder="شماره گواهی نامه"/>
          </label>
          <div v-if="errors.length != 0" role="alert" class="alert alert-error alert-soft flex flex-col items-start">
          <span v-for="item in errors">
            {{ item[0] }}
          </span>
          </div>
          <!-- دکمه ثبت -->
          <button type="submit" class="btn btn-primary w-full mt-6">ثبت</button>
        </fieldset>
      </form>
    </div>
  </div>
</template>
