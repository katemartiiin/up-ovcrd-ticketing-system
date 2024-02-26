import axios from 'axios'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

export default function useResearch() {

    const toast = useToast()
    const errors = ref([])

    const fetch = async(research, params, options) => {
        await axios.post(route('admin.research.fetch'), {
                    office_id: params.office_id,
                    keyword: params.keyword,
                    options: options
                })
                .then((response) => {
                    research.value = response.data.items.data
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
        await axios.post(route('admin.research.store'), payload)
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

    const update = async (payload, initialPayload) => {
        await axios.post(route('admin.research.update', payload.research_id), payload)
                .then((response) => {
                    Object.assign(payload, initialPayload)
                    toast.success(response.data.message, {
                        timeout: 2000
                    })
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const fetchAvailable = async(researchId, staffs) => {
        await axios.get(route('admin.research.available', researchId.value.id))
                .then((response) => {
                    staffs.value = response.data.staffs
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const fetchStaffs = async (researchId, staffs) => {
        await axios.get(route('admin.research.staffs', researchId.value.id))
                .then((response) => {
                    staffs.value = response.data.staffs
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const toggle = async (researchId) => {
        await axios.patch(route('admin.research.toggle', researchId))
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
        toggle,
        errors,
        update,
        fetchStaffs,
        fetchAvailable
    }
}