<script setup>
import {ref, onMounted} from 'vue';
import {useCookie} from 'nuxt/app';
import {basUrl} from "~/composables/states.js";
const { $toast } = useNuxtApp();

const locations = ref([]);

const selectedLocations = ref(null);

const showEditModal = ref(false); // متغیر برای کنترل نمایش مودال ویرایش

const showCreateModal = ref(false); // متغیر برای کنترل نمایش مودال ایجاد مکان

const showDeleteConfirmation = ref(false);

// برای ذخیره داده‌های فرم ایجاد مکان
const newLocations = ref({
  location_name: '',
  description: '',
});
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
      $toast('مکان با موفقیت ویرایش شد', {
        "theme": "colored",
        "type": "success",
        "rtl": true,
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
      showEditModal.value = false;
      fetchLocation(); // بارگذاری مجدد لیست مکان‌ها
    } else {
      console.error('Error updating location:', response.status);
      $toast('مشکلی در ویرایش مکان رخ داده است', {
        "theme": "colored",
        "type": "success",
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
      $toast("مکان با موفقیت حذف شد", {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
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
      $toast('مکان با موفقیت ایجاد شد', {
        "theme": "colored",
        "type": "success",
        "autoClose":"5000",
        "rtl": true,
        "dangerouslyHTMLString": true
      })
      showCreateModal.value = false;
      fetchLocation(); // بارگذاری مجدد لیست مکان‌ها
    } else {
      console.error('Error creating company:', response.status);
      $toast('مشکلی در ویرایش مکان رخ داده است', {
        "theme": "colored",
        "type": "error",
        "autoClose":"5000",
        "dangerouslyHTMLString": true
      })
    }
  } catch (error) {
    console.error('Error:', error);
    $toast('مشکلی رخ داده است', {
      "theme": "colored",
      "type": "error",
      "rtl": true,
      "autoClose":"5000",
      "dangerouslyHTMLString": true
    })
  }

  loaderfun(); // مخفی کردن لودر
};

const DeleteLocation = (item) => {
  selectedLocations.value = item
  showDeleteConfirmation.value = true; // نمایش مودال تایید حذف
};

onMounted(fetchLocation);
</script>

<template>
  <div class="p-4">
    <div class="card shadow-md px-5   rounded-lg">
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
          <button class="btn btn-success" onclick="createLocations_modal.showModal()">
            مکان جدید
            <Icon name="material-symbols:add-location-alt-outline-rounded" size="18"/>
          </button>
        </div>
      </div>
    </div>
    <!-- جدول مکان‌ها -->
    <div class="mt-4 shadow-lg p-1 rounded-xl">
      <table class="table">
        <thead class="bg-base-content/20">
        <tr class="text-center ">
          <th>id</th>
          <th>نام</th>
          <th>توضیحات</th>
          <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="location in locations" :key="location.id" class="hover:bg-base-300 transition delay- text-center">
          <th>{{ location.id }}</th>
          <td>{{ location.location_name }}</td>
          <td>{{ location.description }}</td>
          <td class="">
            <div @click="DeleteLocation(location)" onclick="confirmDelete_modal.showModal()" class="tooltip"
                 data-tip="حذف">
              <button class="btn btn-xs btn-error">
                <Icon name="i-heroicons-trash-20-solid" size="18"/>
              </button>
            </div>
            <div @click="editLocation(location)" onclick="editLocation_modal.showModal()" class="tooltip"
                 data-tip="ویرایش">
              <button class="btn btn-xs btn-info">
                <Icon name="i-heroicons-pencil-square-20-solid" size="18"/>
              </button>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
      <div v-if="locations.length===0" class="w-full text-center my-4">
        <span class="loading loading-ring w-1/12"></span>
      </div>
    </div>

    <!-- مودال ویرایش مکان -->
    <dialog id="editLocation_modal" class="modal">
      <div class="modal-box relative">
        <div v-if="selectedLocations!=null">
          <!-- فیلدست کل محتوا -->
          <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
            <legend class="fieldset-legend text-lg font-bold flex items-center gap-2">
              ویرایش مکان - {{ selectedLocations.location_name }}
              <Icon name="ic:outline-place" size="18"/>
            </legend>

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
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button type="submit" @click="updateLocations" class="btn btn-primary w-full mt-6">ویرایش مکان</button>
            </form>
          </fieldset>
        </div>
      </div>
    </dialog>


    <!-- مودال ایجاد مکان -->
    <dialog id="createLocations_modal" class="modal">
      <div class="modal-box">
        <!-- دکمه بستن -->
        <form class="space-y-6">
          <fieldset class="fieldset w-full bg-base-200 border border-base-300 p-4 rounded-box space-y-6">
            <legend class="fieldset-legend text-lg font-bold flex items-center gap-2">
              مکان جدید
              <Icon name="material-symbols:add-location-alt-outline-rounded" size="18"/>
            </legend>
            <!-- نام مکان -->
            <label class="floating-label input input-bordered flex items-center gap-4 w-full">
            <span class="flex items-center">
              <Icon name="material-symbols:my-location" size="18" class="ml-2"/>
              نام مکان
            </span>
              <input v-model="newLocations.location_name" type="text" class="grow" placeholder="نام مکان"/>
            </label>

            <!-- توضیحات -->
            <label class="floating-label textarea textarea-bordered flex items-center gap-4 w-full mt-3">
            <span class="flex items-center">
              <Icon name="fluent:text-description-20-regular" size="18" class="ml-2"/>
              توضیحات
            </span>
              <textarea v-model="newLocations.description" class="textarea textarea-bordered w-full"
                        placeholder="توضیحات"></textarea>
            </label>
            <form method="dialog">
              <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
                <Icon name="material-symbols:close"/>
              </button>
              <button @click="createLocations" type="submit" class="btn btn-primary w-full mt-6">ایجاد مکان</button>
            </form>
          </fieldset>
        </form>
      </div>
    </dialog>
    <dialog id="confirmDelete_modal" class="modal">
      <div class="modal-box">
        <br>
        <h2 class="text-lg font-bold mb-4">آیا مطمئن هستید که می‌خواهید این مکان را حذف کنید؟</h2>
        <div class="modal-action" dir="ltr">
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 close-btn">
              <Icon name="material-symbols:close"/>
            </button>
            <button class="btn btn-error" @click="confirmDelete">بله، حذف کن</button>
          </form>
        </div>
      </div>
    </dialog>
  </div>
</template>
