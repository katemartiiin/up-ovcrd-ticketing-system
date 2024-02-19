<script>
/* Vue */
import { ref, reactive, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedStaff.vue'
/* Components */
import Breadcrumb from '@/Components/Breadcrumb.vue'
import Timeline from '@/Components/Timeline.vue'
import UpModal from '@/Components/UpModal.vue'
export default {
    components: {
        Head,
        UpModal,
        Timeline,
        Breadcrumb,
        AuthenticatedLayout
    },
    props: [
        'item',
        'logs',
        'offices'
    ],
    setup(props) {
        const ticket = computed(() => props.item)

        const { staffNote, staffFetchNotes, staffCompleted, staffTransfer } = useTickets()

        const showNotes = ref(false)
        const noteFiles = ref(null)
        const notes = ref(props.item.notes)

        const initialPayload = {
            note: '',
            files: []
        }

        const payload = reactive({...initialPayload})

        const showTransfer = ref(false)
        
        const initialTransfer = {
            ticket_id: ticket.value.id,
            office_id: '',
            reason: ''
        }

        const payloadTransfer = reactive({...initialTransfer})

        const getTicketNotes = async () => {
            await staffFetchNotes(props.item.id, notes)
        }

        const toggleNotes = () => {
            showNotes.value = !showNotes.value
            getTicketNotes()
        }

        const toggleTransfer = () => {
            showTransfer.value = !showTransfer.value
        }

        const completedTicket = async (ticketId) => {
            await staffCompleted(ticketId)
        }

        const transferTicket = async () => {
            await staffTransfer(payloadTransfer, initialTransfer)
        }

        const handleNoteUpload = async (event) => {
            payload.files = noteFiles.value.files
        }

        const addNotes = async () => {
            await staffNote(payload, initialPayload, ticket.value.id)
            getTicketNotes()
        }

        return {
            handleNoteUpload,
            completedTicket,
            payloadTransfer,
            toggleTransfer,
            transferTicket,
            showTransfer,
            toggleNotes,
            noteFiles,
            showNotes,
            addNotes,
            payload,
            notes
        }
    }
}
</script>
<template>
    <Head title="Tickets" />
    
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb parentTitle="Tickets" :parentLink="route('staff.tickets.index')" :currentTitle="'View Ticket #' + item.control_number" />
        </template>

        <div class="py-4 px-5 md:px-10">

            <!-- Transfer Ticket -->
            <UpModal :show="showTransfer" @close="toggleTransfer()">
                <div class="w-full flex flex-wrap">
                    <div class="w-full bg-blue-600 text-white py-5 px-7">
                        <h2 class="text-xl">Transfer Ticket</h2>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-blue-600 font-semibold block mb-2">To</label>
                            <select v-model="payloadTransfer.office_id" id="countries" class="bg-gray-50 border border-gray-300 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option disabled selected value="">Select section</option>
                                <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.name }}</option>
                            </select>
                        </div>
                        <div class="w-full flex flex-wrap mt-2 mb-5">
                            <label class="text-blue-600 font-semibold block mb-2">Reason/s</label>
                            <textarea v-model="payloadTransfer.reason" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter transfer reason" rows="5"></textarea>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleTransfer()" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="transferTicket" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Transfer</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Transfer Ticket -->

            <!-- Notes Ticket -->
            <UpModal :show="showNotes" @close="toggleNotes()">
                <div class="w-full flex flex-wrap">
                    <div class="w-full flex bg-up-maroon text-white py-5 px-7">
                        <h2 class="text-xl">Ticket Notes</h2>
                        <button @click="toggleNotes()" type="button" class="absolute right-7 text-lg">x</button>
                    </div>
                    <div class="w-full py-5 px-7">
                        <div class="w-full bg-gray-100 h-48 rounded-md shadow-md flex flex-wrap mt-2 mb-5 p-5 overflow-y-scroll">
                            <template v-if="item.notes.length">
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
                            <textarea v-model="payload.note" class="w-full bg-gray-50 rounded-md border border-gray-300 text-sm" placeholder="Enter ticket note" rows="3"></textarea>
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
                            <div v-if="payload.files.length" class="my-3 flex">
                                <div class="w-full flex grid grid-cols-4 gap-2">
                                    <span v-for="(file, index) in payload.files" :key="index" class="text-sm bg-gray-300 py-1 px-2 rounded-md bg-opacity-50">{{ file.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex items-end justify-end my-2">
                            <button @click="toggleNotes()" type="button" class="bg-gray-300 hover:bg-gray-400 text-white px-3 py-2 rounded-md mr-2 transition duration-200 ease-in-out">Cancel</button>
                            <button @click="addNotes()" type="button" class="bg-up-maroon hover:bg-up-maroon-darker text-white px-3 py-2 rounded-md transition duration-200 ease-in-out">Add</button>
                        </div>
                    </div>
                </div>
            </UpModal>
            <!-- End Notes Ticket -->

            <!-- Page header -->
            <div class="w-full flex flex-wrap">
                <div class="w-full md:w-2/3 my-2">
                    <h1 class="font-bold text-up-maroon text-3xl uppercase">{{ item.title }}</h1>
                </div>
                <div class="w-full md:w-1/3 my-2">
                    <div class="w-full flex grid grid-cols-3 gap-3">
                        <button @click="toggleNotes" type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 rounded-md">Notes</button>
                        <button v-if="item.status == 0" @click="completedTicket(item.id)" type="button" class="bg-up-green hover:bg-up-green-darker text-white py-1 rounded-md">Completed</button>
                        <button v-if="item.status == 0" @click="toggleTransfer()" type="button" class="bg-blue-600 hover:bg-blue-500 text-white py-1 rounded-md">Transfer</button>
                    </div>
                </div>
            </div>
            <!-- End Page header -->

            <!-- Page body -->
            <div class="w-full flex flex-wrap my-5">

                <!-- Ticket Information -->
                <div class="w-full md:w-3/4 my-2">
                    <div class="bg-white py-5 px-10 md:mr-1.5 rounded-md">
                        <div class="w-full flex flex-wrap mt-3 mb-7 text-lg">
                            <div class="w-full md:w-1/2">
                                <label class="block text-up-green font-semibold mb-2">Section</label>
                                <p class="pl-3">{{ item.office.name }}</p>
                            </div>
                            <div class="w-full md:w-1/2">
                                <label class="block text-up-green font-semibold mb-2">Type</label>
                                <p class="pl-3">{{ item.process.name }}</p>
                            </div>
                        </div>
                        <div class="w-full mt-3 mb-7 text-lg">
                            <label class="block text-up-green font-semibold mb-2">Description</label>
                            <p class="pl-3">{{ item.description }}</p>
                        </div>
                        <div class="w-full flex flex-wrap mt-3 mb-7 text-lg">
                            <div class="w-full md:w-1/2">
                                <label class="block text-up-green font-semibold mb-2">Status</label>
                                <p class="pl-3">{{ item.statusLabel }}</p>
                            </div>
                            <div v-if="item.research_id" class="w-full md:w-1/2">
                                <label class="block text-up-green font-semibold mb-2">Research ID</label>
                                <p class="pl-3">{{ item.researchCode }}</p>
                            </div>
                        </div>
                        <div class="w-full mt-3 mb-7 text-lg">
                            <label class="block text-up-green font-semibold mb-2">Uploaded File/s ({{ item.files.length }})</label>
                            <template v-for="file in item.files" :key="file.id">
                                <div class="w-full flex flex-wrap py-2">
                                    <span class="text-up-yellow"><font-awesome-icon icon="fa-solid fa-file" /></span>
                                    <a :href="file.fileUrl" class="ml-2 text-sm my-auto hover:font-semibold hover:underline hover:cursor-pointer transition duration-300 ease-in-out" download>{{ file.name }}</a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <!-- End Ticket Information -->

                <!-- Ticket Timeline -->
                <div class="w-full md:w-1/4 my-2">
                    <div class="bg-white p-5 md:ml-1.5 rounded-md">
                        <div class="w-full mt-3">
                            <label class="block text-up-green text-xl font-bold mb-2">Timeline</label>                         
                            <Timeline :items="logs" />
                        </div>
                    </div>
                </div>
                <!-- End Ticket Timeline -->

            </div>
            <!-- End Page body -->

        </div>
    </AuthenticatedLayout>

</template>