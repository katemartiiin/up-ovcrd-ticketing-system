<script>
/* Vue */
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedClient.vue'
/* Components */
import UpModal from '@/Components/UpModal.vue'
import Dropdown from '@/Components/Dropdown.vue'
import ErrorText from '@/Components/ErrorText.vue'
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Link,
        Head,
        UpModal,
        Dropdown,
        ErrorText,
        Pagination,
        AuthenticatedLayout
    },
    props: ['ticketOffices', 'researchIds'],
    setup(props) {
        const { fetch, store, note, remove, errors, resolve, fetchNotes } = useTickets()

        const offices = computed(() => props.ticketOffices)

        const headers = ref([
            { text: 'Ticket #', value: 'control_number' },
            { text: 'Section', value: 'officeCode' },
            { text: 'Type', value: 'processName' },
            { text: 'Status', value: 'statusLabel' },
            { text: 'Date', value: 'createdDate'},
            { text: 'Action', value: 'action' }
        ])

        const searchParams = reactive({
            keyword: '',
            office: '',
            office_id: '',
            process_id: '',
            status: ''
        })

        const initialPayload = {
            office_id: '',
            process_id: '',
            research_id: '',
            title: '',
            description: '',
            files: []
        }

        const payload = reactive({...initialPayload})

        const tickets = ref([])
        
        const creatingTicket = ref(false)
        const addingNote = ref(false)

        const selectedResearch = ref('')

        const selectedOffice = ref('')
        const processes = ref([])

        const selectedTicket = ref('')
        const notes = ref([])

        const showCreate = ref(false)
        const showFilters = ref(false)

        const showNotes = ref(false)

        const initialNote = {
            note: '',
            files: []
        }

        const payloadNote = reactive({...initialNote})
        const noteFiles = ref(null)

        const ticketFiles = ref(null)

        const pageOptions = reactive({
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        })

        const handleFileUpload = async (event) => {
            payload.files = ticketFiles.value.files
        }

        const handleNoteUpload = async (event) => {
            payloadNote.files = noteFiles.value.files
        }

        const filters = () => {
            showFilters.value = !showFilters.value
        }

        const removeFile = (index) => {
            payload.files.splice(index, 1)
        }

        const toggleCreate = () => {
            showCreate.value = !showCreate.value
            errors.value = []
        }

        const removeTicket = async (ticketId) => {
            await remove(ticketId)
            fetchTickets()
        }

        const resolveTicket = async (ticketId) => {
            await resolve(ticketId)
            fetchTickets()
        }

        const fetchTickets = async () => {
            await fetch(searchParams, pageOptions, tickets)
        }

        const createTicket = async () => {
            creatingTicket.value = true
            await store(payload, initialPayload, selectedOffice, selectedResearch)
            fetchTickets()
            creatingTicket.value = false
        }

        const search = () => {
            fetchTickets()
        }

        const getTicketNotes = async () => {
            await fetchNotes(selectedTicket.value, notes)
        }

        const toggleNotes = (ticketId) => {
            if (!showNotes.value) {
                selectedTicket.value = ticketId
                getTicketNotes()
            } else {
                selectedTicket.value = ''
                notes.value = []
                noteFiles.value = null
            }

            showNotes.value = !showNotes.value
            errors.value = []
        }

        const addNote = async () => {
            addingNote.value = true
            await note(payloadNote, initialNote, selectedTicket.value)
            getTicketNotes()
            addingNote.value = false
        }

        watch(
            () => selectedOffice.value,
            (value) => {
                if (value) {
                    processes.value = value.processes
                    payload.office_id = value.id
                }
            }
        )

        watch(
            () => searchParams.office,
            (value) => {
                if (value) {
                    processes.value = value.processes
                    searchParams.office_id = value.id
                } else {
                    searchParams.office_id = ''
                }
            }
        )

        watch(
            () => selectedResearch.value,
            (value)  => {
                payload.research_id = value.id
            }
        )

        watch(
            () => payload.research_id,
            (value) => {
                if (value != "") {
                    selectedOffice.value = selectedResearch.value.office
                    payload.office_id = selectedResearch.value.office_id
                    payload.process_id = ''
                }
            }
        )

        onMounted(fetchTickets)

        return {
            notes,
            errors,
            search,
            addNote,
            payload,
            offices,
            tickets,
            headers,
            filters,
            processes,
            showNotes,
            noteFiles,
            addingNote,
            showCreate,
            removeFile,
            pageOptions,
            showFilters,
            toggleNotes,
            payloadNote,
            ticketFiles,
            searchParams,
            createTicket,
            removeTicket,
            toggleCreate,
            fetchTickets,
            resolveTicket,
            creatingTicket,
            selectedOffice,
            getTicketNotes,
            selectedTicket,
            selectedResearch,
            handleNoteUpload,
            handleFileUpload,
        }
    }
}
</script>

