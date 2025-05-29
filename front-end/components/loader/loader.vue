<script setup lang="ts">
const loader = useLoader() // فرض می‌کنیم composable یا state داره
</script>

<template>
  <Teleport to="body">
    <Transition>
      <div
          v-show="loader"
          class="loader bg-base-100 w-screen h-screen fixed top-0 left-0 flex justify-center items-center"
          style="z-index: 999999;"
      >
        <span class="loading loading-spinner w-2/12"></span>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.loader {
  opacity: 85%;
}
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}
.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
