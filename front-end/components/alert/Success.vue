<script setup>
import {useAlertStateSuccess, useAlertText} from "~/composables/states.js";
let State = useAlertStateSuccess()
let text = useAlertText()
</script>

<template>
  <transition name="fade">
    <div class="toast  toast-top toast-start" style="z-index: 14;" v-if="State">
      <div class="alert alert-success alert-success-soft">
        <Icon name="uil:check-circle" size="24"></Icon>
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