<template>
    <Head title="Tickets" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Tickets</h1>
            <p class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} Active</p>
        </template>

        <div class="py-5 px-5 md:px-10">

            <!-- Create Ticket -->
            <UpModal :show="showCreate" @close="toggleCreate">
                <div class="w-full flex flex-wrap">
                    <div class="w-full flex bg-up-green text-white py-5 px-7">
                        <h2 class="text-xl">New Ticket</h2>
                        <button @click="toggleCreate" type="button" class="absolute right-7 text-lg">x</button>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 mb-5">
                            <div class="w-full">
                                <label class="text-up-green font-semibold block mb-2">Section <span class="text-red-500">*</span></label>
                                <select v-model="selectedOffice" id="countries" class="border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 bg-gray-50">
                                    <option disabled selected value="">Select section</option>
                                    <option v-for="office in offices" :key="office.id" :value="office">{{ office.short_code }}</option>
                                </select>
                                <ErrorText v-if="errors.office_id" :message="errors.office_id[0]" />
                            </div>
                            <div class="w-full">
                                <label class="text-up-green font-semibold block mb-2">Type <span class="text-red-500">*</span></label>
                                <select v-model="payload.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                    <option disabled selected value="">Select type</option>
                                    <option v-for="process in processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                                </select>
                                <ErrorText v-if="errors.process_id" :message="errors.process_id[0]" />
                            </div>
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-up-green font-semibold block mb-2">Research ID <small>(optional)</small></label>
                            <select v-model="selectedResearch" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option selected value="">Select ID</option>
                                <option v-for="research in researchIds" :key="research.id" :value="research">{{ research.research_code }}</option>
                            </select>
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-up-green font-semibold block mb-2">Title <span class="text-red-500">*</span></label>
                            <input v-model="payload.title" type="text" class="w-full bg-gray-50 border-gray-300 rounded-md mr-1 p-2" placeholder="Title">
                            <ErrorText v-if="errors.title" :message="errors.title[0]" />
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-up-green font-semibold block mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea v-model="payload.description" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter ticket description" rows="5"></textarea>
                            <ErrorText v-if="errors.description" :message="errors.description[0]" />
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-up-green font-semibold block mb-2">Upload file/s</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG or PDF (MAX. 10MB)</p>
                                    </div>
                                    <input id="dropzone-file" ref="ticketFiles" type="file" @change="handleFileUpload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple />
                                </label>
                            </div> 
                            <div v-if="payload.files.length" class="my-3 flex">
                                <div class="w-full flex grid grid-cols-4 gap-2">
                                    <span v-for="(file, index) in payload.files" :key="index" class="text-sm bg-gray-300 py-1 px-2 rounded-md bg-opacity-50">{{ file.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleCreate" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="createTicket" type="button" class="bg-up-green-light hover:bg-up-green text-white px-3 py-2 rounded-md transition duration-200 ease-in-out" :disabled="creatingTicket">
                                <span v-if="!creatingTicket">Create</span>
                                <span v-else class="text-center">
                                    <div role="status">
                                        <svg aria-hidden="true" class="w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                        </svg>
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Create Ticket -->

            <!-- Notes Ticket -->
            <UpModal :show="showNotes" @close="toggleNotes(selectedTicket)">
                <div class="w-full flex flex-wrap">
                    <div class="w-full flex bg-up-maroon text-white py-5 px-7">
                        <h2 class="text-xl">Ticket Notes</h2>
                        <button @click="toggleNotes(selectedTicket)" type="button" class="absolute right-7 text-lg">x</button>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full bg-gray-100 h-48 rounded-md shadow-md flex flex-wrap mt-2 mb-5 p-5 overflow-y-scroll">
                            <template v-if="notes.length">
                                <template v-for="note in notes" :key="note.id">
                                    <div class="w-full flex flex-wrap text-sm">
                                        <div class="w-1/3">
                                            <span class="md:text-sm text-xs">{{ note.createdDate }}</span>
                                        </div>
                                        <div class="w-2/3">
                                            <span>{{ note.note }}</span><br>
                                            <span class="font-semibold">{{ note.authorName }}</span>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <template v-else>
                                <p class="text-sm my-2 italic text-gray-600">No notes added yet. </p>
                            </template>
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-up-maroon font-semibold block mb-2">Add note</label>
                            <textarea v-model="payloadNote.note" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter ticket note" rows="3"></textarea>
                            <ErrorText v-if="errors.note" :message="errors.note[0]" />
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <div class="flex items-center justify-center w-full">
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span></p>
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF (MAX. 10MB)</p>
                                    </div>
                                    <input id="dropzone-file" ref="noteFiles" type="file" @change="handleNoteUpload" class="hidden" accept=".pdf,.jpg,.jpeg,.png" multiple />
                                </label>
                            </div> 
                            <div v-if="payloadNote.files.length" class="my-3 flex">
                                <div class="w-full flex grid grid-cols-4 gap-2">
                                    <span v-for="(file, index) in payloadNote.files" :key="index" class="text-sm bg-gray-300 py-1 px-2 rounded-md bg-opacity-50">{{ file.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleNotes(selectedTicket)" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="addNote()" type="button" class="bg-up-maroon hover:bg-up-maroon-darker text-white px-3 py-2 rounded-md transition duration-200 ease-in-out" :disabled="addingNote">
                                <span v-if="!addingNote">Add</span>
                                <span v-else class="text-center">
                                    <div role="status">
                                        <svg aria-hidden="true" class="w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                        </svg>
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Notes Ticket -->

            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-2/3 py-1">
                    <input v-model="searchParams.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Search" />
                </div>
                <div class="w-full md:w-1/3 py-1 flex grid grid-cols-3 gap-2 pl-2">
                    <button @click="search" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">Search</button>
                    <button @click="filters" type="button" class="bg-gray-300 border border-gray-400 hover:bg-gray-400 text-gray-500 hover:text-gray-200 font-semibold py-2 px-10 rounded-md transition duration-300 ease-in-out">Filters</button>
                    <button @click="toggleCreate" type="button" class="bg-blue-600 border border-blue-700 hover:bg-blue-700 text-white font-semibold py-2 px-10 rounded-md transition duration-300 ease-in-out">Create</button>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Filters section -->
            <div v-if="showFilters" class="w-full border-2 border-gray-300 p-5 rounded-md my-5">
                <h3 class="font-semibold text-up-maroon mb-2">Filter by:</h3>
                <div class="w-full flex grid grid-cols-3 gap-2 text-xs">
                    <!-- Filter by office -->
                    <div class="w-full">
                        <label for="countries" class="block mb-2 font-medium text-gray-900">Section</label>
                        <select v-model="searchParams.office" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select section</option>
                            <option v-for="office in offices" :key="office.id" :value="office">{{ office.short_code }}</option>
                        </select>
                    </div>
                    <!-- Filter by type -->
                    <div class="w-full">
                        <label for="countries" class="block mb-2 font-medium text-gray-900">Type</label>
                        <select v-model="searchParams.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select type</option>
                            <option v-for="process in processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                        </select>
                    </div>
                    <!-- Filter by status -->
                    <div class="w-full">
                        <label for="status" class="block mb-2 font-medium text-gray-900">Status</label>
                        <select v-model="searchParams.status" id="status" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                            <option selected value="">Select status</option>
                            <option value="0">Active</option>
                            <option value="2">Resolved</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- End Filters Section -->

            <!-- Ticket Table -->
            <div :class="showFilters ? '' : 'mt-5'">
                <!-- SM -->
                <div class="w-full md:hidden block bg-gray-200 rounded-md px-5 py-3 mb-5">
                    <!-- Table items -->
                    <template v-if="tickets.length">
                        <div v-for="(item, index) in tickets" :key="index" class="w-full flex flex-wrap text-sm mt-2 mb-4">
                            <template v-for="(header, key) in headers" :key="key" class="w-full flex flex-wrap">
                                <div class="w-1/2 bg-up-maroon text-white p-2 border-b border-white">{{ header['text'] }}</div>
                                <div v-if="header['value'] != 'action'" class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold">{{ item[header['value']] }}</div>
                                <div v-else class="w-1/2 bg-white p-2">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                                >
                                                    Select
                                                    <svg
                                                        class="ml-2 -mr-0.5 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>
            
                                        <template #content>
                                            <Link :href="route('tickets.show', item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-yellow-600">View</Link>
                                            <button @click="toggleNotes(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5">Notes</button>
                                            <button v-if="item.status != 2" @click="resolveTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-green-700">Resolved</button>
                                            <button v-if="!item.deleted_at && item.status == 0" @click="removeTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-red-800">Archive</button>
                                        </template>
                                    </Dropdown>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 pl-3 pr-7 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-6 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="tickets.length">
                            <div v-for="(item, index) in tickets" :key="index" class="w-full bg-white rounded-md px-5 py-3 shadow my-3">
                                <div class="w-full flex grid grid-cols-6 gap-2">
                                    <template v-for="(header, key) in headers" :key="key">
                                        <span v-if="header['value'] != 'action'" >{{ item[header['value']] }}</span>
                                        <div v-else class="w-full flex flex-wrap">
                                            <Dropdown align="left" width="48">
                                                <template #trigger>
                                                    <span class="inline-flex rounded-md">
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-transparent hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                                        >
                                                            Select
                                                            <svg
                                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 20 20"
                                                                fill="currentColor"
                                                            >
                                                                <path
                                                                    fill-rule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </template>
                    
                                                <template #content>
                                                    <Link :href="route('tickets.show', item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-yellow-600">View</Link>
                                                    <button @click="toggleNotes(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5">Notes</button>
                                                    <button v-if="item.status != 2" @click="resolveTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-green-700">Resolved</button>
                                                    <button v-if="!item.deleted_at && item.status == 0" @click="removeTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-red-800">Archive</button>
                                                </template>
                                            </Dropdown>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full bg-gray-100 rounded-md px-5 py-3 shadow my-3">
                                <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                            </div>
                        </template>
                    </div>
                    <!-- End of Table items -->
                </div>
                <!-- Pagination -->
                <div class="w-full">
                    <Pagination :options="pageOptions" @fetch="fetchTickets" />
                </div>
            </div>
            <!-- End of Ticket Table -->

        </div>
    </AuthenticatedLayout>
</template>
