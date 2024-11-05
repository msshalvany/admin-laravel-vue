<template>
  <div class="relative flex flex-col justify-center h-screen overflow-hidden" dir="rtl">
    <div class="w-full p-6 m-auto bg-white rounded-md shadow-md ring-2 ring-gray-800/50 lg:max-w-lg">
      <h1 class="text-3xl font-semibold text-center text-gray-700">Login</h1>
      <form @submit.prevent="login">
        <div>
          <label class="label">
            <span class="text-base label-text">نام کاربری :</span>
          </label>
          <input v-model="username" type="text" name="username" placeholder="نام کاربری خود را وارد کنید"
                 class="w-full input input-bordered"/>
        </div>
        <div>
          <label class="label">
            <span class="text-base label-text">رمز عبور :</span>
          </label>
          <input v-model="password" type="password" name="password" placeholder="رمز عبور خود را وارد کنید"
                 class="w-full input input-bordered"/>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn-neutral btn btn-block">ورود</button>
        </div>
      </form>
      <!-- نمایش پیام خطا -->
      <div role="alert" class="alert alert-error mt-4" v-if="checkLogin">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 shrink-0 stroke-current"
            fill="none"
            viewBox="0 0 24 24">
          <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ errorMessage }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCookie } from 'nuxt/app'; // استفاده از useCookie

const username = ref('');
const password = ref('');
const checkLogin = ref(false);
const errorMessage = ref(''); // اضافه کردن پیغام خطا
const router = useRouter();

// ایجاد کوکی برای توکن
const jwtCookie = useCookie('jwt');

onMounted(() => {
  document.documentElement.setAttribute('data-theme', 'light');
});

definePageMeta({
  layout: 'empty',
});

// توابع مربوط به ورود
const login = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username: username.value,
        password: password.value
      })
    });

    // بررسی موفقیت‌آمیز بودن درخواست
    if (!response.ok) {
      throw new Error('نام کاربری یا رمز عبور اشتباه است');
    }

    const data = await response.json();

    // ذخیره کردن توکن JWT در کوکی
    jwtCookie.value = data.token;

    // هدایت به صفحه اصلی پس از ورود موفق
    router.push('/');
  } catch (error) {
    // نمایش پیام خطا
    errorMessage.value = error.message || 'خطا در ورود به سیستم';
    checkLogin.value = true;

    // مخفی کردن پیام خطا پس از چند ثانیه
    setTimeout(() => {
      checkLogin.value = false;
      errorMessage.value = ''; // پاک کردن پیام خطا
    }, 3000);
  }
};
</script>
