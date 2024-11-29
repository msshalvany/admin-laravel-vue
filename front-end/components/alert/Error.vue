<script setup>
import {useAlertStateError, useAlertText} from "~/composables/states.js";

let State = useAlertStateError()
let text = useAlertText()
</script>

<template>
  <transition name="fade">
    <div class="toast toast-top toast-start" style="z-index: 14;" v-if="State">
      <div class="alert alert-error alert-error-soft">
        <Icon name="ooui-error" size="24"></Icon>
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