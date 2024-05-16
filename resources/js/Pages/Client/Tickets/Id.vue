<script>
/* Vue */
import { ref, reactive } from 'vue'
import { Head } from '@inertiajs/vue3'
/* Composables */
import useTickets from '@/composables/tickets'
/* Layout */
import AuthenticatedLayout from '@/Layouts/AuthenticatedClient.vue'
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
        'logs'
    ],
    setup(props) {
        const { fetchNotes, followUp, resolve, remove, note } = useTickets()

        const showNotes = ref(false)
        const noteFiles = ref(null)
        const notes = ref(props.item.notes)

        const initialPayload = {
            note: '',
            files: []
        }

        const payload = reactive({...initialPayload})

        const addingNote = ref(false)

        const getTicketNotes = async () => {
            await fetchNotes(props.item.id, notes)
        }

        const toggleNotes = () => {
            showNotes.value = !showNotes.value
            getTicketNotes()
        }

        const sendFollowUp = async (ticketId) => {
            await followUp(ticketId)
        }

        const resolveTicket = async (ticketId) => {
            await resolve(ticketId)
            location.reload()
        }

        const removeTicket = async (ticketId) => {
            await remove(ticketId)
        }

        const handleNoteUpload = async (event) => {
            payload.files = noteFiles.value.files
        }

        const addNotes = async (itemId) => {
            addingNote.value = true
            await note(payload, initialPayload, itemId)
            getTicketNotes()
            addingNote.value = false
        }

        return {
            handleNoteUpload,
            getTicketNotes,
            resolveTicket,
            sendFollowUp,
            removeTicket,
            toggleNotes,
            addingNote,
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
            <Breadcrumb parentTitle="Tickets" :parentLink="route('tickets.index')" :currentTitle="'View Ticket #' + item.control_number" />
        </template>

        <div class="py-4 px-5 md:px-10">
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
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF (MAX. 10MB)</p>
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
                            <button @click="addNotes(item.id)" type="button" class="bg-up-maroon hover:bg-up-maroon-darker text-white px-3 py-2 rounded-md transition duration-200 ease-in-out" :disabled="addingNote">
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

            <!-- Page header -->
            <div class="w-full flex flex-wrap">
                <div class="w-full md:w-2/3 my-2">
                    <h1 class="font-bold text-up-maroon text-3xl uppercase">{{ item.title }}</h1>
                </div>
                <div class="w-full md:w-1/3 my-2">
                    <div class="w-full flex grid grid-cols-3 gap-3">
                        <button @click="toggleNotes" type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 rounded-md">Notes</button>
                        <button v-if="item.status == 0" @click="resolveTicket(item.id)" type="button" class="bg-up-green hover:bg-up-green-darker text-white py-1 rounded-md">Resolve</button>
                        <button v-if="item.deleted_at != '' && item.status == 0" @click="removeTicket(item.id)" type="button" class="bg-up-maroon hover:bg-up-maroon-darker text-white py-1 rounded-md">Archive</button>
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
                            <label class="block text-up-green font-semibold mb-2">Description</label>
                            <p class="pl-3">{{ item.description }}</p>
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
                        <div class="w-full my-7 flex items-end justify-end">
                            <button @click="sendFollowUp(item.id)" type="button" class="block text-up-maroon underline font-semibold mb-2">Send alert to follow up this ticket</button>
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