<template>
  <div class="form-control w-full">
    <div class="relative">
      <label class="floating-label">
        <span>
          {{ label }} <slot name="icon"></slot>
        </span>
        <input
            v-model="search"
            :placeholder="placeholder"
            class="input input-bordered w-full"
            @focus="showDropdown = true"
            @blur="handleBlur"
        />
      </label>
      <ul
          v-if="showDropdown"
          class="absolute z-50 mt-1 w-full bg-base-100 rounded-box shadow-lg max-h-60 overflow-auto"
      >
        <li
            v-for="item in filteredItems"
            :key="item.id"
            @mousedown.prevent="toggleSelect(item.id)"
            class="cursor-pointer px-4 py-2 hover:bg-base-200 flex items-center"
        >
          <input
              type="checkbox"
              class="checkbox checkbox-sm ml-2"
              :checked="isSelected(item.id)"
              @change.prevent
          />
          {{ item.text }}
        </li>
      </ul>
    </div>

    <!-- نمایش آیتم‌های انتخاب‌شده -->
    <div class="mt-2 flex flex-wrap gap-2">
      <div v-for="id in modelValue" :key="id" class="badge badge-primary">
        {{ getTextById(id) }}
        <button class="" @click="removeItem(id)">
          <Icon name="ic:sharp-cancel" size="18" class="mt-1"/>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    required: true
  },
  items: {
    type: Array,
    required: true
  },
  label: String,
  placeholder: String
})

const emit = defineEmits(['update:modelValue'])

const search = ref('')
const showDropdown = ref(false)

const filteredItems = computed(() =>
    props.items.filter(item =>
        item.text.toLowerCase().includes(search.value.toLowerCase())
    )
)

function isSelected(id) {
  return props.modelValue.includes(id)
}

function toggleSelect(id) {
  const newValue = [...props.modelValue]
  const index = newValue.indexOf(id)
  if (index === -1) {
    newValue.push(id)
  } else {
    newValue.splice(index, 1)
  }
  emit('update:modelValue', newValue)
}

function removeItem(id) {
  const newValue = props.modelValue.filter(item => item !== id)
  emit('update:modelValue', newValue)
}

function getTextById(id) {
  const found = props.items.find(item => item.id === id)
  return found ? found.text : '---'
}

function handleBlur() {
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

watch(
    () => props.modelValue,
    (newVal) => {
      if (!Array.isArray(newVal)) {
        emit('update:modelValue', [])
      }
    },
    { immediate: true }
)
</script>
