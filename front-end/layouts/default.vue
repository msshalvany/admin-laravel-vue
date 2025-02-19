<template>
  <div>
    <loader></loader>
    <AlertError></AlertError>
    <AlertSuccess></AlertSuccess>
    <AlertWarning></AlertWarning>
    <div class="drawer lg:drawer-open" dir="rtl">
      <input id="my-drawer-2" type="checkbox" class="drawer-toggle" v-model="isDrawerOpen"/>

      <!-- Sidebar (Drawer) -->
      <div class="drawer-side shadow-2xl" style="z-index: 12">
        <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu bg-base-100 text-base-content min-h-full w-64 p-2.5">
          <li>
            <details v-if="hasPermission('UserManegment')" class="mt-2">
              <summary>
                <Icon name="ph:users-three" size="26" />
                کاربران
              </summary>
              <ul>
                <li>
                  <NuxtLink to="/user" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
                    <Icon name="hugeicons:user-list" size="24" />
                    لیست کاربران
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/user/permisition" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
                    <Icon name="hugeicons:lock-key" size="24" />
                    سطوح دسترسی
                  </NuxtLink>
                </li>
              </ul>
            </details>
            <details class="mt-2">
              <summary>
                <Icon name="ph:truck-trailer-light" size="24" />
                تردد
              </summary>
              <ul>
                <li>
                  <NuxtLink to="/LoadingRecord" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
                    <Icon name="mdi:truck-fast-outline" size="24" />
                    ثبت تردد
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/LoadingRecord/history" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
                    <Icon name="streamline:shipping-transfer-truck-time-truck-shipping-delivery-time-waiting-delay" size="26" />
                    تاریخچه
                  </NuxtLink>
                </li>
              </ul>
            </details>
            <NuxtLink class="mt-2" to="/company" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
              <Icon name="octicon:organization-16" size="20"></Icon>
              کمپانی ها
            </NuxtLink>
            <NuxtLink class="mt-2" to="/LoadingRecord/drivers/" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
              <Icon name="healthicons:truck-driver" size="26" />
              رانندگان
            </NuxtLink>
            <NuxtLink class="mt-2" to="/LoadingRecord/truck/" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
              <Icon name="ph:truck-duotone" size="26" />
              ماشین ها
            </NuxtLink>
            <NuxtLink class="mt-2" to="/location" active-class="bg-[#422AD5] text-white" @click="closeDrawer">
              <Icon name="ic:outline-place" size="24"></Icon>
              مکان ها
            </NuxtLink>
          </li>
        </ul>
      </div>

      <!-- Main content area -->
      <div class="drawer-content flex flex-col index">
        <!-- Header (Top bar) -->
        <div class="navbar bg-base-100 shadow-md">
          <div class="flex-1">
            <!-- Button to toggle the drawer -->
            <label for="my-drawer-2" class="btn btn-ghost drawer-button lg:hidden">
              <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 26 26"
                  stroke="currentColor"
              >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16m-7 6h7"
                />
              </svg>
            </label>
          </div>
          <div class="flex-none">
            <ThemeToggle/>
            <NotificationsDropdown/>
            <UserMenu/>
          </div>
        </div>

        <!-- Main Content -->
        <main class="h-full">
          <slot/> <!-- محتویات صفحات -->
        </main>
        <footer class="footer sm:footer-horizontal footer-center bg-base-300 text-base-content p-4">
          <aside>
            <p>© 2025 شرکت پارسیان. تمامی حقوق محفوظ است. | طراحی و توسعه توسط تیم پارسیان.</p>
          </aside>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue';

const isDrawerOpen = ref(false);

function closeDrawer() {
  isDrawerOpen.value = false;
}

definePageMeta({
  pageTransition: {
    name: 'rotate',
  },
});

const permissions = useCookie('permissions').value || []; // دسترسی‌ها را از کوکی‌ها می‌گیریم

// تابع برای چک کردن دسترسی خاص در آرایه دسترسی‌ها
const hasPermission = (item) => {
  return permissions.includes(item); // بررسی اینکه آیا دسترسی مورد نظر در آرایه هست یا نه
};
</script>
