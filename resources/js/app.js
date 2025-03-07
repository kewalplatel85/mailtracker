import './bootstrap';
import './navi.js';
import $ from 'jquery';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// dashboard
$(document).ready(function() {
    let classCounter = 1;

    $('#track_number').on('keypress', function(event) {
        let status = $('.package_stat').attr('data-stat');

        if (event.which === 13) { // Enter key pressed
            event.preventDefault();

            const value = $.trim($('#track_number').val());
            if (!value) return; // Exit if no value

            // Function to check if tracking number exists
            function trackingNumberExists(value, callback) {
                $.ajax({
                    url: '/check-tracking',
                    method: 'POST',
                    data: { tracking_number: value },
                    success: function(response) {
                        // console.log('AJAX Success:', response);
                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.error('AJAX Error:', status, error);
                        alert('Error checking tracking number');
                        if (typeof callback === 'function') {
                            callback({ exists: false });
                        }
                    }
                });
            }

            // Function to add tracking row to the table
            function addTrackingRow(value) {
                const uniqueClass = 'trn-' + classCounter++;
                $('#tracking_table tbody').append(`
                    <tr>
                        <td class="${uniqueClass} p-1 ml-25 flex justify-between items-center">
                            <span class="text-center flex-1">${value}</span>
                            <button class="print-btn bg-blue-500 text-white px-2 py-1 mr-2 rounded hover:bg-blue-600" data-lbl="${value}">Print</button>
                            <button class="delete-btn p-1 bg-red-500 text-white rounded">Delete</button>
                        </td>
                    </tr>
                `);
                $('#track_number').val('');
                $('#sms').val('You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');
                updateTotalCount();
            }

            // Function to update the total package count
            function updateTotalCount() {
                $('#total_count').text($('#tracking_table tbody tr').length);
            }

            // Function to filter customer table by mailbox number
            function filterCustomerByMailbox(mailboxNumber) {
                $('#mailbox').val(mailboxNumber).trigger('input');
            }
            // Handle 'Outgoing' Status
            if (status === 'Outgoing') {
                trackingNumberExists(value, function(response) {
                    if (response.exists) {
                        if (response.status === 'Incoming') {
                            addTrackingRow(value);
                            $('#sms').val('Thanks for Picking up the package!');
                            filterCustomerByMailbox(response.mailbox_number);
                            let totalCount = $('#tracking_table tbody tr').length
                            $('#pcounter').val(totalCount);
                        } else if (response.status === 'Outgoing') {
                            alert('Package already picked up.');
                        }
                    } else {
                        alert('Tracking number cannot be found.');
                    }
                });
                return;
            }

            // Handle 'Incoming' Status - Full validation required
            if (status === 'Incoming') {
                const packageLimit = parseInt($('#pcounter').val()) || 0;
                const mailbox = parseInt($('#mailbox').val()) || 0;
                const mailboxcount = $('#mailbox').attr('data-mc');

                if (mailbox <= 0) {
                    alert('Add Mailbox First!');
                    $('#track_number').val('');
                    return;
                }

                if (mailboxcount <= 0) {
                    alert('Mailbox does not exist, select a new one.');
                    $('#track_number').val('');
                    return;
                }

                if ($('#tracking_table tbody tr').length >= packageLimit) {
                    alert('Package limit reached!');
                    $('#track_number').val('');
                    return;
                }

                trackingNumberExists(value, function(response) {
                    if (response.exists) {
                        // If status is "Incoming" and already exists, alert user
                        if (response.status === 'Incoming') {
                            alert('Package already added.');
                            $('#track_number').val('');
                            return;
                        }
                    } else {
                        // If tracking number not found, add to tracking table
                        addTrackingRow(value);
                        $('#sms').val('You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');
                    }
                });
            }
        }
    });
});

$('#tracking_table').on('click', '.delete-btn', function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
    updateTotalCount();
});

function updateTotalCount() {
    let totalCount = $('#tracking_table tbody tr').length;
    $('#tracking_table tbody').attr('data-total', totalCount);
}

function updateMailcount(input){
    const mailboxcounter = $('#clientTable tbody tr:visible').length;
    let cust = $('#clientTable tbody tr:visible').find('td:eq(3)').text().trim();
    $('#mailbox').attr('data-mc',mailboxcounter);
    $('#mailbox').attr('data-mb',input);
    $('#customer').val(cust)
}

