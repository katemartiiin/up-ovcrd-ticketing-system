import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

import Toast from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";

/* Date Picker */
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
/* import specific icons */
import { 
        faAngleLeft, faAngleRight, 
        faBars, faBell, faBriefcase,
        faChartBar, faCheckCircle,
        faDownload,
        faEye,
        faFile,
        faLock, faLockOpen,
        faMagnifyingGlass, faMinus,
        faNoteSticky,
        faPencil,
        faRecycle,
        faSignOut, 
        faTachometer, faTag, faTicket, faTrash,
        faUsers, faUserPlus,
        faWindowRestore,  
    } from '@fortawesome/free-solid-svg-icons'

/* add icons to the library */
library.add(
        faAngleLeft, faAngleRight,
        faBars, faBell, faBriefcase, 
        faChartBar,faCheckCircle,
        faDownload,
        faEye,
        faFile,
        faLock, faLockOpen,
        faMagnifyingGlass, faMinus,
        faNoteSticky,
        faPencil,
        faRecycle,
        faSignOut, 
        faTachometer, faTag, faTicket, faTrash,
        faUsers, faUserPlus, faWindowRestore 
    )

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const options = {
    // You can set your default options here
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Toast, options)
            .use(ZiggyVue, Ziggy)
            .component('VueDatePicker', VueDatePicker)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
