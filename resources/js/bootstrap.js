import jQuery from "jquery";
window.$ = window.jQuery =jQuery;
import { popper } from "@popperjs/core";
window.popper = popper;
import moment from "moment";
window.moment = moment;
// // Import Axios
import axios from 'axios';
window.axios = axios;

// Import toastr
import toastr from "toastr";
window.toastr = toastr;

import { Loader } from '@googlemaps/js-api-loader';


// Import jQuery Validation and its additional methods
import "jquery-validation";
import "jquery-validation/dist/additional-methods";

// Import AdminLTE
import "admin-lte";
import Notification from "./Notification";

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


const loader = new Loader({
    apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
    version: "weekly",
    libraries: ["places"]
});
export default loader;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

if(typeof window.AUTH_ID != 'undefined'){
  const notification = new Notification();
  window.Echo.private('App.Models.User.' + window.AUTH_ID)
 .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function ({ resource }) {
      notification.handleNotification(resource);
  });
}