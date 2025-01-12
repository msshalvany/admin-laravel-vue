<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";

// لیست کاربران
const users = ref([]);
// دسترسی‌های کاربر انتخاب‌شده
const userPermissions = ref([]);
// کاربر انتخاب‌شده
const selectedUser = ref(null);
// کنترل نمایش مودال
const showModal = ref(false);

const showModalEdit = ref(false);

const showDeleteConfirmation = ref(false);

const nwePassword = ref(null)


// دریافت لیست کاربران
const fetchUsers = async () => {
  try {
    const response = await fetch(`${basUrl().value}/users`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      users.value = data.data;
      console.log(data)
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
    const response = await fetch(`${basUrl().value}/users/${user.id}/permissions`, {
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
    await fetch(`${basUrl().value}/users/${selectedUser.value.id}/permissions/${permission.id}`, {
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

const updateUser = async (user) => {
  selectedUser.value = {...user}
  showModalEdit.value = true
  console.log(user)
};

const submitFormEditUser = async () => {
  loaderfun(); // نمایش لودر
  try {
    const response = await fetch(`${basUrl().value}/users/${selectedUser.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        username: selectedUser.value.username,
        password: nwePassword.value,
        mobile: selectedUser.value.mobile,
      }),
    });

    if (response.ok) {
      AlertSuccess('کاربر با موفقیت ویرایش شد');
      fetchUsers(); // بارگذاری مجدد لیست کمپانی‌ها
      closeModal()
    } else {
      console.error('Error updating company:', response.status);
      AlertError('مشکلی در ویرایش کاربر رخ داده است');
    }
    closeModal()
  } catch (error) {
    console.error('Error:', error);
    AlertError('مشکلی رخ داده است');
  }

  loaderfun(); // مخفی کردن لودر
};


// تابع برای شروع فرآیند حذف راننده
const DeleteUser = (user) => {
  selectedUser.value = user
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};

const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/users/${selectedUser.value.id}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      AlertSuccess("کاربر با موفقیت حذف شد");
      fetchUsers();
      showDeleteConfirmation.value = false;
      selectedUser.value = null;
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};

// بستن مودال
const closeModal = () => {
  showModal.value = false;
  showModalEdit.value = false;
  showDeleteConfirmation.value = false;
  selectedUser.value = null;
  nwePassword.value = null
};

const items = (row) => [
  [
    {
      label: "سطح دسترسی",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => fetchUserPermissions(row)
      ,
    },
  ],
  [
    {
      label: "ویرایش",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => updateUser(row)
      ,
    },
  ],
  [
    {
      label: "حذف",
      icon: "i-heroicons-trash-20-solid",
      click: () => DeleteUser(row)
    },
  ],
];


onMounted(fetchUsers);
</script>
<template>
  <div class="p-4">
    <div class="card shadow-md px-5 py-1 rounded-lg">
      <div class="flex justify-between items-center mb-4">
        <div class="breadcrumbs text-sm">
          <ul class="flex items-center">
            <li>
              <nuxt-link to="/" class="flex items-center">
                <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                خانه
              </nuxt-link>
            </li>
            <li>
              <a class="flex items-center">
                <Icon name="ph:users-three-light" size="18" class="ml-2"/>
                کاربران
              </a>
            </li>
          </ul>
        </div>
        <div>
          <NuxtLink to="/user/create">
            <button class="btn btn-success flex items-center">
              <span> کاربر جدید</span>
              <Icon name="material-symbols-add-circle" size="18"/>
            </button>
          </NuxtLink>
        </div>
      </div>
    </div>


    <!-- بخش جدول کاربران -->
    <div class="card shadow-lg p-4 rounded-lg">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
          <tr class="text-center">
            <th>نام</th>
            <th>موبایل</th>
            <th>عملیات</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id" class="hover text-center">
            <td>{{ user.username }}</td>
            <td>{{ user.mobile }}</td>
            <td>
              <UDropdown :items="items(user)">
                <button class="btn btn-sm btn-primary flex items-center">
                  <span>عملیات</span>
                  <Icon name="hugeicons:account-setting-01" size="18"/>
                </button>
              </UDropdown>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- مودال‌ها -->
    <div v-if="showModal" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <h2 class="text-lg font-bold mb-6 text-center">
          مدیریت دسترسی‌ها - {{ selectedUser?.username }}
        </h2>
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
      </div>
    </div>

    <div v-if="showModalEdit" class="modal modal-open">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>

        <!-- فرم -->
        <form @submit.prevent="submitFormEditUser" class="form-control space-y-6">
          <!-- عنوان فرم -->
          <h2 class="text-lg font-bold mb-6 text-center">
            {{ selectedUser?.username }} - ویرایش کاربر
          </h2>

          <!-- نام کاربری -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:account-circle-full" size="18" class="ml-2"/>
          نام کاربری
        </span>
            <input v-model="selectedUser.username" type="text" class="grow" placeholder="نام کاربری"/>
          </label>

          <!-- رمز عبور -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="solar-lock-password-unlocked-linear" size="18" class="ml-2"/>
          رمز عبور
        </span>
            <input v-model="selectedUser.password" type="password" class="grow" placeholder="رمز عبور"/>
          </label>

          <!-- شماره همراه -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="uiw-mobile" size="18" class="ml-2"/>
          شماره همراه
        </span>
            <input v-model="selectedUser.mobile" name="mobile" type="number" class="grow" placeholder="شماره همراه"/>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-6">ویرایش کاربر</button>
        </form>
      </div>
    </div>

    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </div>
      </div>
    </div>
  </div>
</template>


