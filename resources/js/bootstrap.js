/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
const token = $('meta[name="csrf-token"]').attr('content');
console.log("token",token);
const channelName = 'location'
function getCookieValue(name) 
    {
      const regex = new RegExp(`(^| )${name}=([^;]+)`)
      const match = document.cookie.match(regex)
      if (match) {
        return match[2]
      }
   }
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
    encrypted:import.meta.env.VITE_PUSHER_ENCRYPTED ?? false,
    forceTLS: import.meta.env.VITE_PUSHER_FORCETLS ?? false,
    disableStatus: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    },
    enabledTransports: ['ws', 'wss'],
});

import tinymce from 'tinymce/tinymce';
import 'tinymce/themes/silver/theme';

// Load specific plugins and toolbar items as needed
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';

tinymce.init({
  selector: 'textarea', // Use the editor for all textareas or specify a CSS selector
  plugins: 'link image',
  toolbar: 'undo redo | bold italic | link image',
  menubar: false, // Hide the menu bar
});
