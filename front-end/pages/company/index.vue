<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {AlertSuccess, loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";

const companies = ref([]);

const selectedCompany = ref(null);

const showEditModal = ref(false); // متغیر برای کنترل نمایش مودال ویرایش

const showCreateModal = ref(false); // متغیر برای کنترل نمایش مودال ایجاد کمپانی

const showDeleteConfirmation = ref(false);

const showModal = ref(false);
// برای ذخیره داده‌های فرم ایجاد کمپانی
const newCompany = ref({
  name: '',
  address: '',
  phone: '',
});
const closeModal = () => {
  showModal.value = false;
  showEditModal.value = false;
  showDeleteConfirmation.value = false;
  selectedCompany.value = null;
};
// دریافت لیست کمپانی‌ها
const fetchCompanies = async () => {
  try {
    const response = await fetch(`${basUrl().value}/companies`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });

    if (response.ok) {
      const data = await response.json();
      companies.value = data.data;
    } else {
      console.error('Error fetching companies:', response.status);
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

// انتخاب کمپانی برای ویرایش
const editCompany = (company) => {
  selectedCompany.value = {...company}; // ایجاد یک کپی از کمپانی
  showEditModal.value = true; // باز کردن مودال ویرایش
};

// ارسال تغییرات کمپانی
const updateCompany = async () => {
  loaderfun(); // نمایش لودر

  try {
    const response = await fetch(`${basUrl().value}/companies/${selectedCompany.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify({
        name: selectedCompany.value.name,
        address: selectedCompany.value.address,
        phone: selectedCompany.value.phone,
      }),
    });

    if (response.ok) {
      AlertSuccess('کمپانی با موفقیت ویرایش شد');
      showEditModal.value = false;
      fetchCompanies(); // بارگذاری مجدد لیست کمپانی‌ها
    } else {
      console.error('Error updating company:', response.status);
      AlertError('مشکلی در ویرایش کمپانی رخ داده است');
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
        `${basUrl().value}/companies/${selectedCompany.value.id}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      AlertSuccess("کمپانی با موفقیت حذف شد");
      fetchCompanies();
      showDeleteConfirmation.value = false;
      selectedCompany.value = null;
    } else {
      console.error("Error deleting driver:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
  loaderfun();
};
// ایجاد کمپانی جدید
const createCompany = async () => {
  loaderfun(); // نمایش لودر

  try {
    const response = await fetch(`${basUrl().value}/companies`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${useCookie('jwt').value}`,
      },
      body: JSON.stringify(newCompany.value),
    });

    if (response.ok) {
      AlertSuccess('کمپانی با موفقیت ایجاد شد');
      showCreateModal.value = false;
      fetchCompanies(); // بارگذاری مجدد لیست کمپانی‌ها
    } else {
      console.error('Error creating company:', response.status);
      AlertError('مشکلی در ایجاد کمپانی رخ داده است');
    }
  } catch (error) {
    console.error('Error:', error);
    AlertError('مشکلی رخ داده است');
  }

  loaderfun(); // مخفی کردن لودر
};

const DeleteCompany = (item) => {
  selectedCompany.value = item
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};
const items = (row) => [
  [
    {
      label: "ویرایش",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => editCompany(row)
      ,
    },
  ],
  [
    {
      label: "حذف",
      icon: "i-heroicons-trash-20-solid",
      click: () => DeleteCompany(row)
    },
  ],
];
onMounted(fetchCompanies);
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5 rounded-lg">
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
                <Icon name="octicon:organization-16" size="15"></Icon>
                کمپانی‌ها
              </a>
            </li>
          </ul>
        </div>
        <div class="text-left">
          <button class="btn btn-success" @click="showCreateModal = true">
            کمپانی جدید
            <Icon name="material-symbols:add-circle" size="18"/>
          </button>
        </div>
      </div>
    </div>
    <!-- جدول کمپانی‌ها -->
    <div class="overflow-x-auto mt-4 shadow-lg p-1 rounded-xl">
      <table class="table">
        <thead>
        <tr class="text-center">
          <th>id</th>
          <th>نام</th>
          <th>آدرس</th>
          <th>تلفن</th>
          <th>مدیریت</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="company in companies" :key="company.id" class="hover text-center">
          <th>{{ company.id }}</th>
          <td>{{ company.name }}</td>
          <td>{{ company.address }}</td>
          <td>{{ company.phone }}</td>
          <td>
            <UDropdown :items="items(company)">
              <button class="btn btn-sm btn-primary flex items-center">
                <span>عملیات</span>
                <Icon name="mingcute:settings-7-line" size="18"/>
              </button>
            </UDropdown>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- مودال ویرایش کمپانی -->
    <div v-if="showEditModal" class="modal modal-open">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="showEditModal = false">
          <Icon name="material-symbols:close"/>
        </button>

        <!-- عنوان ویرایش -->
        <h2 class="text-lg font-bold mb-6 text-center mt-2">
          ویرایش کمپانی - {{ selectedCompany.name }}
        </h2>

        <!-- فرم ویرایش کمپانی -->
        <form @submit.prevent="updateCompany" class="space-y-6">
          <!-- نام کمپانی -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="octicon:organization-16" size="18" class="ml-2"/>
          نام کمپانی
        </span>
            <input v-model="selectedCompany.name" type="text" class="grow" placeholder="نام کمپانی"/>
          </label>

          <!-- آدرس -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
          آدرس
        </span>
            <input v-model="selectedCompany.address" type="text" class="grow" placeholder="آدرس"/>
          </label>

          <!-- تلفن -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:phone-in-talk" size="18" class="ml-2"/>
          تلفن
        </span>
            <input v-model="selectedCompany.phone" type="text" class="grow" placeholder="تلفن"/>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-6">ویرایش کمپانی</button>
        </form>
      </div>
    </div>

    <!-- مودال ایجاد کمپانی -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="showCreateModal = false">
          <Icon name="material-symbols:close"/>
        </button>

        <!-- عنوان فرم -->
        <h2 class="text-lg font-bold mb-6 text-center mt-2">کمپانی جدید</h2>

        <!-- فرم ایجاد کمپانی -->
        <form @submit.prevent="createCompany" class="space-y-6">
          <!-- نام کمپانی -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="octicon:organization-16" size="18" class="ml-2"/>
          نام کمپانی
        </span>
            <input v-model="newCompany.name" type="text" class="grow" placeholder="نام کمپانی"/>
          </label>

          <!-- آدرس -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
          آدرس
        </span>
            <input v-model="newCompany.address" type="text" class="grow" placeholder="آدرس"/>
          </label>

          <!-- تلفن -->
          <label class="floating-label input input-bordered flex items-center gap-4 w-full">
        <span class="flex items-center">
          <Icon name="material-symbols:phone-in-talk" size="18" class="ml-2"/>
          تلفن
        </span>
            <input v-model="newCompany.phone" type="text" class="grow" placeholder="تلفن"/>
          </label>

          <!-- دکمه ارسال -->
          <button type="submit" class="btn btn-primary w-full mt-6">ایجاد کمپانی</button>
        </form>
      </div>
    </div>
    <div v-if="showDeleteConfirmation" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn" @click="closeModal">
          <Icon name="material-symbols:close"/>
        </button>
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کمپانی را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
        </div>
      </div>
    </div>
  </div>
</template>
