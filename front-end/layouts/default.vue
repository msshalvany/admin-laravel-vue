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
        <ul class="menu bg-base-100 text-base-content min-h-full w-80 p-4">
          <li>
            <details>
              <summary>
                <Icon name="ph:users-three" size="26"/>
                کاربران
              </summary>
              <ul>
                <li>
                  <NuxtLink to="/user" @click="closeDrawer">
                    <Icon name="hugeicons:user-list" size="26"/>
                    لیست کاربران
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/user/permisition" @click="closeDrawer">
                    <Icon name="hugeicons:lock-key" size="26"/>
                    سطوح دسترسی
                  </NuxtLink>
                </li>
              </ul>
            </details>
          </li>
          <li>
            <details>
              <summary>
                <Icon name="ph:truck-trailer-light" size="26"/>
                تردد
              </summary>
              <ul>
                <li>
                  <NuxtLink to="/LoadingRecord/drivers/" @click="closeDrawer">
                    <Icon name="healthicons:truck-driver" size="26"/>
                    رانندگان
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/LoadingRecord/truck/" @click="closeDrawer">
                    <Icon name="ph:truck-duotone" size="26"/>
                    ماشین ها
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/LoadingRecord" @click="closeDrawer">
                    <Icon name="mdi:truck-fast-outline" size="26"/>
                    ثبت تردد
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/LoadingRecord/history" @click="closeDrawer">
                    <Icon name="streamline:shipping-transfer-truck-time-truck-shipping-delivery-time-waiting-delay"
                          size="26"/>
                    تاریخچه
                  </NuxtLink>
                </li>
              </ul>
            </details>
            <NuxtLink to="/company" @click="closeDrawer">
              <a>
                <Icon name="octicon:organization-16" size="24"></Icon>
                کمپانی ها
              </a>
            </NuxtLink>
            <NuxtLink to="/location" @click="closeDrawer">
              <a>
                <Icon name="ic:outline-place" size="24"></Icon>
                مکان ها
              </a>
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
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
const isDrawerOpen = ref(false);

function closeDrawer() {
  isDrawerOpen.value = false;
}

definePageMeta({
  pageTransition: {
    name: 'rotate',
  },
});
</script>
