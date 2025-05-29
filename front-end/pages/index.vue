<script setup>

const countLoadingRecord = ref(0)
const monthlyData = ref([])

const fetchcountLoadingRecord = async () => {
  try {
    const response = await fetch(
        `${basUrl().value}/loading_records/count`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      const data = await response.json();
      countLoadingRecord.value = data.count
    } else {
      console.error("Error fetching drivers:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};
const fetchMonthlyTraffic = async () => {
  try {
    const response = await fetch(
        `${basUrl().value}/loading_records/monthly-count`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${useCookie("jwt").value}`,
            "Content-Type": "application/json",
          },
        }
    );

    if (response.ok) {
      const data = await response.json();
      monthlyData.value = data;

      options.value = {
        data: data,
        series: [{type: 'bar', xKey: 'month', yKey: 'entries'}],
      };
    } else {
      console.error("Error fetching monthly traffic:", response.status);
    }
  } catch (error) {
    console.error("Error:", error);
  }
};

const options = ref({data: [], series: []});
onMounted(function (){
  fetchcountLoadingRecord()
  fetchMonthlyTraffic()
})
</script>

<template>
  <div class="p4">
    <div class="text-center mt-4">
      <div class="stats shadow">
        <div class="stat">
          <div class="stat-figure text-primary">
            <Icon name="material-symbols-light:pending-actions" size="40"></Icon>
          </div>
          <div class="stat-title"></div>
          <div class="stat-value text-primary">{{ countLoadingRecord }}</div>
          <div class="stat-desc">تعداد کل تردد</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <Icon name="material-symbols-light:pending-actions" size="40"></Icon>
          </div>
          <div class="stat-value text-secondary">{{ countLoadingRecord }}</div>
          <div class="stat-desc">تعداد کل تردد</div>
        </div>

        <div class="stat">
          <div class="stat-value">{{ countLoadingRecord }}%</div>
          <div class="stat-desc text-secondary">تعداد کل تردد</div>
        </div>
      </div>
    </div>
    <div>
      <agCharts :options="options"/>
    </div>
  </div>
</template>

<style scoped>

</style>