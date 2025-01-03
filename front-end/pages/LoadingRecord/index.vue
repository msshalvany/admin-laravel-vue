<script setup>
import {basUrl} from "~/composables/states.js";

import {ref} from "vue";

const date = '1399-5-8'
const companies = ref([]);
const selectedCompany = ref(null);
const trucks = ref([]);
const selectedTrucks = ref(null)

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
      companies.value = data.data.map(item => ({
        label: item.name,  // نام راننده برای نمایش
        value: item.id    // شناسه راننده برای ارسال
      }));
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

const fetchTrucks = async () => {
  let id = selectedCompany.value.value
  try {
    const response = await fetch(`${basUrl().value}/companies/trucks/${id}`, {
      method: 'GET',
      headers: {
        Authorization: `Bearer ${useCookie('jwt').value}`,
        'Content-Type': 'application/json',
      },
    });
    if (response.ok) {
      const data = await response.json();
      trucks.value = data.data.map((item) => {
        let plate_number = `شماره پلاک :${item.plate_number}`
        return {
          label: plate_number,
          value: item.id,
          color: item.color
        }
      });
      console.log(trucks.value)
    }
  } catch (error) {
    console.error('Error:', error);
  }
};

onMounted(function () {
  fetchCompanies()
})
watch(selectedCompany, function () {
  fetchTrucks()
})
watch(selectedTrucks, function () {
  console.log(selectedTrucks.value)
})
</script>

<template>
  <div class="p-2">
    <!-- مسیر صفحه -->
    <div class="px-4">
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
              <Icon name="ph:truck-trailer-light" class="ml-1" size="18"/>
              تردد
            </a>
          </li>
          <li>
            <a>
              <Icon name="mdi:truck-fast-outline" size="18"/>
              ثبت تردد
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="w-8/12 md:w-12/12 p-4 m-auto flex flex-col mt-4 border-2 rounded-2xl">
      <h1 class="text-center text-2xl font-bold">ثبت تردد</h1>
      <div class="mt-2">
        <div>
          <USelectMenu v-model="selectedCompany" size="xl"
                       :options="companies"
                       placeholder="انتخاب شرکت"
                       :popper="{ arrow: true }"
                       searchable
                       searchable-placeholder="جستجو....."
                       class="p-0 input"
          />
        </div>
        <div class="mt-4">
          <USelectMenu v-model="selectedTrucks" size="xl"
                       :options="trucks"
                       placeholder="انتخاب ماشین"
                       :popper="{ arrow: true }"
                       searchable
                       icon=""
                       searchable-placeholder="جستجو....."
                       class="mt-2 "
          >
            <template #option="{ option:truck }">
              <span class="h-2 w-2 rounded-full" :style="`background-color:${truck.color}`"></span>
              <span class="truncate">{{ truck.label }}</span>
            </template>
          </USelectMenu>
        </div>
        <div class="mt-4">
          <label class="input input-bordered flex items-center gap-2">
            <Icon name="material-symbols-light:calendar-clock-rounded" size="24"/>
            <input id="datePicker" type="text" class="grow" placeholder="تاریخ تردد" />
          </label>
          <date-picker
              v-model="date"
              type="datetime"
              format="jYYYY/jMM/jDD HH:mm"
              custom-input="#datePicker"
          />
        </div>

      </div>
    </div>
    <!-- جدول رانندگان -->
  </div>
</template>

<style scoped>

</style>