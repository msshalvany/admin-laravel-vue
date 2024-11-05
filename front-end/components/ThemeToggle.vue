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

function toggleTheme() {
  document.documentElement.setAttribute('data-theme', isDarkTheme.value ? 'dark' : 'light');
}

// هنگام بارگذاری اولیه، بررسی و تنظیم تم
onMounted(() => {
  if (localStorage.getItem('theme') === 'dark') {
    isDarkTheme.value = true;
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.setAttribute('data-theme', 'light');
  }
});

// ذخیره‌سازی تم در localStorage هنگام تغییر
watch(isDarkTheme, (newValue) => {
  localStorage.setItem('theme', newValue ? 'dark' : 'light');
});
</script>
