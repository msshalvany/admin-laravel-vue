<script setup>
defineProps(['text'])
let active =  ref(true)
onMounted(()=>{
  setTimeout(()=>{
      active.value=false
  },3000)
})

function fadOute() {
  active.value=false
}
</script>

<template>
  <transition name="fade">
    <div class="toast toast-top toast-end" @click="fadOute" style="z-index: 14;" v-if="active">
      <div class="alert alert-error">
        <Icon name="uil:exclamation-circle" size="24"></Icon>
        <span>{{text}}</span>
      </div>
    </div>
  </transition>
</template>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>