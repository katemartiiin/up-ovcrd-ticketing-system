<script>
/* Vue */
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
/* Components */
import NavLink from '@/Components/NavLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
/* Composables */
import { useCurrentTime } from "../composables/useCurrentTime"

export default {
    components: {
        Link,
        NavLink,
        Dropdown,
        DropdownLink,
        ApplicationLogo,
        ResponsiveNavLink
    },
    setup() {
        const showingNavigationDropdown = ref(false)

        const { currentTime } = useCurrentTime()

        return {
            currentTime, 
            showingNavigationDropdown
        }
    }
}
</script>

<template>
    <div>
        <div class="min-h-screen w-full flex flex-wrap">
            <!-- Desktop Navigation-->
            <div class="w-1/6 md:block hidden shadow-lg">
                <div class="bg-white md:py-5 flex justify-center items-center">
                    <Link :href="route('admin.dashboard')">
                        <ApplicationLogo
                            class="block admin-logo-size fill-current text-gray-800"
                        />
                    </Link>
                </div>
                <div class="bg-up-maroon min-h-screen">
                    <div class="my-3">
                        <NavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-tachometer" />
                                </div>
                                <div class="3/4">Dashboard</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.research.index')" :active="route().current('admin.research.index')">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-tag" />
                                </div>
                                <div class="3/4">Research IDs</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.offices.index')" :active="route().current('admin.offices.index') || route().current('admin.offices.create') || route().current('admin.offices.edit')">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-briefcase" />
                                </div>
                                <div class="3/4">Sections</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.users.index')" :active="route().current('admin.users.index') || route().current('admin.users.create') || route().current('admin.users.show')">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-users" />
                                </div>
                                <div class="3/4">Users</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.settings.index')" :active="route().current('admin.settings.index')">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-tools" />
                                </div>
                                <div class="3/4">Settings</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.about')" :active="route().current('admin.about')" method="post" as="button">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-info-circle" />
                                </div>
                                <div class="3/4">About</div>
                            </div>
                        </NavLink>
                        <NavLink :href="route('admin.logout', 'admin')" :active="route().current('admin.logout')" method="post" as="button" class="px-3">
                            <div class="w-full flex">
                                <div class="w-1/4">
                                    <font-awesome-icon icon="fa-solid fa-sign-out" />
                                </div>
                                <div class="3/4 px-3">Log out</div>
                            </div>
                        </NavLink>
                    </div>
                </div>
            </div>
            <div class="md:w-5/6 w-full bg-gray-100">
                <!-- Mobile Navigation -->
                <div class="md:hidden block w-full flex flex-wrap bg-white py-3">
                    <div class="w-1/2">
                        <Link :href="route('admin.dashboard')">
                            <ApplicationLogo
                                class="block ml-3 h-10 fill-current text-gray-800"
                            />
                        </Link>
                    </div>
                    <div class="w-1/2 my-auto flex items-end justify-end">
                        <button class="mr-5 text-lg" @click="showingNavigationDropdown = !showingNavigationDropdown"><font-awesome-icon icon="fa-solid fa-bars"></font-awesome-icon></button>
                    </div>
                </div>
                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden bg-white"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.research.index')" :active="route().current('admin.research.index')">
                            Research IDs
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.offices.index')" :active="route().current('admin.offices.index') || route().current('admin.offices.create') || route().current('admin.offices.edit')">
                            Sections
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.users.index')" :active="route().current('admin.users.index') || route().current('admin.users.create') || route().current('admin.users.show')">
                            Users
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('admin.settings.index')" :active="route().current('admin.settings.index')">
                            Settings
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.notifications.index')"> Notifications </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.logout', 'admin')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
                <div class="w-full my-auto">
                    <div class="py-5 md:flex hidden justify-end items-end md:pr-10 bg-oblation-header">
                        <Link :href="route('admin.notifications.index')" class="mr-3 text-up-maroon-darker hover:text-white m-auto transition duration-200 ease-in-out text-center"><font-awesome-icon icon="fa-solid fa-bell" /></Link>
                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-up-maroon-darker bg-transparent hover:text-white focus:outline-none transition ease-in-out duration-150"
                                        >
                                            <img :src="$page.props.auth.user.avatarUrl != '' ? $page.props.auth.user.avatarUrl : $page.props.auth.user.google_avatar" width="50" class="rounded-full" />

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
                                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                    <DropdownLink :href="route('admin.logout', 'admin')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <div class="mt-3 md:px-10 px-5">
                        <div class="w-full flex flex-wrap">
                            <div class="md:w-1/2 w-full md:mt-0 mt-3">
                                <h1 v-if="route().current('admin.dashboard')" class="text-3xl mt-5">Welcome back, <span class="font-bold text-up-maroon">{{ $page.props.auth.user.name }}</span>!</h1>
                                <!-- Page Heading -->
                                <div v-else class="mt-5">
                                    <slot name="header" />
                                </div>
                            </div>
                            <div class="md:block hidden md:w-1/2 w-full text-right">
                                <p class="text-sm text-up-green font-medium mt-5">{{ currentTime }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
