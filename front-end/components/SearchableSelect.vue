<template>
  <div class="relative inline-block w-64" ref="dropdownRef">
    <label @click="toggleDropdown" class="floating-label">
      <span>
        {{ label }}
        <slot name="icon" />
      </span>
      <input type="text" :value=" selectedItem ? selectedItem.text :''" :placeholder="label" class="input input-md">
    </label>

    <div v-if="isOpen" class="absolute mt-2 w-full z-10 bg-base-100 shadow-lg rounded-lg">
      <input
          type="text"
          v-model="search"
          ref="searchInput"
          placeholder="جستجو..."
          class="input input-bordered w-full"
      />
      <ul class="max-h-60 overflow-y-auto">
        <li
            v-for="item in filteredItems"
            :key="item.id"
            @click="selectItem(item)"
            class="px-4 py-2 hover:bg-base-300 cursor-pointer"
        >
          {{ item.text }}
        </li>
        <li v-if="filteredItems.length === 0" class="px-4 py-2 text-gray-400">موردی یافت نشد</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
  label: String,
  placeholder: {
    type: String,
    default: 'جستجو...',
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const search = ref('')
const selectedItem = ref(null)

const dropdownRef = ref(null)
const searchInput = ref(null)

const toggleDropdown = async () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    search.value = ''
    await nextTick()
    searchInput.value?.focus()
  }
}

const filteredItems = computed(() =>
    props.items.filter(item =>
        item.text.toLowerCase().includes(search.value.toLowerCase())
    )
)

const selectItem = (item) => {
  selectedItem.value = item
  emit('update:modelValue', item.id)
  isOpen.value = false
}

// Handle click outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
