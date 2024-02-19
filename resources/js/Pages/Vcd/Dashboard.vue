<script>
/* Vue */
import { ref, computed, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useReports from '@/composables/reports'
/* Components */
import AuthenticatedLayout from '@/Layouts/AuthenticatedVcd.vue'
import VueHighcharts from 'vue3-highcharts'

export default {
    components: {
        Head,
        VueHighcharts,
        AuthenticatedLayout,
    },
    setup() {
        const dates = ref([])

        const { charts, filterCharts } = useReports()

        const barText = ref('')
        const barData = ref([])
        const pieData = ref([])
        const graphData = ref([])

        const pieChartOptions = computed(() => ({
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },
            title: {
                text: 'Tickets per status',
            },
            plotOptions: {
                pie: {
                    innerSize: 100,
                    depth: 45
                }
            },
            series: [{
                name: 'Tickets',
                data: pieData.value
            }],
            colors: ['#fe5288', '#0183d6', '#f4a62f', '#9346dd', '#1aadce',
                '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']
        }))

        const barChartOptions = computed(() => ({
            chart: {
                type: 'column',
            },
            title: {
                text: barText.value,
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Total Number of Tickets'
                }
            },
            legend: {
                    enabled: false
                },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
            },
            series: [
                {
                    name: 'Tickets',
                    data: barData.value,
                    colorByPoint: true,
                }
            ],
            colors: ['#0183d6', '#f4a62f', '#fe5288', '#9346dd', '#1aadce',
                    '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']
        }))

        const chartOptions = computed(() => ({
            chart: {
                type: 'line',
            },
            title: {
                text: 'Monthly Inquiries',
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'Total Number of Tickets'
                }
            },
            legend: {
                    enabled: false
                },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b> of total<br/>'
            },
            series: [
                {
                    name: 'Tickets',
                    data: graphData.value,
                }
            ]
        }))

        const onRender = () => {
            console.log('Chart rendered');
        }

        const fetchCharts = async () => {
            await charts(dates, barData, pieData, graphData, barText)
        }

        const filter = async () => {
            await filterCharts(dates, barData, pieData)
        }

        onMounted(fetchCharts)

        return {
            pieChartOptions,
            barChartOptions,
            chartOptions,
            fetchCharts,
            graphData,
            onRender,
            barData,
            barText,
            pieData,
            filter,
            dates,
        }
    }
}
</script>
<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <div class="py-7 md:py-10 px-3">
            <div class="w-full flex flex-wrap px-2 md:px-5 mb-5">
                <div class="w-full md:w-1/3 flex grid grid-cols-4 gap-2 md:px-3">
                    <VueDatePicker v-model="dates" class="col-span-3" placeholder="Select range" :max-date="new Date()" :enable-time-picker="false" range></VueDatePicker>
                    <button @click="filter" type="button" class="bg-up-green text-white px-5 rounded-md">Filter</button>
                </div>
            </div>
            <div class="w-full flex flex-wrap px-2 md:px-5">
                <div class="w-full md:w-2/3 pb-7 md:px-3">
                    <vue-highcharts
                        type="chart"
                        :options="barChartOptions"
                        :redrawOnUpdate="true"
                        :oneToOneUpdate="false"
                        :animateOnUpdate="true"
                        @rendered="onRender"
                    />
                </div>
                <div class="w-full md:w-1/3 pb-7 md:px-3">
                    <vue-highcharts
                        type="chart"
                        :options="pieChartOptions"
                        :redrawOnUpdate="true"
                        :oneToOneUpdate="false"
                        :animateOnUpdate="true"
                        @rendered="onRender"
                    />
                </div>
                <div class="w-full md:px-3">
                    <vue-highcharts
                        type="chart"
                        :options="chartOptions"
                        :redrawOnUpdate="true"
                        :oneToOneUpdate="false"
                        :animateOnUpdate="true"
                        @rendered="onRender"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
