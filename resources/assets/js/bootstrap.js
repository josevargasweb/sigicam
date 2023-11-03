
window._ = require('lodash');

/**
 * Aca se alojan las aplicaciones a utilizar si no estan disponibles en templates
 */

 try {
    //  window.$ = window.jQuery = require('jquery');
    //  require('bootstrap-sass');  
    //  window.Popper = require('popper.js').default;
    //  var Highcharts = require('highcharts');
    //  require('highcharts/modules/exporting')(Highcharts);
    //  window.datetimepicker = require('pc-bootstrap4-datetimepicker');
    // window.datatables = require( 'datatables' );

} catch (e) {
    console.log(e);
}


/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

 window.axios = require('axios');

 window.axios.defaults.headers.common = {
     'X-CSRF-TOKEN': window.Laravel.csrfToken,
     'X-Requested-With': 'XMLHttpRequest'
 };

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
