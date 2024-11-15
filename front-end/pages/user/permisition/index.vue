<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">مدیریت دسترسی‌ها</h1>

    <!-- جدول دسترسی‌ها -->
    <table class="table w-full">
      <thead>
      <tr>
        <th>#</th>
        <th>نام دسترسی</th>
        <th>عملیات</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(permission, index) in permissions" :key="permission.id">
        <td>{{ index + 1 }}</td>
        <td>{{ permission.name }}</td>
        <td>
          <button class="btn btn-sm btn-outline" @click="showUsers(permission)">
            مشاهده کاربران
          </button>
        </td>
      </tr>
      </tbody>
    </table>

    <!-- فرم ایجاد دسترسی جدید -->
    <div class="mt-6">
      <input
          v-model="newPermission"
          type="text"
          placeholder="نام دسترسی جدید"
          class="input input-bordered w-full mb-2"
      />
      <button class="btn btn-info w-full" @click="createPermission">
        ایجاد دسترسی
      </button>
    </div>

    <!-- مودال کاربران -->
    <div v-if="selectedPermission" class="modal modal-open">
      <div class="modal-box">
        <h2 class="text-lg font-bold mb-4">
          لیست کاربران - {{ selectedPermission.name }}
        </h2>
        <table class="table w-full">
          <thead>
          <tr>
            <th>#</th>
            <th>نام کاربر</th>
            <th>وضعیت دسترسی</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(user, index) in users" :key="user.id">
            <td>{{ index + 1 }}</td>
            <td>{{ user.username }}</td>
            <td>
              <input
                  type="checkbox"
                  class="toggle toggle-primary"
                  :checked="user.hasPermission"
                  @change="togglePermission(user)"
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

  <alert-error v-if="alert_error !== ''" :text="alert_error"></alert-error>
</template>

<script setup>
import { ref, onMounted } from 'vue';

let alert_error = ref('');
const permissions = ref([]); // لیست دسترسی‌ها
const newPermission = ref(''); // مقدار ورودی دسترسی جدید
const selectedPermission = ref(null); // دسترسی انتخاب شده برای نمایش کاربران
const users = ref([]); // لیست کاربران مرتبط با دسترسی

// دریافت لیست دسترسی‌ها
const fetchPermissions = async () => {
  const response = await fetch('http://localhost:8000/api/permissions');
  permissions.value = await response.json();
};

// ایجاد دسترسی جدید
const createPermission = async () => {
  if (!newPermission.value) {
    alert_error.value = 'نام دسترسی را وارد کنید';
    setTimeout(() => {
      alert_error.value = '';
    }, 5000);
    return;
  }

  await fetch('http://localhost:8000/api/permissions', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({name: newPermission.value}),
  });

  newPermission.value = '';
  fetchPermissions(); // به‌روز‌رسانی لیست دسترسی‌ها
};

// نمایش کاربران مرتبط با دسترسی
const showUsers = async (permission) => {
  selectedPermission.value = permission;
  const response = await fetch(
      `http://localhost:8000/api/permissions/${permission.id}/users`
  );
  users.value = await response.json();
};

// تغییر وضعیت دسترسی کاربر
const togglePermission = async (user) => {
  await fetch(`http://localhost:8000/api/permissions/${selectedPermission.value.id}/users/${user.id}`, {
    method: 'PATCH',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({hasPermission: !user.hasPermission}),
  });

  // به‌روز‌رسانی وضعیت کاربر
  user.hasPermission = !user.hasPermission;
};

// بستن مودال
const closeModal = () => {
  selectedPermission.value = null;
};

onMounted(fetchPermissions);
</script>
