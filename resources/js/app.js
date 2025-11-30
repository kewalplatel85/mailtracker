import './bootstrap';
import './navi.js';
import $ from 'jquery';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Make jQuery available globally for legacy compatibility
window.$ = window.jQuery = $;
