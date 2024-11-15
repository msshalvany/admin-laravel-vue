
<script setup>
import {select} from "#ui/ui.config/index.js";

const users = ref([]);
const fetchUsers = async () => {
  const response = await fetch('http://localhost:8000/api/users', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer ${useCookie('jwt').value}`, // ارسال توکن JWT در هدر برای احراز هویت
      'Content-Type': 'application/json',
    },
  });
  if (response.ok) {
    const data = await response.json();
    users.value = data.data.map(function (index) {
      return index.username + ' - ' +  index.mobile
    });
  }
};
onMounted(() => {
  fetchUsers();
  console.log(users)
});
const selected = ref()
const selectUser = ref('')
const selectPer = ref('')
</script>
>

<template>
  <div>
  <date-picker v-model="time" type="time" format="H:m"/>
  </div>
</template>

<style scoped>

</style>