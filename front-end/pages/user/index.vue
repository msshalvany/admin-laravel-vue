<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {loaderfun} from "~/composables/statFunc.js";

// لیست کاربران
const users = ref([]);
// دسترسی‌های کاربر انتخاب‌شده
const userPermissions = ref([]);
// کاربر انتخاب‌شده
const selectedUser = ref(null);
// کنترل نمایش مودال
const showModal = ref(false);

// دریافت لیست کاربران
const fetchUsers = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/users', {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      users.value = data.data;
    } else {
      console.error('Error fetching users:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// نمایش دسترسی‌های کاربر در مودال
const fetchUserPermissions = async (user) => {
  loaderfun()
  selectedUser.value = user;
  showModal.value = true;
  try {
    const response = await fetch(`http://localhost:8000/api/users/${user.id}/permissions`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      userPermissions.value = await response.json();
      loaderfun()
    } else {
      console.error('Error fetching permissions:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// تغییر وضعیت دسترسی کاربر
const togglePermission = async (permission) => {
  loaderfun()
  try {
    await fetch(`http://localhost:8000/api/users/${selectedUser.value.id}/permissions/${permission.id}`, {
      method: 'PATCH',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({hasPermission: !permission.hasPermission}),
    });

    // به‌روز‌رسانی وضعیت محلی
    permission.hasPermission = !permission.hasPermission;
    loaderfun()
  } catch (error) {
    console.error('Error toggling permission:', error);
  }
};

// بستن مودال
const closeModal = () => {
  showModal.value = false;
  selectedUser.value = null;
};

onMounted(fetchUsers);
</script>
<template>
  <div>
    <div class="p-4">
      <!-- مسیر صفحه -->
      <div class="breadcrumbs text-sm">
        <ul>
          <li>
            <nuxt-link to="/">
              <Icon name="ic:baseline-home" size="18" class="ml-2"/>
              خانه
            </nuxt-link>
          </li>
          <li>
            <a>
              <Icon name="ph:users-three-light" size="18" class="ml-2"/>
              کاربران
            </a>
          </li>
        </ul>
      </div>

      <!-- دکمه ایجاد کاربر جدید -->
      <NuxtLink to="user/create">
        <div class="text-left">
          <button class="btn btn-success">
            ایجاد کاربر جدید
            <Icon name="material-symbols-add-circle" size="18"/>
          </button>
        </div>
      </NuxtLink>

      <!-- جدول کاربران -->
      <div class="overflow-x-auto mt-4">
        <table class="table">
          <thead>
          <tr class="text-center">
            <th>id</th>
            <th>نام</th>
            <th>تاریخ</th>
            <th>مدیریت دسترسی‌ها</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id" class="hover text-center">
            <th>{{ user.id }}</th>
            <td>{{ user.username }}</td>
            <td>{{ user.created_at }}</td>
            <td>
              <button
                  class="btn btn-sm btn-outline"
                  @click="fetchUserPermissions(user)"
              >
                مشاهده دسترسی‌ها
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <!-- مودال مدیریت دسترسی‌ها -->
      <div v-if="showModal" class="modal modal-open">
        <div class="modal-box">
          <h2 class="text-lg font-bold mb-4">مدیریت دسترسی‌ها - {{ selectedUser?.username }}</h2>
          <table class="table w-full">
            <thead>
            <tr>
              <th>#</th>
              <th>نام دسترسی</th>
              <th>وضعیت</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(permission, index) in userPermissions" :key="permission.id">
              <td>{{ index + 1 }}</td>
              <td>{{ permission.name }}</td>
              <td>
                <input
                    type="checkbox"
                    class="toggle toggle-primary"
                    :checked="permission.hasPermission"
                    @change="togglePermission(permission)"
                />
              </td>
            </tr>
            </tbody>
          </table>
          <div class="modal-action">
            <button class="btn btn-error" @click="closeModal">بستن</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

