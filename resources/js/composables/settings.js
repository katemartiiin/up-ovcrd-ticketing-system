import axios from 'axios'
import { useToast } from 'vue-toastification'

export default function useSettings() {

    const toast = useToast()

    const fetch = async (items) => {
        await axios.get(route('admin.settings.fetch'))
                .then((response) => {
                    items.value = response.data.items
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const update = async (items) => {
        axios.post(route('admin.settings.update'), {
                settings: items.value
            })  
            .then((response) => {
                toast.success(response.data.message, {
                    timeout: 5000
                })
            }).catch((error) => {
                toast.error(error.response.data.message, {
                    timeout: 2000
                })
            })
    }

    return {
        fetch,
        update
    }
}