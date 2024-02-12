import axios from 'axios'
import { useToast } from 'vue-toastification'

export default function useNotifications() {

    const toast = useToast()

    const fetch = async (options, notifications) => {
        await axios.post(route('admin.notifications.fetch'), {
                    options: options
                }).then((response) => {
                    notifications.value = response.data.items.data
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

    const vcdFetch = async (options, notifications) => {
        await axios.post(route('vcd.notifications.fetch'), {
                    options: options
                }).then((response) => {
                    notifications.value = response.data.items.data
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

    const staffFetch = async (options, notifications) => {
        await axios.post(route('staff.notifications.fetch'), {
                    options: options
                }).then((response) => {
                    notifications.value = response.data.items.data
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

    const clientFetch = async (options, notifications) => {
        await axios.post(route('notifications.fetch'), {
                    options: options
                }).then((response) => {
                    notifications.value = response.data.items.data
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

    return {
        fetch,
        vcdFetch,
        staffFetch,
        clientFetch
    }
}