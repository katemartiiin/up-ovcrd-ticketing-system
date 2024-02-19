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
                    options: options,
                    type: filters.type,
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

    const activate = async (id) => {
        await axios.patch(route('admin.users.activate', id))
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

    const updateProfile = async (payload) => {
            errors.value = []
            try {
                var form = new FormData();
                form.append('first_name', payload.first_name)
                form.append('last_name', payload.last_name)
    
                if (payload.new_avatar.length > 0) {
                    form.append('avatar', payload.new_avatar[0])
                }
    
                toast.info('Updating your profile...', {
                    timeout: 2000
                })
        
    
                await axios.post(route('profile.update'), form)
                        .then((response) => {
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
    
            } catch (error) {
                if (error.response.status == 422) {
                    errors.value = error.response.data.errors
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                }
            }
    }


    return {
        fetch,
        store,
        errors,
        update,
        activate,
        updateProfile
    }
}