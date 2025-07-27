<script setup lang="ts">
  let step = ref(1);
let chang_step = (step_target) => {
  step.value = step_target;
  console.log(step.value)
}
</script>

<template>
  <div>
    <div class="p-4">
      <div class="card shadow-md px-5 rounded-lg">
        <div class="flex justify-between items-center mb-4">
          <div class="breadcrumbs text-sm">
            <ul class="flex items-center">
              <li>
                <nuxt-link to="/">
                  <Icon name="ic:baseline-home" size="18" class="ml-2"/>
                  خانه
                </nuxt-link>
              </li>
              <li>
                <a>
                  <Icon name="ph:truck-trailer-light" class="ml-1" size="18"/>
                  تردد
                </a>
              </li>
              <li>
                <a class="flex items-center">
                  <Icon name="streamline:shipping-transfer-truck-time-truck-shipping-delivery-time-waiting-delay"
                        size="18"/>
                  لیست تردد
                </a>
              </li>
              <li>
                <a class="flex items-center">
                  <Icon name="mdi:truck-fast-outline" size="18"/>
                  لیست تردد
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>


      <div class="overflow-x-auto mt-2 card shadow-lg p-1 rounded-2xl">
        <ul class="steps steps-vertical lg:steps-horizontal">
          <li class="step" :class="{'step-primary': step >=1}" @click="chang_step(1)">
            <span class="step-icon">
               <Icon name="mdi:truck-fast-outline" size="18"/>
            </span>
            انتخاب ماشین
          </li>
          <li class="step" :class="{'step-primary':step >=2}" @click="chang_step(2)">
          <span class="step-icon">
              <Icon name="healthicons:truck-driver" size="26"/>
          </span>
            انتخاب راننده
          </li>
          <li class="step" :class="{'step-primary':step >=3}" @click="chang_step(3)">
            <span class="step-icon">
              <Icon name="material-symbols:scale" size="26"/>
          </span>
            ثبت وزن و مکان
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>