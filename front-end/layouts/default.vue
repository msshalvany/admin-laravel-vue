<!-- layouts/admin.vue -->
<template>
  <div
      class="loader bg-white w-screen h-screen fixed content-center footer-center"
      style="z-index: 13; display: none"
  >
    <div class="loading loading-ring loading-lg w-20"></div>
  </div>
  <div class="drawer lg:drawer-open">
    <input id="my-drawer-2" type="checkbox" class="drawer-toggle"/>

    <!-- Sidebar (Drawer) -->
    <div class="drawer-side" style="z-index: 12">
      <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
      <ul class="menu bg-base-100 text-base-content min-h-full w-80 p-4">
        <li>
          <a>
            <Icon name="uil:dashboard" size="24"></Icon>
            resume.pdf
          </a>
        </li>
        <li>
          <details>
            <summary>
              <Icon name="uil:dashboard" size="24"></Icon>
              My Files
            </summary>
            <ul>
              <li>
                <a>
                  <Icon name="uil:dashboard" size="24"></Icon>
                  Project-final.psd
                </a>
              </li>
              <li>
                <a>
                  <Icon name="uil:dashboard" size="24"></Icon>
                  Project-final-2.psd
                </a>
              </li>
              <li>
                <details>
                  <summary>
                    <Icon name="uil:dashboard" size="24"></Icon>
                    Images
                  </summary>
                  <ul>
                    <li>
                      <a>
                        <Icon name="uil:dashboard" size="24"></Icon>
                        Screenshot1.png
                      </a>
                    </li>
                    <li>
                      <a>
                        <Icon name="uil:dashboard" size="24"></Icon>
                        Screenshot2.png
                      </a>
                    </li>
                    <li>
                      <details>
                        <summary>
                          <Icon name="uil:dashboard" size="24"></Icon>
                          Others
                        </summary>
                        <ul>
                          <li>
                            <a>
                              <Icon name="uil:dashboard" size="24"></Icon>
                              Screenshot3.png
                            </a>
                          </li>
                        </ul>
                      </details>
                    </li>
                  </ul>
                </details>
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

import ThemeToggle from '~/components/ThemeToggle.vue';
import NotificationsDropdown from '~/components/NotificationsDropdown.vue';
import UserMenu from '~/components/UserMenu.vue';
</script>

<style>
html {
  direction: rtl;
}
</style>
