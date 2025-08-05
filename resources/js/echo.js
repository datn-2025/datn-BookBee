import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Enable Pusher logging for debugging (chỉ trong development)
if (import.meta.env.DEV) {
    Pusher.logToConsole = true;
}

// Check if Pusher environment variables are available
if (!import.meta.env.VITE_PUSHER_APP_KEY) {
    console.error('Pusher app key not found. Please set VITE_PUSHER_APP_KEY in your .env file');
}

if (!import.meta.env.VITE_PUSHER_APP_CLUSTER) {
    console.error('Pusher cluster not found. Please set VITE_PUSHER_APP_CLUSTER in your .env file');
}

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (!csrfToken) {
    console.error('CSRF token not found. Make sure the meta tag is present in your layout.');
}

// Initialize Echo with error handling
try {
    // Check if required env vars exist
    if (!import.meta.env.VITE_PUSHER_APP_KEY) {
        throw new Error('VITE_PUSHER_APP_KEY is missing');
    }
    if (!import.meta.env.VITE_PUSHER_APP_CLUSTER) {
        throw new Error('VITE_PUSHER_APP_CLUSTER is missing');
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

} catch (error) {
    console.error('❌ Error initializing Echo:', error.message);
    window.Echo = null;
}

// Debug Echo connection - only if Echo was created successfully
if (window.Echo && window.Echo.connector) {
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Pusher connected');
    });

    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('❌ Pusher error:', error);
    });
    
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('� Pusher disconnected');
    });
} else {
    console.error('❌ Echo connector not available');
}