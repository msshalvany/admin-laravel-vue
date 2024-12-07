<script setup>
import { ref } from 'vue';
import { AlertError, AlertSuccess, loaderfun } from "~/composables/statFunc.js";
import { basUrl } from "~/composables/states.js";
import { useCookie } from 'nuxt/app';

const name = ref('');
const address = ref('');
const phone = ref('');
const jwtCookie = useCookie('jwt');

// متد ارسال فرم
const submitForm = async () => {
  loaderfun();
  try {
    const response = await fetch(`${basUrl().value}/companies`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${jwtCookie.value}`,
      },
      body: JSON.stringify({
        name: name.value,
        address: address.value,
        phone: phone.value,
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      AlertError("اطلاعات را صحیح وارد کنید");
    } else {
      AlertSuccess("اطلاعات با موفقیت ثبت شد");

      // پاک‌سازی فرم بعد از موفقیت
      name.value = '';
      address.value = '';
      phone.value = '';
    }
  } catch (error) {
    console.error('Error:', error);
    AlertError("مشکلی رخ داده");
  }
  loaderfun();
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
          <nuxt-link to="/company">
            <Icon name="ph:building" size="18" class="ml-2"/>
            لیست کمپانی‌ها
          </nuxt-link>
        </li>
        <li>
          <a>
            <Icon name="ph:building" size="18" class="ml-2"/>
            کمپانی جدید
          </a>
        </li>
      </ul>
    </div>

    <div class="p-8 m-auto w-10/12">
      <form @submit.prevent="submitForm">
        <label class="input input-bordered flex items-center gap-4 mt-4">
          <Icon name="octicon:organization-16" size="18" class="ml-2"/>
          <input v-model="name" type="text" class="grow" placeholder="نام کمپانی"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
          <input v-model="address" type="text" class="grow" placeholder="آدرس"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="material-symbols:phone-in-talk" size="18" class="ml-2"/>
          <input v-model="phone" type="text" class="grow" placeholder="تلفن"/>
        </label>
        <button type="submit" class="btn btn-primary mt-4">ثبت</button>
      </form>
    </div>
  </div>
</template>
