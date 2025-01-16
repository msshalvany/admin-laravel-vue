<script setup>
import {useAlertStateSuccess, useAlertText} from "~/composables/states.js";

let State = useAlertStateSuccess();
let text = useAlertText();

const closeAlert = () => {
  State.value = false;
};
</script>

<template>
  <transition name="custom-fade">
    <div class="toast toast-top toast-start" style="z-index: 14;" v-if="State">
      <div class="alert alert-success alert-success-soft flex items-center">
        <Icon name="uil:check-circle" size="24"></Icon>
        <span class="ml-2 flex-grow">{{ text }}</span>
        <button class="btn-close mt-0.5 text-sm pointer-events-auto" @click="closeAlert">✖</button>
      </div>
    </div>
  </transition>
</template>

<style>
/* انیمیشن fade سفارشی */
.custom-fade-enter-active {
  transition: transform 0.3s ease, opacity 0.5s ease;
}

.custom-fade-leave-active {
  animation: slide-out 0.6s forwards;
}

/* انیمیشن رفتن راست سپس چپ و ناپدید شدن */
@keyframes slide-out {
  0% {
    transform: translateX(0);
    opacity: 1;
  }
  30% {
    transform: translateX(20px);
  }
  100% {
    transform: translateX(-50px);
    opacity: 0;
  }
}
</style>
