<script setup>
import {useAlertStateWarning, useAlertText} from "~/composables/states.js";
let State = useAlertStateWarning()
let text = useAlertText()
</script>

<template>
  <transition name="fade">
    <div class="toast  toast-top toast-start" style="z-index: 14;" v-if="State">
      <div class="alert alert-warning alert-warning-soft">
        <Icon name="ic-round-warning" size="24"></Icon>
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