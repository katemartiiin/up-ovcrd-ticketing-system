import axios from 'axios'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

export default function useUsers() {

    const toast = useToast()
    const errors = ref([])

    const fetch = async (users, filters, options) => {
        await axios.post(route('admin.users.fetch'), {
                    office_id: filters.office_id,
                    role: filters.role,
                    status: filters.status,
                    keyword: filters.keyword,
                    options: options
                })
                .then((response) => {
                    users.value = response.data.items.data
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
        toast.info('Creating new user...', {
            timeout: 2000
        })
        await axios.post(route('admin.users.store'), payload)
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

    const update = async (user) => {
        await axios.post(route('admin.users.update', user.id), user)
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

    const remove = async (id) => {
        await axios.delete(route('admin.users.delete', id))
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

    const restore = async (id) => {
        await axios.patch(route('admin.users.restore', id))
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
        remove,
        restore
    }
}