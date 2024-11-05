<script setup lang="ts">
import {ref, onMounted} from 'vue';

// ایجاد یک متغیر برای ذخیره لیست کاربران
const users = ref([]);

// ارسال درخواست به API برای دریافت لیست کاربران
const fetchUsers = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/users', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${useCookie('jwt').value}`, // ارسال توکن JWT در هدر برای احراز هویت
        'Content-Type': 'application/json',
      },
    });

    // اگر درخواست موفقیت‌آمیز بود، داده‌ها را ذخیره کن
    if (response.ok) {
      const data = await response.json();
      users.value = data.data; // فرض بر این است که داده‌ها در فیلد `users` قرار دارند
    } else {
      console.error('Error fetching users:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// بارگذاری داده‌ها هنگام بارگذاری کامپوننت
onMounted(() => {
  fetchUsers();
});

// تنظیم متا دیتا برای استفاده از میانه‌راه (middleware)
definePageMeta({
  middleware: ['auth'],
});
</script>

<template>
  <div class="p-4">
    <div class="breadcrumbs text-sm">
      <ul>
        <li>
          <a>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                class="h-4 w-4 stroke-current">
              <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
            خانه
          </a>
        </li>
        <li>
          <a>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                class="h-4 w-4 stroke-current">
              <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
            </svg>
              کاربران
          </a>
        </li>
      </ul>
    </div>
    <div class="overflow-x-auto mt-4">
      <table class="table">
        <thead>
        <tr class="text-center">
          <th>id</th>
          <th>Name</th>
          <th>date</th>
        </tr>
        </thead>
        <tbody>
        <!-- نمایش داده‌های کاربران -->
        <tr v-for="user in users" :key="user.id" class="hover text-center">
          <th>{{ user.id }}</th>
          <td>{{ user.username }}</td>
          <td>{{ user.created_at }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
