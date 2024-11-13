<!-- layouts/admin.vue -->
<template>
  <div class="drawer lg:drawer-open" dir="rtl">
    <input id="my-drawer-2" type="checkbox" class="drawer-toggle"/>

    <!-- Sidebar (Drawer) -->
    <div class="drawer-side shadow-2xl" style="z-index: 12">
      <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
      <ul class="menu bg-base-100 text-base-content min-h-full w-80 p-4">
        <li>

        </li>
        <li>
          <details>
            <summary>
              <Icon name="ph:users-three-light"  size="24" />
              کاربران
            </summary>
            <ul>
              <li>
                <NuxtLink to="/user">
                  <Icon name="hugeicons:user-list" size="24" />
                  لیست کاربران
                </NuxtLink>
              </li>
              <li>
                <a>
                  <Icon name="hugeicons:lock-key" size="24" />
                  سطوح دسترسی
                </a>
              </li>
            </ul>
          </details>
        </li>
        <li>
          <a>
            <Icon name="uil:dashboard" size="24"></Icon>
            reports-final-2.pdf
          </a>
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
                viewBox="0 0 24 24"
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
</template>
<script setup lang="ts">
</script>