function updateCustomer(){
    const mailboxcounter = $('#clientTable tbody tr:visible').length;
    let mailbox = $('#clientTable tbody tr:visible').find('td:eq(0)').text().trim();
    $('#mailbox').attr('data-mc',mailboxcounter);
    $('#mailbox').attr('data-mb',mailbox);
    $('#mailbox').val(mailbox);
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
        setTimeout(() => {
            updateMailcount(input);
        },300);
    });

    $('#customer').on('input', function () {
        let input = $(this).val().trim().toLowerCase();

        $('#clientTable tbody tr').each(function () {
            let customer = $(this).find('td:eq(3)').text().trim().toLocaleLowerCase();
            // If input is empty or matches the mailbox number, show row, else hide
            if (input === '' || customer === input) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        setTimeout(() => {
            updateCustomer();
        },300);
    });

});

$(document).ready(function () {
    $('#tracking_table').on('click', '.print-btn', function(e) {
        e.preventDefault();
        let clientTable = $('#clientTable tbody tr:visible');
        let customerName = clientTable.find('td:eq(3)').text().trim();
        let customerPhone = clientTable.find('td:eq(4)').text().trim();
        let trackingNumber = $(this).attr('data-lbl');
        let mailbox = $('#mailbox').attr('data-mb');
        // Create a new window for printing
        let printWindow = window.open('', '', 'width=800,height=1280');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Mail ALL Center</title>
                    <script src="https://cdn.tailwindcss.com"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.0/JsBarcode.all.min.js"></script>
                    <style>
                        @page {
                            size: 4in 6in;
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: 'Inter', sans-serif;
                        }
                    </style>
                </head>
                <body class="h-screen flex items-center justify-center p-4 bg-white">
                    <div class="w-[4in] h-[6in] border border-black rounded-lg shadow-lg p-4 grid grid-rows-5 gap-4">
                        <!-- Header -->
                        <div class="flex justify-between items-center text-lg font-semibold">
                            <span>Mailbox</span>
                            <span>Package ID</span>
                        </div>

                        <!-- Mailbox & Package ID -->
                        <div class="flex justify-between items-center">
                            <h1 class="text-7xl font-bold">${mailbox}</h1>
                            <h1 class="text-7xl font-bold">005</h1>
                        </div>

                        <!-- Barcode -->
                        <div class="flex justify-center">
                            <svg id="barcode" class="w-full"></svg>
                        </div>

                        <!-- Tracking Information -->
                        <div class="mt-5 flex justify-between items-center text-lg">
                            <h2 class="font-medium">Tracking Number:</h2>
                            <h2 class="font-bold">${trackingNumber}</h2>
                        </div>

                        <!-- Customer Information -->
                        <div class="flex justify-between gap-2 text-lg">
                            <div class="flex justify-between">
                                <h5 class="font-medium">Customer: ${customerName}</h5>
                                <h5 class="font-medium">Contact: ${customerPhone}</h5>
                            </div>
                        </div>
                    </div>

                    <script>
                        const script = document.createElement('script');
                        script.src = "https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.0/JsBarcode.all.min.js";
                        script.onload = function() {
                            JsBarcode("#barcode", "${trackingNumber}", {
                                format: "CODE128",
                                displayValue: true,
                                fontSize: 35,
                                margin: 10
                            });
                            setTimeout(() => window.print(), 500);
                        };
                        document.head.appendChild(script);
                    </script>
                </body>
            </html>
        `);

        printWindow.document.close();
    });
});

$(document).ready(function() {
    function getCustomerInfo(mailboxNumber) {
        let customerInfo = {};
        $('#clientTable tbody tr').each(function() {
            let mailbox = $(this).find('td:eq(0)').text().trim();
            if (mailbox == mailboxNumber) {
                customerInfo.name = $(this).find('td:eq(3)').text().trim();
                customerInfo.phone = $(this).find('td:eq(4)').text().trim();
                return false;
            }
        });
        return customerInfo;
    }

    $('#packageForm').submit(function(event) {
        event.preventDefault();
        let formData = $(this).serializeArray();
        // Convert tracking numbers to array
        let mailboxNumber = $('#mailbox').attr('data-mb');
        let package_stat = $('.package_stat').attr('data-stat');
        let num_packages = $('#pcounter').val();
        let customer = getCustomerInfo(mailboxNumber);
        let trackingNumbers = [];
        let sms = $('#sms').val();
        $('#tracking_table tbody tr').each(function() {
            let trackingNumber = $(this).find('span').text().trim();
            trackingNumbers.push(trackingNumber);
        });

        trackingNumbers.forEach(trackingNumber => {
            formData.push({ name: 'tracking_numbers[]', value: trackingNumber });
        });
        formData.push({ name: 'customer_name', value: customer.name, });
        formData.push({ name: 'customer_phone', value: customer.phone, });
        formData.push({ name: 'mailbox', value: mailboxNumber, });
        formData.push({ name: 'package_status', value: package_stat });
        formData.push({ name: 'num_packages', value: num_packages });
        formData.push({ name: 'sms', value: sms });

        if(package_stat ==='Incoming'){
            $.ajax({
                url: '/saveAndNotify',
                type: 'POST',
                data: formData,
                success: function(response) {
                    let message = response.message;
                alert(message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }else{
            $.ajax({
                url: '/outgoing-packge',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating status');
                }
            });
        }

    });
});

$(document).ready(function(){
    function clearTrackingTable() {
        $('#tracking_table tbody').empty();
        updateTotalCount();
    }

    $('.scanStat li').on('click',function(e){
        e.preventDefault();
        let status = $(this).text();
        if(status === 'Outgoing'){
            $('#mailbox').val('');
            $('#customer').val('');
            $('#pcounter').val('');
            $('#mailbox').prop('readonly', true);
            $('#customer').prop('readonly', true);
            $('#pcounter').prop('readonly', true);
            $('#sms').val('Thanks for Picking up the package!');
            $('#track_number').focus();
            clearTrackingTable();
        }else{
            $('#mailbox').val('');
            $('#customer').val('');
            $('#pcounter').val('1');
            $('#mailbox').prop('readonly', false);
            $('#customer').prop('readonly', false);
            $('#pcounter').prop('readonly', false);
            $('#sms').val('You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');
            $('#mailbox').focus();
            clearTrackingTable();
        }
    });
});

// Packagelogs
$(document).ready(function() {
    $('.package_Logstat li').on('click', function(){
        let status =$(this).text().trim();
    //    alert(status);
        $.ajax({
            url: '/get-packages', // Update with your endpoint
            method: 'GET',
            data: { status: status },
            success: function (response) {
                // Call function to render the updated table
                updatePackageTable(response);
            },
            error: function () {
                alert('Error fetching package logs.');
            }
        });
    });

    function updatePackageTable(packages) {
        let tableBody = $('#packageLogs tbody');
        tableBody.empty(); // Clear existing rows

        packages.forEach(function (packageGroup) {
            let trackingNumbers = packageGroup.tracking_numbers.join(', <br>');

            let row = `
                <tr>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.mailbox_number}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.customer_name}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.phone_number}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.package_count}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${trackingNumbers}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.status}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold bg-gray-900 text-white sm:pl-0">${packageGroup.date_received}</td>
                </tr>
            `;
            tableBody.append(row); // Append new rows
        });
    }
});

// twilio
$(document).ready(function () {
    // Toggle inbox panel
    $('#toggle-inbox').on('click', function () {
        $('#inbox-panel').toggleClass('hidden opacity-0 translate-y-4');
    });

    // Close inbox panel
    $('#close-inbox').on('click', function () {
        $('#inbox-panel').addClass('hidden opacity-0 translate-y-4');
    });

    // Tab switching logic
    $('.tab-link').on('click', function () {
        const targetTab = $(this).data('tab');

        // Hide all tabs and remove active styling
        $('.tab-content').addClass('hidden');
        $('.tab-link').removeClass('text-blue-600');

        // Show the selected tab and highlight it
        $(`#${targetTab}`).removeClass('hidden');
        $(this).addClass('text-blue-600');
    });

    // Handle reply form submission via AJAX
    $(document).on('submit', '.reply-form', function (e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                alert(response.success || 'Reply sent successfully!');
                form.trigger('reset'); // Clear the form on success
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.error || 'Failed to send reply.');
            }
        });
    });

    // Handle "Create Message" form submission via AJAX
    $('#create form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            success: function (response) {
                alert(response.success || 'Message sent successfully!');
                form.trigger('reset'); // Clear the form on success
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Failed to send message.';
                alert(errorMsg);
            }
        });
    });
});

