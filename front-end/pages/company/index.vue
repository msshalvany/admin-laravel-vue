<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {loaderfun} from "~/composables/statFunc.js";
import {basUrl} from "~/composables/states.js";
const { $toast } = useNuxtApp();

let companyToDelete = ref(null)

const companies = ref([]);

const selectedCompany = ref(null);

const showEditModal = ref(false); // متغیر برای کنترل نمایش مودال ویرایش

const showCreateModal = ref(false); // متغیر برای کنترل نمایش مودال ایجاد کمپانی

const showDeleteConfirmation = ref(false);

// برای ذخیره داده‌های فرم ایجاد کمپانی
const newCompany = ref({
  name: '',
  address: '',
  phone: '',
});
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
      $toast('کمپانی با موفقیت ویرایش شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
      showEditModal.value = false;
      fetchCompanies(); // بارگذاری مجدد لیست کمپانی‌ها
    } else {
      console.error('Error updating company:', response.status);
      $toast('مشکلی در ویرایش کمپانی رخ داده است', {
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
        `${basUrl().value}/companies/${companyToDelete.value}`,
        {
          method: "delete",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      $toast('کمپانی با موفقیت حذف شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
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
      $toast('کمپانی با موفقیت ایجاد شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
      showCreateModal.value = false;
      fetchCompanies(); // بارگذاری مجدد لیست کمپانی‌ها
    } else {
      console.error('Error creating company:', response.status);
      $toast('مشکلی در ایجاد کمپانی رخ داده است', {
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
          <button class="btn btn-success" onclick="createCompany_modal.showModal()">
            کمپانی جدید
            <Icon name="bi:building-fill-add" size="18"/>
          </button>
        </div>
      </div>
    </div>
    <!-- جدول کمپانی‌ها -->
    <div class="mt-4 shadow-lg p-1 rounded-xl">
      <table class="table">
        <thead class="bg-base-content/20">
        <tr class="text-center">
          <th>id</th>
          <th>نام</th>
          <th>آدرس</th>
          <th>تلفن</th>
          <th>مدیریت</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="company in companies" :key="companies.id" class="hover:bg-base-300 transition delay- text-center">
          <th>{{ company.id }}</th>
          <td>{{ company.name }}</td>
          <td>{{ company.address }}</td>
          <td>{{ company.phone }}</td>
          <td class="">
            <div @click="companyToDelete = company.id " onclick="confirmDelete_modal.showModal()" class="tooltip"
                 data-tip="حذف">
              <button class="btn btn-xs btn-error">
                <Icon name="i-heroicons-trash-20-solid" size="18"/>
              </button>
            </div>
            <div @click="editCompany(company)" onclick="editCompany_modal.showModal()" class="tooltip" data-tip="ویرایش">
              <button class="btn btn-xs btn-info">
                <Icon name="i-heroicons-pencil-square-20-solid" size="18"/>
              </button>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if="companies.length===0" class="w-full text-center my-4">
        <span class="loading loading-ring w-1/12"></span>
      </div>
    </div>


    <dialog id="editCompany_modal" class="modal">
      <div class="modal-box relative">
        <h2 class="text-lg font-bold mb-6 text-center mt-2">

        </h2>

        <!-- فرم ویرایش کمپانی -->
        <form v-if="selectedCompany" class="space-y-6">
          <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
            <legend  class="fieldset-legend text-lg font-bold flex items-center gap-2">
              ویرایش کمپانی - {{ selectedCompany.name }}
              <Icon name="octicon:organization-16" size="18" class="ml-2"/>
            </legend>
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
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button type="submit" @click="updateCompany" class="btn btn-primary w-full mt-6">ویرایش کمپانی</button>
            </form>
          </fieldset>
        </form>
      </div>
    </dialog>
    <dialog id="createCompany_modal" class="modal">
      <div class="modal-box relative">
        <!-- دکمه بستن -->
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn"
                @click="showCreateModal = false">
          <Icon name="material-symbols:close"/>
        </button>

        <!-- عنوان فرم -->
        <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
          <legend class="fieldset-legend text-lg font-bold flex items-center gap-2">
            کپانی جدید
            <Icon name="bi:building-fill-add" size="18"/>
          </legend>
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
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
              <Icon name="material-symbols:close"/>
            </button>
            <button type="submit" @click="createCompany" class="btn btn-primary w-full mt-6">ایجاد کمپانی</button>
          </form>
        </fieldset>
      </div>
    </dialog>
    <dialog id="confirmDelete_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این کمپانی را حذف کنید؟</h2>
        <form method="dialog">
          <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
            <Icon name="material-symbols:close"/>
          </button>
          <div class="modal-action" dir="ltr">
            <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
          </div>
        </form>
      </div>
    </dialog>
  </div>
</template>
