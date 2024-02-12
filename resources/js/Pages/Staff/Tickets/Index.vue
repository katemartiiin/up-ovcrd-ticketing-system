<script>
/* Vue */
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedStaff.vue'
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
    props: ['office', 'offices'],
    setup() {
        const { errors, staffFetch, staffNote, staffClaim, staffCompleted, staffTransfer, staffFetchNotes } = useTickets()

        const headers = ref([
            { text: 'Ticket #', value: 'control_number' },
            { text: 'Type', value: 'processName' },
            { text: 'Status', value: 'statusLabel' },
            { text: 'Staff', value: 'isClaimed' },
            { text: 'Date', value: 'createdDate'},
            { text: 'Action', value: 'action' }
        ])

        const searchParams = reactive({
            keyword: '',
            process_id: '',
            tab: 0
        })

        const tickets = ref([])
        const processes = ref([])

        const selectedTicket = ref('')
        const notes = ref([])

        const showNotes = ref(false)
        const showTransfer = ref(false)

        const initialNote = {
            note: '',
            files: []
        }

        const payloadNote = reactive({...initialNote})
        const noteFiles = ref(null)

        const ticketFiles = ref(null)

        const handleFileUpload = async (event) => {
            payload.files = ticketFiles.value.files
        }

        const handleNoteUpload = async (event) => {
            payloadNote.files = noteFiles.value.files
        }

        const initialTransfer = {
            ticket_id: '',
            office_id: '',
            reason: ''
        }

        const payloadTransfer = reactive({...initialTransfer})
        const selectedTransfer = ref('')

        const initialOptions = {
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        }

        const pageOptions = reactive({...initialOptions})

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

        const toggleTransfer = (ticketId) => {
            selectedTransfer.value = showTransfer ? ticketId : ''
            showTransfer.value = !showTransfer.value
            errors.value = []
        }

        const completedTicket = async (ticketId) => {
            await staffCompleted(ticketId)
            fetchTickets()
        }

        const fetchTickets = async () => {
            await staffFetch(searchParams, pageOptions, tickets)
        }

        const search = () => {
            fetchTickets()
        }

        const getTicketNotes = async () => {
            await staffFetchNotes(selectedTicket.value.id, notes)
        }

        const toggleTabs = (tab) => {
            Object.assign(pageOptions, initialOptions)
            searchParams.tab = tab
            fetchTickets()
        }

        const addNote = async () => {
            await staffNote(payloadNote, initialNote, selectedTicket.value.id)
            getTicketNotes()
        }

        const transferTicket = async () => {
            payloadTransfer.ticket_id = selectedTransfer.value
            await staffTransfer(payloadTransfer, initialTransfer)
            fetchTickets()
        }

        const claimTicket = async (ticketId) => {
            await staffClaim(ticketId)
            fetchTickets()
        }

        watch(
            () => searchParams.process_id,
            (value) => {
                fetchTickets()
            }
        )

        onMounted(fetchTickets)

        return {
            notes,
            errors,
            search,
            headers,
            addNote,
            tickets,
            noteFiles,
            processes,
            showNotes,
            toggleTabs,
            toggleNotes,
            claimTicket,
            payloadNote,
            ticketFiles,
            pageOptions,
            showTransfer,
            searchParams,
            fetchTickets,
            getTicketNotes,
            toggleTransfer,
            transferTicket,
            selectedTicket,
            payloadTransfer,
            completedTicket,
            selectedTransfer,
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
            <p class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} {{ searchParams.tab == 2 ? 'Archived' : 'Active'}}</p>
        </template>

        <div class="py-5 px-5 md:px-10">

            <!-- Transfer Ticket -->
            <UpModal :show="showTransfer" @close="toggleTransfer(selectedTransfer)">
                <div class="w-full flex flex-wrap">
                    <div class="w-full bg-blue-600 text-white py-5 px-7">
                        <h2 class="text-xl">Transfer Ticket</h2>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-blue-600 font-semibold block mb-2">To</label>
                            <select v-model="payloadTransfer.office_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option disabled selected value="">Select section</option>
                               <template v-for="office in offices" :key="office.id">
                                    <option v-if="office.id != 1" :value="office.id">{{ office.name }}</option>
                               </template>
                            </select>
                            <ErrorText v-if="errors.office_id" :message="errors.office_id[0]" />
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-blue-600 font-semibold block mb-2">Reason/s</label>
                            <textarea v-model="payloadTransfer.reason" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter transfer reason" rows="5"></textarea>
                            <ErrorText v-if="errors.reason" :message="errors.reason[0]" />
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleTransfer(selectedTransfer)" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="transferTicket" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Transfer</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Transfer Ticket -->

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
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF (MAX. 2000MB)</p>
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
                            <button @click="addNote()" type="button" class="bg-up-maroon hover:bg-up-maroon-darker text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Add</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Notes Ticket -->

            <!-- Search bar -->
            <div class="w-full flex flex-wrap text-sm">
                <div class="w-full md:w-3/4 py-1">
                    <input v-model="searchParams.keyword" type="text" class="w-full border-gray-300 rounded-md mr-2 text-sm" placeholder="Ticket #" />
                </div>
                <div class="w-full md:w-1/4 py-1 flex grid grid-cols-2 gap-2 md:pl-2">
                    <button @click="search" type="button" class="border border-up-green bg-up-green-lighter hover:bg-up-green hover:text-white transition duration-300 ease-in-out text-up-green font-semibold py-2 px-5 rounded-md">Search</button>
                    <select v-model="searchParams.process_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                        <option selected value="">Select type</option>
                        <option v-for="process in office.processes" :key="process.id" :value="process.id">{{ process.name }}</option>
                    </select>
                </div>
            </div>
            <!-- End of Search bar -->

            <!-- Ticket Table -->
            <div class="mt-5">
                <!-- Tabs -->
                <div class="w-full flex bg-gray-200 rounded-md mb-5">
                    <div class="w-full bg-gray-200 rounded-md">
                        <div class="w-full flex grid grid-cols-2 md:grid-cols-6 gap-0 text-center">
                            <button @click="toggleTabs(0)" type="button" class="text-white py-1 md:rounded-md" :class="searchParams.tab == 0 ? 'bg-up-maroon z-10' : 'bg-gray-500 hover:bg-gray-400'">YOUR TICKETS</button>
                            <button @click="toggleTabs(1)" type="button" class="text-white py-1 md:rounded-md md:mx--1" :class="searchParams.tab == 1 ? 'bg-up-maroon z-10' : 'bg-gray-500 hover:bg-gray-400'">SECTION</button>
                            <button @click="toggleTabs(2)" type="button" class="text-white py-1 md:rounded-md md:mx--2" :class="searchParams.tab == 2 ? 'bg-up-maroon z-10' : 'bg-gray-500 hover:bg-gray-400'">ARCHIVED</button>
                        </div>
                    </div>
                </div>
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
                                            <Link :href="route('staff.tickets.show', item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-yellow-600">View</Link>
                                            <button v-if="!searchParams.trashed" @click="toggleNotes(item)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5">Notes</button>
                                            <button v-if="!item.staff_id && !searchParams.trashed && item.status == 0" @click="claimTicket(item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-up-maroon">Claim</button>
                                            <button v-if="!searchParams.trashed && item.status == 0" @click="toggleTransfer(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-blue-700">Transfer</button>
                                            <button v-if="!searchParams.trashed && item.status == 0" @click="completedTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-green-700">Completed</button>
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
                            <div class="w-full flex grid grid-cols-6 gap-5">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="tickets.length">
                            <div v-for="(item, index) in tickets" :key="index">
                                <div class="w-full bg-white rounded-md px-5 py-3 shadow my-3" :class="item.ticketDue >= 3 ? 'border-2 border-up-maroon' : ''">
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
                                                        <Link :href="route('staff.tickets.show', item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-yellow-600">View</Link>
                                                        <button v-if="!searchParams.trashed" @click="toggleNotes(item)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5">Notes</button>
                                                        <button v-if="!item.staff_id && !searchParams.trashed && item.status == 0" @click="claimTicket(item.id)" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-up-maroon">Claim</button>
                                                        <button v-if="!searchParams.trashed && item.status == 0" @click="toggleTransfer(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-blue-700">Transfer</button>
                                                        <button v-if="!searchParams.trashed && item.status == 0" @click="completedTicket(item.id)" type="button" class="block w-full text-xs bg-transparent hover:bg-gray-100 py-2 font-medium text-left pl-5 text-green-700">Completed</button>
                                                    </template>
                                                </Dropdown>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-full bg-gray-100 rounded-md px-5 py-3 shadow my-3">
                                <p class="text-center text-sm tracking-widest text-gray-600">No items found.</p>
                            </div>
                        </template>
                        <!-- End of Table items -->
                    </div>
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
