<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";
const { $toast } = useNuxtApp();

let userToDelete = ref(null)

let loading = ref(false)

function loadingTootle() {
  let val = loading.value
  loading.value = !val
}

// لیست کاربران
const users = ref([]);
// دسترسی‌های کاربر انتخاب‌شده
const userPermissions = ref([]);
// کاربر انتخاب‌شده
const selectedUser = ref(null);
// کنترل نمایش مودال
const showModal = ref(false);

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
  loadingTootle()
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
    } else {
      console.error('Error fetching permissions:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
  loadingTootle()
};

// تغییر وضعیت دسترسی کاربر
const togglePermission = async (permission) => {
  loadingTootle()
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
    loadingTootle()
  } catch (error) {
    console.error('Error toggling permission:', error);
  }
};

const updateUser = async (user) => {
  selectedUser.value = {...user}
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
      $toast('کاربر با موفقیت ویرایش شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
      fetchUsers(); // بارگذاری مجدد لیست کمپانی‌ها
    } else {
      console.error('Error updating company:', response.status);
      $toast('مشکلی در ویرایش کاربر رخ داده است', {
        "theme": "colored",
        "type": "error",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
    }
  } catch (error) {
    console.error('Error:', error);
    $toast('مشکلی رخ داده است', {
      "theme": "colored",
      "type": "error",
      "autoClose":"5000",
      "rtl": true,
      "dangerouslyHTMLString": true
    })
  }

  loaderfun(); // مخفی کردن لودر
};


const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/users/${userToDelete.value}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      $toast('کاربر با موفقیت حذف شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
      fetchUsers();
      selectedUser.value = null;
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};
onMounted(fetchUsers);
</script>
<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
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
            <li>
              <a class="flex items-center">
                <Icon name="hugeicons:user-list" size="18" />
                لیست کاربران
              </a>
            </li>
          </ul>
        </div>
        <div>
          <NuxtLink to="/user/create">
            <button class="btn btn-success flex items-center">
              <span> کاربر جدید</span>
              <Icon name="streamline:interface-user-add-actions-add-close-geometric-human-person-plus-single-up-user" size="18"/>
            </button>
          </NuxtLink>
        </div>
      </div>
    </div>


    <!-- بخش جدول کاربران -->
    <div class="card shadow-lg p-4 rounded-lg">
      <div class="overflow-x-auto">
        <table class="table">
          <thead class="bg-base-content/20">
          <tr class="text-center">
            <th>نام</th>
            <th>موبایل</th>
            <th>عملیات</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id" class="hover:bg-base-300 transition delay- text-center">

            <td>{{ user.username }}</td>
            <td>{{ user.mobile }}</td>
            <td class="">
              <div>
                <div @click="userToDelete = user.id " onclick="confirmDelete_modal.showModal()" class="tooltip"
                     data-tip="حذف">
                  <button class="btn btn-xs btn-error">
                    <Icon name="i-heroicons-trash-20-solid" size="18"/>
                  </button>
                </div>
                <div @click="updateUser(user)" onclick="editUser_modal.showModal()" class="tooltip" data-tip="ویرایش">
                  <button class="btn btn-xs btn-info">
                    <Icon name="i-heroicons-pencil-square-20-solid" size="18"/>
                  </button>
                </div>
                <div @click="fetchUserPermissions(user)" onclick="userPermissions.showModal()" class="tooltip"
                     data-tip="ویرایش مجوز">
                  <button class="btn btn-xs btn-warning">
                    <Icon name="material-symbols:lock-person-rounded" size="18"/>
                  </button>
                </div>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
        <div v-if="users.length===0" class="w-full text-center my-4">
          <span class="loading loading-ring w-1/12"></span>
        </div>
      </div>
    </div>

    <!-- مودال‌ها -->
    <dialog id="userPermissions" class="modal">
      <div class="modal-box relative" v-if="selectedUser">

        <!-- لایه لودینگ وسط مودال -->
        <div
            v-if="loading"
            class="absolute inset-0 bg-base-100 bg-opacity-80 z-50 flex justify-center items-center opacity-60 pointer-events-none"
        >
          <span class="loading loading-ring w-1/4"></span>
        </div>

        <!-- محتوای اصلی -->
        <div>
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
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
              <Icon name="material-symbols:close"/>
            </button>
          </form>
        </div>

      </div>
    </dialog>

    <dialog id="editUser_modal" class="modal">
      <div class="modal-box relative" v-if="selectedUser">
        <div class="form-control space-y-6">
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
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
              <Icon name="material-symbols:close"/>
            </button>
            <button type="submit" @click="submitFormEditUser" class="btn btn-primary w-full mt-6">ویرایش کاربر</button>
          </form>
        </div>
      </div>
    </dialog>

    <dialog id="confirmDelete_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟</h2>
        <form method="dialog">
          <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
            <Icon name="material-symbols:close"/>
          </button>
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </form>
      </div>
    </dialog>
  </div>
</template>


