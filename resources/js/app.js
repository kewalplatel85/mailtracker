import './bootstrap';
import './navi.js';
import $ from 'jquery';

// dashboard
$(document).ready(function() {
    let classCounter = 1;

    $('#track_number').on('keypress', function(event) {
        if (event.which === 13) { // Enter key
            event.preventDefault();
            const uniqueClass = 'trn-' + classCounter++;
            const packageLimit = parseInt($('#pcounter').val()) || 0;

            if ($('#tracking_table tbody tr').length < packageLimit) {
                const value = $.trim($('#track_number').val());
                if (value) {
                    $('#tracking_table tbody').attr('data-total', classCounter);
                    $('#tracking_table tbody').append('<tr><td class="p-1 flex justify-between items-center '+ uniqueClass + '"> <span class="text-center flex-1">' + value +
                        '</span><button class="delete-btn p-1 bg-red-500 text-white rounded">Delete</button></td></tr>');
                    $('#track_number').val('');
                }
            } else {
                alert('Package limit reached!');
                $('#track_number').val('');
            }
            updateTotalCount();
        }
    });
});

$('#tracking_table').on('click', '.delete-btn', function() {
    $(this).closest('tr').remove();
    updateTotalCount();
});

function updateTotalCount() {
    let totalCount = $('#tracking_table tbody tr').length;
    $('#tracking_table tbody').attr('data-total', totalCount);
}

$(document).ready(function () {
    $('#mailbox').on('input', function () {
        let input = $(this).val().trim();

        $('#clientTable tbody tr').each(function () {
            let mailboxNumber = $(this).find('td:first').text().trim();
            // If input is empty or matches the mailbox number, show row, else hide
            if (input === '' || mailboxNumber === input) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
