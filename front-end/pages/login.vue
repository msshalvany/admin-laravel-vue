<template>
  <loader></loader>
  <AlertError></AlertError>
  <AlertSuccess></AlertSuccess>
  <div class="relative flex flex-col justify-center h-screen overflow-hidden" dir="rtl">
    <div class="w-10/12 p-6 m-auto bg-white rounded-md shadow-md ring-2 ring-gray-800/50 lg:max-w-lg">
      <h1 class="text-3xl font-semibold text-center text-gray-700">
        <span class="m-3">ورود</span>
        <Icon class="align-top" style="vertical-align:-10px" name="ic:outline-log-in" />
      </h1>
      <form @submit.prevent="login">
        <div class="mt-4">
          <div>
            <label class="input input-bordered flex items-center gap-2 w-full">
              <Icon name="material-symbols:person-outline" size="24" />
              <input v-model="username" type="text" name="username" placeholder="نام کاربری خود را وارد کنید"
                class="grow" />
            </label>
          </div>
          <div class="mt-3">
            <label class="input input-bordered flex items-center gap-2 w-full">
              <Icon name="material-symbols:vpn-key-outline" size="24" />
              <input v-model="password" type="password" name="password" placeholder="رمز عبور خود را وارد کنید"
                class="w-full mt-2" />
            </label>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn-neutral btn btn-block">ورود</button>
        </div>
      </form>
    </div>
  </div>
  <alert-error v-if="checkLogin" text="نام کاربری یا رمز عبور اشتباه است"></alert-error>
</template>

<script setup>
const username = ref('');
const password = ref('');
const checkLogin = ref(false);
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
  loaderfun()
  try {
    const response = await fetch(`${basUrl().value}/login`, {
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
      loaderfun()
      AlertError('نام کاربری یا رمز عبور اشتباه است');
    } else {
      loaderfun()
      AlertSuccess('ورود موفقیت آمیز بود')
      const data = await response.json();
      // ذخیره کردن توکن JWT در کوکی
      jwtCookie.value = data.token;

      // هدایت به صفحه اصلی پس از ورود موفق
      router.push('/');
    }
  } catch (error) {
    // نمایش پیام خطا
    checkLogin.value = true;
    // مخفی کردن پیام خطا پس از چند ثانیه

  }
};
</script>
