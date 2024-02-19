import axios from 'axios'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

export default function useOffices() {

    const toast = useToast()
    const errors = ref([])

    const fetch = async (searchParams, options, offices) => {
        await axios.post(route('admin.offices.fetch'), {
                    keyword: searchParams.keyword,
                    status: searchParams.status,
                    options: options
                }).then((response) => {
                    offices.value = response.data.items.data
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

    const store = async (payload, initialPayload) => {
        errors.value = []
        await axios.post(route('admin.offices.store'), payload)
                .then((response) => {
                    Object.assign(payload, initialPayload)
                    toast.success(response.data.message, {
                        timeout: 2000
                    })

                }).catch((error) => {
                    if (error.response.status == 422) {
                        errors.value = error.response.data.errors
                        toast.error('Invalid input', {
                            timeout: 2000
                        })
                    } else {
                        toast.error(error.response.data.message, {
                            timeout: 2000
                        })
                    }
                })
    }

    const update = async (office, addedProcess, removedProcess, removedUsers) => {
        await axios.post(route('admin.offices.update', office.value.id), office.value)
                .then((response) => {
                    addedProcess.value = []
                    removedProcess.value = []
                    removedUsers.value = []
                    
                    toast.success(response.data.message, {
                        timeout: 2000
                    })

                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const activate = async (id) => {
        await axios.patch(route('admin.offices.activate', id))
                .then((response) => {
                    toast.success(response.data.message, {
                        timeout: 2000
                    })
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    return {
        fetch,
        store,
        errors,
        update,
        activate
    }
}