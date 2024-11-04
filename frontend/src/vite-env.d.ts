/// <reference types="vite/client" />

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        Pusher: Pusher;
        Echo: Echo;
    }
}
