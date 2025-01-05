<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";

const locations = ref([]);

const selectedLocations = ref(null);

const showEditModal = ref(false); // متغیر برای کنترل نمایش مودال ویرایش

const showCreateModal = ref(false); // متغیر برای کنترل نمایش مودال ایجاد مکان

const showDeleteConfirmation = ref(false);

const showModal = ref(false);
// برای ذخیره داده‌های فرم ایجاد مکان
const newLocations = ref({
  location_name: '',
  description: '',
});
const closeModal = () => {
  showModal.value = false;
  showEditModal.value = false;
  showDeleteConfirmation.value = false;
  selectedLocations.value = null;
};
// دریافت لیست مکان‌ها
const fetchLocation = async () => {
  try {
    const response = await fetch(`${basUrl().value}/location`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      locations.value = data.data;
    } else {
      console.error('Error fetching location:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// انتخاب مکان برای ویرایش
const editLocation = (location) => {
  selectedLocations.value = {...location}; // ایجاد یک کپی از مکان
  showEditModal.value = true; // باز کردن مودال ویرایش
};

// ارسال تغییرات مکان
const updateLocations = async () => {
  loaderfun(); // نمایش لودر

  try {
    const response = await fetch(`${basUrl().value}/location/${selectedLocations.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        location_name: selectedLocations.value.location_name,
        description: selectedLocations.value.description,
      }),
    });

    if (response.ok) {
      AlertSuccess('مکان با موفقیت ویرایش شد');
      showEditModal.value = false;
      fetchLocation(); // بارگذاری مجدد لیست مکان‌ها
    } else {
      console.error('Error updating location:', response.status);
      AlertError('مشکلی در ویرایش مکان رخ داده است');
    }
  } catch (error) {
    console.error('Error:', error);
    AlertError('مشکلی رخ داده است');
  }

  loaderfun(); // مخفی کردن لودر
};

const confirmDelete = async () => {
  loaderfun();
  try {
    const response = await fetch(
        `${basUrl().value}/location/${selectedLocations.value.id}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      AlertSuccess("مکان با موفقیت حذف شد");
      fetchLocation();
      showDeleteConfirmation.value = false;
      selectedLocations.value = null;
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};
// ایجاد مکان جدید
const createLocations = async () => {
  loaderfun(); // نمایش لودر

  try {
    const response = await fetch(`${basUrl().value}/location`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify(newLocations.value),
    });

    if (response.ok) {
      AlertSuccess('مکان با موفقیت ایجاد شد');
      showCreateModal.value = false;
      fetchLocation(); // بارگذاری مجدد لیست مکان‌ها
    } else {
      console.error('Error creating company:', response.status);
      AlertError('مشکلی در ایجاد مکان رخ داده است');
    }
  } catch (error) {
    console.error('Error:', error);
    AlertError('مشکلی رخ داده است');
  }

  loaderfun(); // مخفی کردن لودر
};

const DeleteLocation = (item) => {
  selectedLocations.value = item
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};
const items = (row) => [
  [
    {
      label: "ویرایش",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => editLocation(row)
      ,
    },
  ],
  [
    {
      label: "حذف",
      icon: "i-heroicons-trash-20-solid",
      click: () => DeleteLocation(row)
    },
  ],
];
onMounted(fetchLocation);
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 py-1 rounded-lg">
      <div class="flex justify-between items-center mb-4">
        <div class="breadcrumbs text-sm">
          <ul class="flex items-center">
            <li>
              <nuxt-link to="/">
                <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                خانه
              </nuxt-link>
            </li>
            <li>
              <a>
                <Icon name="ic:outline-place" size="18"></Icon>
                مکان ها
              </a>
            </li>
          </ul>
        </div>
        <div class="text-left">
          <button class="btn btn-success" @click="showCreateModal = true">
            مکان جدید
            <Icon name="material-symbols:add-circle" size="18"/>
          </button>
        </div>
      </div>
    </div>
    <!-- جدول مکان‌ها -->
    <div class="overflow-x-auto mt-4 shadow-lg p-1 rounded-xl">
      <table class="table">
        <thead>
        <tr class="text-center">
          <th>id</th>
          <th>نام</th>
          <th>توضیحات</th>
          <th>مدیریت</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="location in locations" :key="location.id" class="hover text-center">
          <th>{{ location.id }}</th>
          <td>{{ location.location_name }}</td>
          <td>{{ location.description }}</td>
          <td>
            <UDropdown :items="items(location)">
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

    <!-- مودال ویرایش مکان -->
    <div v-if="showEditModal" class="modal modal-open">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="showEditModal = false">✕</button>

        <!-- عنوان فرم -->
        <h2 class="text-lg font-bold mb-6 text-center mt-4">ویرایش مکان - {{ selectedLocations.location_name }}</h2>

        <!-- فرم ویرایش -->
        <form @submit.prevent="updateLocations" class="space-y-6">
          <!-- نام مکان -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
          نام مکان
        </span>
            <input v-model="selectedLocations.location_name" type="text" class="grow" placeholder="نام مکان"/>
          </label>

          <!-- توضیحات -->
          <label class="floating-label textarea textarea-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="fluent:text-description-20-regular" size="18" class="ml-2"/>
          توضیحات
        </span>
            <textarea v-model="selectedLocations.description" class="textarea textarea-bordered w-full"
                      placeholder="توضیحات"></textarea>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-6">ویرایش مکان</button>
        </form>
      </div>
    </div>

    <!-- مودال ایجاد مکان -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="showCreateModal = false">✕</button>

        <!-- عنوان فرم -->
        <h2 class="text-lg font-bold mb-6 text-center mt-4">مکان جدید</h2>

        <!-- فرم ایجاد مکان -->
        <form @submit.prevent="createLocations" class="space-y-6">
          <!-- نام مکان -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
          نام مکان
        </span>
            <input v-model="newLocations.location_name" type="text" class="grow" placeholder="نام مکان"/>
          </label>

          <!-- توضیحات -->
          <label class="floating-label textarea textarea-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="fluent:text-description-20-regular" size="18" class="ml-2"/>
          توضیحات
        </span>
            <textarea v-model="newLocations.description" class="textarea textarea-bordered w-full" placeholder="توضیحات"></textarea>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-6">ایجاد مکان</button>
        </form>
      </div>
    </div>
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="closeModal">✕</button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این مکان را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </div>
      </div>
    </div>
  </div>
</template>
