<script setup>
import { ref, onMounted } from 'vue';
import { useCookie } from 'nuxt/app';
import { loaderfun } from "~/composables/statFunc.js";
import { basUrl } from "~/composables/states.js";

const companies = ref([]);
const selectedCompany = ref(null);
const showEditModal = ref(false); // متغیر برای کنترل نمایش مودال ویرایش
const showCreateModal = ref(false); // متغیر برای کنترل نمایش مودال ایجاد کمپانی

// برای ذخیره داده‌های فرم ایجاد کمپانی
const newCompany = ref({
  name: '',
  address: '',
  phone: '',
});

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
  selectedCompany.value = { ...company }; // ایجاد یک کپی از کمپانی
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

onMounted(fetchCompanies);
</script>

<template>
  <div>
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
            <Icon name="ph:business" size="18" class="ml-2"/>
            کمپانی‌ها
          </a>
        </li>
      </ul>
    </div>

    <!-- دکمه ایجاد کمپانی جدید -->
    <div class="text-left p-4">
      <button class="btn btn-success" @click="showCreateModal = true">
        ایجاد کمپانی جدید
        <Icon name="material-symbols:add-circle" size="18"/>
      </button>
    </div>

    <!-- جدول کمپانی‌ها -->
    <div class="border-t overflow-x-auto mt-4 shadow-lg p-1 rounded-2xl">
      <table class="table table-sm">
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
            <button
                class="btn btn-sm btn-outline"
                @click="editCompany(company)"
            >
              ویرایش
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- مودال ویرایش کمپانی -->
    <div v-if="showEditModal" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="showEditModal = false">✕</button>
        <h2 class="text-lg font-bold mb-4 mt-2">ویرایش کمپانی - {{ selectedCompany?.name }}</h2>

        <!-- فرم ویرایش -->
        <form @submit.prevent="updateCompany">
          <label class="input input-bordered flex items-center gap-4 mt-4">
            <Icon name="octicon:organization-16" size="18" class="ml-2"/>
            <input v-model="selectedCompany.name" type="text" class="grow" placeholder="نام کمپانی"/>
          </label>
          <label class="input input-bordered flex items-center gap-4 mt-4">
            <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
            <input v-model="selectedCompany.address" type="text" class="grow" placeholder="آدرس"/>
          </label>
          <label class="input input-bordered flex items-center gap-4 mt-4">
            <Icon name="material-symbols:phone-in-talk" size="18" class="ml-2"/>
            <input v-model="selectedCompany.phone" type="text" class="grow" placeholder="تلفن"/>
          </label>
          <button type="submit" class="btn btn-primary mt-4">ویرایش کمپانی</button>
        </form>
      </div>
    </div>

    <!-- مودال ایجاد کمپانی -->
    <div v-if="showCreateModal" class="modal modal-open">
      <div class="modal-box">
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" @click="showCreateModal = false">✕</button>
        <h2 class="text-lg font-bold mb-4 mt-4">ایجاد کمپانی جدید</h2>

        <!-- فرم ایجاد کمپانی -->
        <form @submit.prevent="createCompany" class="form-control">
          <label class="input input-bordered flex items-center gap-4">
            <Icon name="octicon:organization-16" size="18" class="ml-2"/>
            <input v-model="newCompany.name" type="text" class="grow" placeholder="نام کمپانی"/>
          </label>
          <label class="input input-bordered flex items-center gap-4 mt-4">
            <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
            <input v-model="newCompany.address" type="text" class="grow" placeholder="آدرس"/>
          </label>
          <label class="input input-bordered flex items-center gap-4 mt-4">
            <Icon name="material-symbols:phone-in-talk" size="18" class="ml-2"/>
            <input v-model="newCompany.phone" type="text" class="grow" placeholder="تلفن"/>
          </label>
          <button type="submit" class="btn btn-primary mt-4">ایجاد کمپانی</button>
        </form>
      </div>
    </div>
  </div>
</template>
