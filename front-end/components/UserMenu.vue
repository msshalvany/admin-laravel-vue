<!-- components/UserMenu.vue -->
<template>
  <div class="dropdown dropdown-end">
    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
      <div class="w-10 rounded-full">
        <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
      </div>
    </div>
    <ul tabindex="0" class="menu dropdown-content bg-base-100 rounded-box mt-3 w-52 p-2 shadow">
      <li><a>پروفایل</a></li>
      <li><a>تنظیمات</a></li>
      <li><button @click="logout">خروج</button></li>
    </ul>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

function logout() {
  const jwtCookie = useCookie('jwt');
  jwtCookie.value=null
  router.push('/login');
}
</script>
