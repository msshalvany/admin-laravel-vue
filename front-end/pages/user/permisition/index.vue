<template>
  <div class="p-6">
    <h1 class="text-lg font-bold mb-6 text-center">
      مدیریت دسترسی‌ها
    </h1>

    <!-- جدول دسترسی‌ها -->
    <div class="overflow-x-auto mt-4 card shadow-lg p-1 rounded-2xl">
      <table class="table table-sm w-full">
        <thead>
        <tr class="text-center">
          <th>#</th>
          <th>نام دسترسی</th>
          <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(permission, index) in permissions" :key="permission.id" class="hover text-center">
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
    </div>
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
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <h2 class="text-lg font-bold mb-6 text-center">
          لیست کاربران - {{ selectedPermission.name }}
        </h2>
        <div>
          <input
              v-model="searchUser"
              type="text"
              placeholder="جستوجو"
              class="input input-bordered w-full mb-2"
              @input="showUsers(selectedPermission)"
          />
        </div>
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
      </div>
    </div>
  </div>

</template>

<script setup>
import {ref, onMounted} from 'vue';
import {loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";

const permissions = ref([]); // لیست دسترسی‌ها
const newPermission = ref(''); // مقدار ورودی دسترسی جدید
const selectedPermission = ref(null); // دسترسی انتخاب شده برای نمایش کاربران
const users = ref([]); // لیست کاربران مرتبط با دسترسی
const searchUser = ref('');
const { $toast } = useNuxtApp();

// دریافت لیست دسترسی‌ها
const fetchPermissions = async () => {
  const response = await fetch(`${basUrl().value}/permissions`);
  permissions.value = await response.json();
};

// ایجاد دسترسی جدید
const createPermission = async () => {
  if (!newPermission.value) {
    AlertError('نام دسترسی را وارد کنید');
    return;
  }
  loaderfun()
  await fetch(`${basUrl().value}/permissions`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({name: newPermission.value}),
  });
  $toast('سطح دسترسی با موفقیت ایجاد شد', {
    "theme": "colored",
    "type": "success",
    "rtl": true,
    "autoClose":"5000",
    "dangerouslyHTMLString": true
  })
  newPermission.value = '';
  fetchPermissions(); // به‌روز‌رسانی لیست دسترسی‌ها
  loaderfun()
};

// نمایش کاربران مرتبط با دسترسی
const showUsers = async (permission) => {
  selectedPermission.value = permission;
  loaderfun()
  const response = await fetch(
      `${basUrl().value}/permissions/${permission.id}/users/${searchUser.value}`
  );
  users.value = await response.json();
  loaderfun()
};

// تغییر وضعیت دسترسی کاربر
const togglePermission = async (user) => {
  loaderfun()
  await fetch(`${basUrl().value}/permissions/${selectedPermission.value.id}/users/${user.id}`, {
    method: 'PATCH',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({hasPermission: !user.hasPermission}),
  });

  // به‌روز‌رسانی وضعیت کاربر
  user.hasPermission = !user.hasPermission;
  loaderfun()
};

// بستن مودال
const closeModal = () => {
  selectedPermission.value = null;
};

onMounted(fetchPermissions);
</script>
