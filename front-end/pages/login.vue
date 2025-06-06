<template>
  <loader></loader>
  <alert-error v-if="checkLogin" text="نام کاربری یا رمز عبور اشتباه است"></alert-error>

  <div class="min-h-screen bg-base-200 flex items-center">
    <div class="card mx-auto w-full max-w-5xl shadow-xl">
      <!-- تنظیمات گرید برای نمایش درست -->
      <div class="grid md:grid-cols-2 grid-cols-1 bg-base-100 rounded-xl">

        <!-- **بخش تصویر (مخفی در موبایل) ** -->
        <div class="hidden md:block">
          <div class="hero min-h-full rounded-l-xl bg-base-200">
            <div class="hero-content py-12">
              <div class="max-w-md">
                <h1 class="text-3xl text-center font-bold">
                  <img
                      src="https://fardim.ir/upload/product/2112-638565534139253666%D8%A7%D9%87%D9%88%D8%B1%D8%A7.jpg"
                      class="w-12 inline-block mr-2 mask mask-circle"
                      alt="dashwind-logo"
                  />
                  پارسیان
                </h1>
                <div class="text-center mt-12">
                  <img
                      src="https://img.freepik.com/free-vector/computer-login-concept-illustration_114360-7962.jpg?w=740&t=st=1705223807~exp=1705224407~hmac=6a7e1b3d37caa6155fb689df5e4b86e598db633aaa841ac84a9576363f76c66d"
                      alt="Dashwind Admin Template"
                      class="w-48 inline-block"
                  />
                </div>
                <div>
                  <h1 class="text-2xl mt-8 font-bold"> به پنل مدیریتی پارسیان خوش آمدید</h1>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- **بخش فرم ورود** -->
        <div dir="rtl" class="py-24 px-10">
          <h2 class="text-3xl font-semibold text-center text-gray-700">
            <span class="m-3">ورود</span>
            <Icon class="align-top" style="vertical-align:-10px" name="ix:log-in"/>
          </h2>
          <form @submit.prevent="login">
            <!-- Username Input -->
            <div class="form-control w-full mt-4">
              <label class="floating-label input input-bordered flex items-center gap-2 w-full">
            <span class="flex items-center">
            <Icon name="material-symbols:person-outline" size="24"/>
              نام کاربری
              </span>
                <input v-model="username" type="text" name="username" placeholder="نام کاربری خود را وارد کنید"
                       class="grow"/>
              </label>
            </div>

            <!-- Password Input -->
            <div class="form-control w-full mt-4">
              <label class="floating-label input input-bordered flex items-center gap-2 w-full">
            <span class="flex items-center">
            <Icon name="material-symbols:vpn-key-outline" size="24"/>
              رمز عبور
            </span>
                <input v-model="password" type="password" name="password" placeholder="رمز عبور خود را وارد کنید"
                       class="w-full mt-2"/>
              </label>
            </div>

            <!-- Login Button -->
            <div class="mt-4">
              <button type="submit" :disabled="disableBtn" class="btn-neutral btn btn-block">ورود</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>


</template>

<script setup>
const username = ref('');
const password = ref('');
const checkLogin = ref(false);
const router = useRouter();
const { $toast } = useNuxtApp();
const disableBtn = ref(false)

// ایجاد کوکی برای توکن
const jwtCookie = useCookie('jwt');
const permissions = useCookie('permissions');

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
      $toast('نام کاربری یا رمز عبور اشتباه است', {
        "theme": "colored",
        "type": "error",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
    } else {
      disableBtn.value=true
      $toast('ورود موفقیت آمیز بود', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
      const data = await response.json();
      // ذخیره کردن توکن JWT در کوکی
      jwtCookie.value = data.token;
      permissions.value = data.permissions;
      loaderfun()
      // هدایت به صفحه اصلی پس از ورود موفق
      setTimeout(function (){
      router.push('/');
      },2000)
    }
  } catch (error) {
    // نمایش پیام خطا
    checkLogin.value = true;
    // مخفی کردن پیام خطا پس از چند ثانیه

  }
};
</script>
