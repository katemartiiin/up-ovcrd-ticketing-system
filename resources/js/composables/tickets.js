import axios from 'axios'
import { ref } from 'vue'
import { useToast } from 'vue-toastification'

export default function useTickets() {

    const toast = useToast()
    const errors = ref([])

    /* Client functions */
    const fetch = async (params, options, tickets) => {
        await axios.post(route('tickets.fetch'), {
                    keyword: params.keyword,
                    office_id: params.office_id,
                    process_id: params.process_id,
                    status: params.status,
                    options: options
                }).then((response) => {
                    tickets.value = response.data.items.data
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

    const fetchNotes = async (ticketId, notes) => {
        await axios.get(route('tickets.fetch.notes', ticketId))
                .then((response) => {
                    notes.value = response.data.notes
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const store = async (payload, initialPayload, selectedOffice, selectedResearch) => {
        errors.value = []
        try {
            var form = new FormData();
            form.append('research_id', payload.research_id)
            form.append('office_id', payload.office_id)
            form.append('process_id', payload.process_id)
            form.append('title', payload.title)
            form.append('description', payload.description)

            if (payload.files.length > 0) {
                for (var i = 0; i < payload.files.length; i++) {
                    form.append('files[' + i + ']', payload.files[i])
                }
            }

            toast.info('Creating your ticket...', {
                timeout: 2000
            })
    

            await axios.post(route('tickets.store'), form)
                    .then((response) => {
                        selectedOffice.value = ''
                        selectedResearch.value = ''
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

        } catch (error) {
            if (error.response.status == 422) {
                errors.value = error.response.data.errors
                toast.error(error.response.data.message, {
                    timeout: 2000
                })
            }
        }
    }

    const note = async (payload, initialPayload, ticketId) => {
        errors.value = []

        var form = new FormData();
        form.append('ticket_id', ticketId)
        form.append('note', payload.note)

        if (payload.files.length > 0) {
            for (var i = 0; i < payload.files.length; i++) {
                form.append('files[' + i + ']', payload.files[i])
            }
        }

        toast.info('Adding your note...', {
            timeout: 2000
        })

        await axios.post(route('tickets.note'), form)
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

    const resolve = async (ticketId) => {
        
        await axios.patch(route('tickets.resolve', ticketId))
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

    const restore = async (ticketId) => {
        await axios.patch(route('tickets.restore', ticketId))
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

    const remove = async (ticketId) => {
        await axios.delete(route('tickets.destroy', ticketId))
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

    const followUp = async (ticketId) => {
        toast.info('Requesting follow-up...', {
            timeout: 2000
        })
        await axios.patch(route('tickets.follow', ticketId))
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

    /* Staff functions */
    const staffFetch = async (params, options, tickets) => {
        await axios.post(route('staff.tickets.fetch'), {
                        keyword: params.keyword,
                        process_id: params.process_id,
                        tab: params.tab,
                        options: options,
                }).then((response) => {
                    tickets.value = response.data.items.data
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

    const staffFetchNotes = async (ticketId, notes) => {
        await axios.get(route('staff.tickets.fetch.notes', ticketId))
                .then((response) => {
                    notes.value = response.data.notes
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    const staffNote = async (payload, initialPayload, ticketId) => {
        errors.value = []
        var form = new FormData();
        form.append('ticket_id', ticketId)
        form.append('note', payload.note)

        if (payload.files.length > 0) {
            for (var i = 0; i < payload.files.length; i++) {
                form.append('files[' + i + ']', payload.files[i])
            }
        }

        toast.info('Adding your note...', {
            timeout: 2000
        })

        await axios.post(route('staff.tickets.note'), form)
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

    const staffTransfer = async (payload, initialPayload) => {
        errors.value = []
        await axios.post(route('staff.tickets.transfer'), payload)
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

    const staffCompleted = async (ticketId) => {
        
        await axios.patch(route('staff.tickets.completed', ticketId))
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

    const staffClaim = async (ticketId) => {
        
        await axios.patch(route('staff.tickets.claim', ticketId))
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

    /* Vcd functions */
    const vcdNote = async (payload, initialPayload, ticketId) => {
        errors.value = []
        var form = new FormData();
        form.append('ticket_id', ticketId)
        form.append('note', payload.note)

        if (payload.files.length > 0) {
            for (var i = 0; i < payload.files.length; i++) {
                form.append('files[' + i + ']', payload.files[i])
            }
        }

        toast.info('Adding your note...', {
            timeout: 2000
        })

        await axios.post(route('vcd.tickets.note'), form)
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

    const vcdFetch = async (params, options, tickets) => {
        await axios.post(route('vcd.tickets.fetch'), {
                    keyword: params.keyword,
                    process_id: params.process_id,
                    trashed: params.trashed,
                    options: options
                }).then((response) => {
                    tickets.value = response.data.items.data
                    options.to = response.data.items.to
                    options.from = response.data.items.from
                    options.total = response.data.items.total
                    options.current_page = response.data.items.current_page
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 200
                    })
                })
    }

    const vcdAssign = async (payload, initialPayload) => {

        toast.info('Assigning ticket...', {
            timeout: 2000
        })

        await axios.put(route('vcd.tickets.assign', payload.ticket_id), payload)
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

    const vcdFetchNotes = async (ticketId, notes) => {
        await axios.get(route('vcd.tickets.fetch.notes', ticketId))
                .then((response) => {
                    notes.value = response.data.notes
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        timeout: 2000
                    })
                })
    }

    return {
        // Client
        note,
        fetch,
        store,
        remove,
        errors,
        restore,
        resolve,
        followUp,
        fetchNotes,
        // Staff
        staffNote,
        staffFetch,
        staffClaim,
        staffTransfer,
        staffCompleted,
        staffFetchNotes,
        // Vcd
        vcdNote,
        vcdFetch,
        vcdAssign,
        vcdFetchNotes
    }
}