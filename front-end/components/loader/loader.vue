<script setup lang="ts">
let loader = useLoader()
</script>

<template>
  <Transition>
    <div
        class="loader bg-base-100 w-screen h-screen fixed flex justify-center items-center"
        style="z-index: 10000000;"
        v-if="loader"
    >
<!--      <div class="loading loading-ring"></div>-->
      <span class="loading loading-spinner w-2/12"></span>
    </div>
  </Transition>
</template>

<style scoped>
.loader {
  opacity: 85%;
}

/* we will explain what these classes do next! */
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>