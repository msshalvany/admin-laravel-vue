<!-- components/ThemeToggle.vue -->
<template>
  <label class="swap swap-rotate mt-1">
    <input type="checkbox" v-model="isDarkTheme" @change="toggleTheme" />
    <!-- نمایش آیکن خورشید اگر تم روشن باشد -->
    <Icon v-if="!isDarkTheme" name="uil:sun" size="24"></Icon>
    <!-- نمایش آیکن ماه اگر تم تاریک باشد -->
    <Icon v-else name="uil:moon" size="24"></Icon>
  </label>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const isDarkTheme = ref(false);

function updateTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme); // تغییر data-theme
  localStorage.setItem('theme', theme); // ذخیره تم در localStorage
}

function toggleTheme() {
  const theme = isDarkTheme.value ? 'dark' : 'light';
  updateTheme(theme);
}

// هنگام بارگذاری اولیه، بررسی تم ذخیره‌شده
onMounted(() => {
  const savedTheme = localStorage.getItem('theme') || 'light';
  isDarkTheme.value = savedTheme === 'dark';
  updateTheme(savedTheme);
});
</script>
