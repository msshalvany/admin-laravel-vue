<script setup>
// تعریف متغیرها با استفاده از ref
import {loaderfun} from "~/composables/statFunc.js";

const username = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const mobile = ref('');
let errorStatus = ref(null);
let succStatus = ref(null);
const jwtCookie = useCookie('jwt');

// متد ارسال فرم
const submitForm = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorStatus.value = "رمز عبور مطابقت ندارد";
    setTimeout(function () {
      errorStatus.value = null
    }, 4000)
    return 0
  }
  loaderfun()
  try {
    const response = await fetch('http://localhost:8000/api/users', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${jwtCookie.value}`,
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value,
        mobile: mobile.value,
      }),
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error(errorData.errors);
      errorStatus.value = "اطلاعات را صحیح وارد کنید";
      setTimeout(function () {
        errorStatus.value = null
      }, 4000)
    } else {
      succStatus.value = "اطلاعات با موفقیت ثبت شد";
      setTimeout(function () {
        succStatus.value = null
      }, 4000)

      // پاک‌سازی فرم بعد از موفقیت
      username.value = '';
      password.value = '';
      passwordConfirmation.value = '';
      mobile.value = '';
    }
  } catch (error) {
    console.error('Error:', error);
    errorStatus.value = "مشکلی رخ داده";
    setTimeout(function () {
      errorStatus.value = null
    }, 4000)
  }
  loaderfun()
};
</script>


<template>
  <div>
    <alert-error v-if="errorStatus" :text="errorStatus"></alert-error>
    <alert-success v-if="succStatus" :text="succStatus"></alert-success>

    <div class="breadcrumbs text-sm p-4">
      <ul>
        <li>
          <nuxt-link to="/">
            <Icon name="ic:baseline-home" size="18" class="ml-2"/>
            خانه
          </nuxt-link>
        </li>
        <li>
          <nuxt-link to="/user">
            <Icon name="ph:users-three-light" size="18" class="ml-2"/>
            لیست کاربران
          </nuxt-link>
        </li>
        <li>
          <a>
            <Icon name="hugeicons:user" size="18" class="ml-2"/>
            کاربر جدید
          </a>
        </li>
      </ul>
    </div>
    <div class="p-8 m-auto w-10/12">
      <form @submit.prevent="submitForm">
        <label class="input input-bordered flex items-center gap-4 mt-4">
          <Icon name="mdi:user" size="18" class="ml-2"/>
          <input v-model="username" type="text" class="grow" placeholder="نام کاربری"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="ant-design:key-outlined" size="18" class="ml-2"/>
          <input v-model="password" type="password" class="grow" placeholder="رمز عبور"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="ant-design:key-outlined" size="18" class="ml-2"/>
          <input v-model="passwordConfirmation" type="password" class="grow" placeholder="تکرار رمز عبور"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="uiw-mobile" size="18" class="ml-2"/>
          <input v-model="mobile" type="number" class="grow" placeholder="شماره همراه"/>
        </label>
        <button type="submit" class="btn btn-primary mt-4">ثبت</button>
      </form>
    </div>
  </div>
</template>