$(document).ready(function () {

    // Collect data from the table
    const customersData = $('#clientTable tbody tr').map(function () {
        return {
            mailbox: $(this).find('td:eq(0)').text().trim(),
            customer: $(this).find('td:eq(3)').text().trim(),
            phone: $(this).find('td:eq(4)').text().trim()
        };
    }).get();

    // Initialize autocomplete for inputs
    function setupAutocomplete(input, key, phoneInput) {
        $(input).on('input', function () {
            const query = $(this).val().toLowerCase();
            const suggestions = customersData.filter(item =>
                item[key].toLowerCase().includes(query)
            );
            showSuggestions($(this), suggestions, key, phoneInput);
        });
    }

    // Display autocomplete suggestions
    function showSuggestions(input, suggestions, key, phoneInput) {
        let suggestionBox = input.siblings('.autocomplete-suggestions');

        if (suggestionBox.length === 0) {
            suggestionBox = $('<div class="autocomplete-suggestions absolute bg-white shadow-lg rounded-lg mt-1 max-h-48 overflow-y-auto w-1/2"></div>');
            input.after(suggestionBox);
        }

        suggestionBox.empty();

        suggestions.forEach(item => {
            const suggestion = $(`<div class="suggestion-item px-4 py-2 hover:bg-gray-100 cursor-pointer">${item[key]}</div>`);
            suggestion.on('click', function () {
                input.val(item[key]);
                phoneInput.val(item.phone);
                suggestionBox.empty();
                fillOtherFields(item, key, input);
            });
            suggestionBox.append(suggestion);
        });
    }

    // Global storage for phone numbers in the Text Blast tab
    let phoneList = [];

    // Helper function to clean and format phone numbers for Twilio (E.164 standard)
    function formatPhoneNumber(number) {
        // Remove all non-digit characters
        let cleaned = number.replace(/\D/g, '');

        // Check if it's a valid phone number (10-15 digits for international numbers)
        if (cleaned.length >= 10 && cleaned.length <= 15) {
            // If it's a 10-digit number (US), add +1 by default
            if (cleaned.length === 10) {
                return `+1${cleaned}`;
            }
            // Ensure the number starts with + for international formats
            if (!cleaned.startsWith('+')) {
                return `+${cleaned}`;
            }
            return cleaned;
        }
        return null; // Invalid number
    }

    // Autofill customer details and handle phone numbers
    function fillOtherFields(selectedItem, key) {
        // Detect the active tab (Create or Text Blast)
        const isTextBlast = $('#textblast').is(':visible');

        // Identify relevant input fields based on the active tab
        const phoneInput = isTextBlast ? $('#phone_numbers') : $('#phone');
        const customerInput = isTextBlast ? $('#search-customer-blast') : $('#search-customer');
        const mailboxInput = isTextBlast ? $('#search-mailbox-blast') : $('#search-mailbox');

        // Update the customer name and mailbox fields
        if (key === 'mailbox') {
            customerInput.val(selectedItem.customer);
        } else if (key === 'customer') {
            mailboxInput.val(selectedItem.mailbox);
        }

        // Handle phone numbers (apply to both Create and Text Blast tabs)
        const formattedPhone = formatPhoneNumber(selectedItem.phone);

        if (formattedPhone) {
            if (isTextBlast) {
                // Append valid numbers in the Text Blast tab (no duplicates)
                if (!phoneList.includes(formattedPhone)) {
                    phoneList.push(formattedPhone);
                }
                // Update the Text Blast phone input
                phoneInput.val(phoneList.join(', '));
            } else {
                // Set the phone number directly in the Create tab (single number only)
                phoneInput.val(formattedPhone);
            }
        }

        // Debug logs for monitoring
        console.log('Is Text Blast Tab Active?', isTextBlast);
        console.log('Appending Phone Number:', formattedPhone);
        console.log('Updated Phone List:', phoneList);
    }


    // Attach autocomplete to both forms
    setupAutocomplete('#search-mailbox', 'mailbox', $('#phone'));
    setupAutocomplete('#search-customer', 'customer', $('#phone'));

    setupAutocomplete('#search-mailbox-blast', 'mailbox', $('#phone_numbers'));
    setupAutocomplete('#search-customer-blast', 'customer', $('#phone_numbers'));

    // Hide suggestions on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.autocomplete-suggestions, #search-mailbox, #search-customer, #search-mailbox-blast, #search-customer-blast').length) {
            $('.autocomplete-suggestions').empty();
        }
    });

});


