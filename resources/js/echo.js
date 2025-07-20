import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// 1gghgbnh1gghgbnh// Check if Pusher environment variables are available
if (!import.meta.env.VITE_PUSHER_APP_KEY) {
    console.error('Pusher app key not found. Please set VITE_PUSHER_APP_KEY in your .env file');
}

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (!csrfToken) {
    console.error('CSRF token not found. Make sure the meta tag is present in your layout.');
}

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    encrypted: true,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    },
    enabledTransports: ['ws', 'wss']
       
});
