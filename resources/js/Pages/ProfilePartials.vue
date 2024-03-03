<script>
/* Vue */
import { Head } from '@inertiajs/vue3'
import { ref, reactive, computed } from 'vue'
/* Composables */
import useUsers from '@/composables/users'
/* Components */
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
export default {
    components: {
        Head,
        TextInput,
        InputError,
        InputLabel
    },
    props: ['user'],
    setup (props) {
        const { errors, updateProfile } = useUsers()

        const userData = computed(() => props.user);

        const form = reactive({
            first_name: userData.value.first_name,
            last_name: userData.value.last_name,
            avatar: userData.value.avatarUrl != '' ? userData.value.avatarUrl : userData.value.google_avatar,
            new_avatar: '',
            errors: errors.value
        })

        const avatar = ref(null)

        const handleAvatarUpload = async (event) => {
            form.new_avatar = avatar.value.files
        }

        const saveProfile = async () => {
            await updateProfile(form)
        }

        return {
            form,
            avatar,
            userData,
            saveProfile,
            handleAvatarUpload
        }
    }
}
</script>
<template>
    <div>
        <div class="py-5 px-5 md:px-10">
            
            <div class="max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

                            <p class="mt-1 text-sm text-gray-600">
                                Update your account's profile information.
                            </p>
                        </header>

                        <div class="mt-6 space-y-6">
                            <div class="w-full flex items items-center justify-center">
                                <img :src="form.avatar" class="w-28 rounded-full border-4 border-gray-300" />
                                <input type="file" ref="avatar" @change="handleAvatarUpload" class="mx-5 text-sm" accept=".jpg, .jpeg, .png" />
                            </div>
                            <div class="w-full flex grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <InputLabel for="first_name" value="First Name" />

                                    <TextInput
                                        id="first_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.first_name"
                                        required
                                        autofocus
                                        autocomplete="first_name"
                                    />

                                    <InputError class="mt-2" :message="form.errors.first_name" />
                                </div>

                                <div>
                                    <InputLabel for="last_name" value="Last Name" />

                                    <TextInput
                                        id="last_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.last_name"
                                        required
                                        autofocus
                                        autocomplete="last_name"
                                    />

                                    <InputError class="mt-2" :message="form.errors.last_name" />
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="button" @click="saveProfile" class="inline-flex items-center px-4 py-2 bg-up-green border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Save
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
