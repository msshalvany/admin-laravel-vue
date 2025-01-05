<script setup>
import {basUrl} from "~/composables/states.js";
import {ref} from "vue";

const date = '1399-5-8'
const companies = ref([]);
const selectedCompany = ref(null);

const trucks = ref([]);
const selectedTrucks = ref(null)

const locations = ref([])
const selectedLocation = ref(null)
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
      locations.value = data.data.map(item => ({
        label: item.location_name,  // نام راننده برای نمایش
        value: item.id    // شناسه راننده برای ارسال
      }));
    } else {
      console.error('Error fetching location:', response.status);
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
  fetchLocation()
})
watch(selectedCompany, function () {
  fetchTrucks()
})
watch(selectedTrucks, function () {
  console.log(selectedTrucks.value)
})
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
    </div>
    <div class="w-8/12 md:w-12/12 p-4 m-auto flex flex-col mt-4 shadow-xl rounded-2xl">
      <div class="mt-2">
        <div>
          <USelectMenu v-model="selectedCompany" size="xl"
                       :options="companies"
                       placeholder="انتخاب شرکت"
                       :popper="{ arrow: true }"
                       searchable
                       icon="octicon:organization-16"
                       searchable-placeholder="جستجو....."
                       class="p-0"
          />
        </div>
        <div class="mt-4">
          <USelectMenu v-model="selectedTrucks" size="xl"
                       :options="trucks"
                       placeholder="انتخاب ماشین"
                       :popper="{ arrow: true }"
                       searchable
                       icon="ph:truck-duotone"
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
            <input id="datePicker" type="text" class="grow" placeholder="تاریخ تردد"/>
          </label>
          <date-picker
              v-model="date"
              type="datetime"
              format="jYYYY/jMM/jDD HH:mm"
              custom-input="#datePicker"
          />
        </div>
        <div class="mt-4">
          <USelectMenu v-model="selectedLocation" size="xl"
                       :options="locations"
                       placeholder="انتخاب مکان"
                       :popper="{ arrow: true }"
                       searchable
                       icon="ic:outline-place"
                       searchable-placeholder="جستجو....."
                       class="mt-2 "
          >
          </USelectMenu>
        </div>
      </div>
    </div>
    <!-- جدول رانندگان -->
  </div>
</template>

<style scoped>

</style>