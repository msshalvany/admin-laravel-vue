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
          <Icon name="material-symbols:account-circle-full" size="18" class="ml-2"/>
          <input v-model="username" type="text" class="grow" placeholder="نام کاربری"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="solar-lock-password-unlocked-linear" size="18" class="ml-2"/>
          <input v-model="password" type="password" class="grow" placeholder="رمز عبور"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="solar-lock-password-unlocked-linear" size="18" class="ml-2"/>
          <input v-model="passwordConfirmation" type="password" class="grow" placeholder="تکرار رمز عبور"/>
        </label>
        <label class="input input-bordered flex items-center gap-2 mt-4">
          <Icon name="uiw-mobile" size="18" class="ml-2"/>
          <input v-model="mobile" name="mobile" type="number" class="grow" placeholder="شماره همراه"/>
        </label>
        <button type="submit" class="btn btn-primary mt-4">ثبت</button>
      </form>
    </div>
  </div>
</template>
