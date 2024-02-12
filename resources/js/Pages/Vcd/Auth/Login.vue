<script setup>
/* Vue */
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
/* Layout */
import GuestLayout from '@/Layouts/GuestLayout.vue'
/* Components */
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const isExternalEmail = ref(false)

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {

    if (form.email.indexOf('upd.edu.ph') > -1) {
        isExternalEmail.value = false

        form.post(route('vcd.login.store'), {
            onFinish: () => form.reset('password'),
        })
    } else {
        isExternalEmail.value = true
    }
    
};
</script>

<template>
    <GuestLayout>
        <Head title="VcD" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <h1 class="text-center text-3xl font-bold text-up-green mb-5">Ticketing System</h1>

        <form @submit.prevent="submit" class="bg-white">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError v-if="isExternalEmail" class="mt-2" message="Please use your UP email for signing in." />
                
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-6 mb-3">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <Link
                v-if="canResetPassword"
                :href="route('vcd.password.request')"
                class="underline font-medium text-sm text-up-green hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Forgot your password?
            </Link>

            <!-- <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div> -->

            <div class="flex items-center justify-end mt-5">
                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
