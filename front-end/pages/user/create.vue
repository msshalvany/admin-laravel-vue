<script setup>
// تعریف متغیرها با استفاده از ref
import {AlertError, AlertSuccess, loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";

const username = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const mobile = ref('');
const jwtCookie = useCookie('jwt');

// متد ارسال فرم
const submitForm = async () => {
  if (password.value !== passwordConfirmation.value) {
    AlertError("رمز عبور مطابقت ندارد")
    return 0
  }
  loaderfun()
  try {
    const response = await fetch(`${basUrl().value}/users`, {
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
      AlertError("اطلاعات را صحیح وارد کنید");
    } else {
      AlertSuccess("اطلاعات با موفقیت ثبت شد");

      // پاک‌سازی فرم بعد از موفقیت
      username.value = '';
      password.value = '';
      passwordConfirmation.value = '';
      mobile.value = '';
    }
  } catch (error) {
    console.error('Error:', error);
    AlertError("مشکلی رخ داده");
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
      </div>
    </div>
    <div class="p-8 m-auto flex justify-center content-center w-10/12 shadow-xl">
      <form @submit.prevent="submitForm" class="form-control w-full space-y-6">
        <fieldset class="fieldset w-xs bg-base-200 border border-base-300 p-4 rounded-box">
          <legend class="fieldset-legend">
            کاربر جدید
            <Icon name="material-symbols:account-circle" size="18" class="ml-2"/>
          </legend>
          <!-- نام کاربری -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full mt-4">
    <span class="flex items-center">
      <Icon name="material-symbols:account-circle-full" size="18" class="ml-2"/>
      نام کاربری
    </span>
            <input v-model="username" type="text" class="grow" placeholder="نام کاربری"/>
          </label>

          <!-- رمز عبور -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full mt-4">
    <span class="flex items-center">
      <Icon name="solar-lock-password-unlocked-linear" size="18" class="ml-2"/>
      رمز عبور
    </span>
            <input v-model="password" type="password" class="grow" placeholder="رمز عبور"/>
          </label>

          <!-- تکرار رمز عبور -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full mt-4">
    <span class="flex items-center">
      <Icon name="solar-lock-password-unlocked-linear" size="18" class="ml-2"/>
      تکرار رمز عبور
    </span>
            <input v-model="passwordConfirmation" type="password" class="grow" placeholder="تکرار رمز عبور"/>
          </label>

          <!-- شماره همراه -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full mt-4">
    <span class="flex items-center">
      <Icon name="uiw-mobile" size="18" class="ml-2"/>
      شماره همراه
    </span>
            <input v-model="mobile" name="mobile" type="number" class="grow" placeholder="شماره همراه"/>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-4">ثبت</button>
        </fieldset>
      </form>
    </div>
  </div>
</template>
