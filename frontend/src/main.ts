import { createApp } from 'vue'
import './style.css'
import './assets/index.css'
import App from './App.vue'

import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: 80,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: [ 'ws', 'wss' ],
})

createApp(App).mount('#app')
