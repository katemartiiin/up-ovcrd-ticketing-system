<script>
/* Vue */
import { ref, watch, reactive, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedVcd.vue'
/* Components */
import UpModal from '@/Components/UpModal.vue'
import Pagination from '@/Components/Pagination.vue'
export default {
    components: {
        Link,
        Head,
        UpModal,
        Pagination,
        AuthenticatedLayout
    },
    props: ['office', 'offices', 'staffs'],
    setup() {
        const { errors, vcdNote, vcdFetch, vcdAssign, vcdFetchNotes } = useTickets()

        const headers = ref([
            { text: 'Ticket #', value: 'control_number' },
            { text: 'Section', value: 'officeCode' },
            { text: 'Type', value: 'processName' },
            { text: 'Status', value: 'statusLabel' },
            { text: 'Staff', value: 'isClaimed' },
            { text: 'Date', value: 'createdDate'},
            { text: 'Action', value: 'action'}
        ])

        const searchParams = reactive({
            keyword: '',
            process_id: '',
            trashed: false
        })

        const tickets = ref([])
        const processes = ref([])

        const selectedTicket = ref('')
        const showNotes = ref(false)
        const noteFiles = ref(null)
        const notes = ref([])

        const initialNote = {
            note: '',
            files: []
        }

        const payloadNote = reactive({...initialNote})

        const showAssign = ref(false)
        const initialAssign = {
            staff_id: '',
            ticket_id: ''
        }

        const payloadAssign = reactive({...initialAssign})

        const initialOptions = {
            to: 0,
            from: 0,
            page: 1,
            total: 0,
            current_page: 1,
            rowsPerPage: 10,
        }

        const pageOptions = reactive({...initialOptions})

        const initialPayload = {
            ticket_id: '',
            staff_id: ''
        }

        const payload = reactive({...initialPayload})

        const handleNoteUpload = async (event) => {
            payloadNote.files = noteFiles.value.files
        }

        const toggleAssign = (ticketId) => {
            showAssign.value = !showAssign.value
            payloadAssign.ticket_id = ticketId
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

        const fetchTickets = async () => {
            await vcdFetch(searchParams, pageOptions, tickets)
        }

        const search = () => {
            fetchTickets()
        }
        
        const assign = async () => {
            await vcdAssign(payloadAssign, initialAssign)
        }

        const toggleTrashed = (isTrashed) => {
            Object.assign(pageOptions, initialOptions)
            searchParams.trashed = isTrashed
            fetchTickets()
        }

        const getTicketNotes = async () => {
            await vcdFetchNotes(selectedTicket.value.id, notes)
        }

        const addNote = async () => {
            await vcdNote(payloadNote, initialNote, selectedTicket.value.id)
            getTicketNotes()
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
            assign,
            search,
            errors,
            addNote,
            payload,
            tickets,
            headers,
            processes,
            showNotes,
            noteFiles,
            showAssign,
            toggleNotes,
            payloadNote,
            pageOptions,
            toggleAssign,
            fetchTickets,
            searchParams,
            payloadAssign,
            toggleTrashed,
            selectedTicket,
            getTicketNotes,
            handleNoteUpload
        }
    }
}
</script>

<template>
    <Head title="Tickets" />
    
    <AuthenticatedLayout>
        <template #header>
            <h1 class="font-bold text-3xl text-up-maroon leading-tight uppercase">Tickets</h1>
            <p class="text-gray-700 my-2 font-semibold text-lg">{{ pageOptions.total }} {{ searchParams.trashed ? 'Archived' : 'Active'}}</p>
        </template>

        <div class="py-5 px-5 md:px-10">
            <!-- Assign Ticket -->
            <UpModal :show="showAssign" @close="toggleAssign(0)">
                <div class="w-full flex flex-wrap">
                    <div class="w-full bg-blue-600 text-white py-5 px-7">
                        <h2 class="text-xl">Assign Ticket</h2>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-blue-600 font-semibold block mb-2">To</label>
                            <select v-model="payloadAssign.staff_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option disabled selected value="">Select staff</option>
                               <template v-for="staff in staffs" :key="staff.id">
                                    <option :value="staff.id">{{ staff.name }}</option>
                               </template>
                            </select>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleAssign(0)" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="assign" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Assign</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Assign Ticket -->
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
                            <button @click="toggleTrashed(false)" type="button" class="text-white py-1 rounded-md z-10" :class="searchParams.trashed ? 'bg-gray-500 hover:bg-gray-400' : 'bg-up-maroon'">ACTIVE</button>
                            <button @click="toggleTrashed(true)" type="button" class="text-white py-1 rounded-r-md mx--1" :class="searchParams.trashed ? 'bg-up-maroon' : 'bg-gray-500 hover:bg-gray-400'">ARCHIVED</button>
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
                                <div v-else class="w-1/2 bg-white text-up-maroon p-2 border-b border-up-maroon font-semibold flex grid grid-cols-4 gap-1">
                                    <Link :href="route('vcd.tickets.show', item.id)" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 text-center text-sm my-auto rounded-sm">
                                        <font-awesome-icon icon="fa-solid fa-eye" />
                                    </Link>
                                    <button @click="toggleNotes(item)" type="button" class="bg-up-maroon hover:bg-up-maroon-lighter rounded-sm text-sm text-white py-1 text-center">
                                        <font-awesome-icon icon="fa-solid fa-note-sticky" />
                                    </button>
                                    <button @click="toggleAssign(item.id)" v-if="!item.staff_id" type="button" class="bg-up-green hover:bg-up-green-darker rounded-sm text-sm text-white py-1 text-center">
                                        <font-awesome-icon icon="fa-solid fa-user-plus" />
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template v-else>
                        <p class="text-center text-sm tracking-widest text-gray-600">No item/s found.</p>
                    </template>
                    <!-- End of Table items -->
                </div>
                <!-- MD -->
                <div class="md:block hidden">
                    <!-- Table header -->
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto mb-3">
                        <div class="w-full bg-white rounded-md px-5 py-2 text-up-maroon font-bold border-2 border-gray-300">
                            <div class="w-full flex grid grid-cols-7 gap-2">
                                <span v-for="(header, index) in headers" :key="index">{{ header['text'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-md py-2 px-3 overflow-x-auto min-tbl-h mb-5">
                        <!-- Table items -->
                        <template v-if="tickets.length">
                            <div v-for="(item, index) in tickets" :key="index">
                                <div class="w-full bg-white rounded-md px-5 py-3 shadow my-3" :class="item.ticketDue >= 3 ? 'border-2 border-up-maroon' : ''">
                                    <div class="w-full flex grid grid-cols-7 gap-2">
                                        <template v-for="(header, key) in headers" :key="key">
                                            <span v-if="header['value'] != 'action'">{{ item[header['value']] }}</span>
                                            <span v-else class="flex grid grid-cols-4 gap-1">
                                                <Link :href="route('vcd.tickets.show', item.id)" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 text-center text-sm my-auto rounded-sm">
                                                    <font-awesome-icon icon="fa-solid fa-eye" />
                                                </Link>
                                                <button @click="toggleNotes(item)" type="button" class="bg-up-maroon hover:bg-up-maroon-lighter rounded-sm text-sm text-white py-1 text-center">
                                                    <font-awesome-icon icon="fa-solid fa-note-sticky" />
                                                </button>
                                                <button @click="toggleAssign(item.id)" v-if="!item.staff_id" type="button" class="bg-up-green hover:bg-up-green-darker rounded-sm text-sm text-white py-1 text-center">
                                                    <font-awesome-icon icon="fa-solid fa-user-plus" />
                                                </button>
                                            </span>
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
