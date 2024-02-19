import axios from 'axios'
import { useToast } from 'vue-toastification'

export default function useReports() {

    const toast = useToast()

    const fetch = async (reports, options) => {
        await axios.post(route('vcd.reports.fetch'), {
                    options: options
                })
                .then((response) => {
                    reports.value = response.data.items.data
                    options.to = response.data.items.to
                    options.from = response.data.items.from
                    options.total = response.data.items.total
                    options.current_page = response.data.items.current_page
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const generate = async (filters) => {
        toast.info('Generating your report...', {
            timeout: 2000
        })
        await axios.post(route('vcd.reports.generate'), filters)
                .then((response) => {
                    window.open(response.data.url)
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const charts = async (dates, bar, pie, graph, barText) => {
        await axios.post(route('vcd.charts'), {
                    dates: dates.value
                }).then((response) => {
                    barText.value = response.data.barText
                    bar.value = response.data.barData
                    pie.value = response.data.pieData
                    graph.value = response.data.graphData
                })
    }

    const filterCharts = async (dates, bar, pie) => {
        await axios.post(route('vcd.charts.filter'), {
                    dates: dates.value
                }).then((response) => {
                    bar.value = response.data.barData
                    pie.value = response.data.pieData
                })
    }


    return {
        fetch,
        charts,
        generate,
        filterCharts
    }
}