import './bootstrap';
import './navi.js';
import $ from 'jquery';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// dashboard
    // adding tracking number
$(document).ready(function() {
    let classCounter = 1;

    $('#track_number').on('keypress', function(event) {
        let status = $('.package_stat').attr('data-stat');
        let custTab = $('#custTab1').val(); // Detect selected tab

        if (event.which === 13) { // Enter key pressed
            event.preventDefault();

            const value = $.trim($('#track_number').val());
            if (!value) return; // Exit if no value

            let phone = $('#cnumber').val()?.trim() || '';
            let customerName = $('#customer').val().trim();
            let mailbox = $('#mailbox').val().trim();

            let mailboxError = $('#mailbox-error').text();
            let customerError = $('#customer-error').text();

            // ✅ Prevent adding tracking number if mailbox or customer has no match in Current Clients
            if (custTab === 'Current Clients' && (mailboxError || customerError)) {
                alert('No match found for mailbox or customer. Please check the inputs.');
                return;
            }

            // ✅ Ensure phone number & name are provided (ONLY if phone input is visible)
            if ($('#cnumber').is(':visible')) {
                let cleanedPhone = phone.replace(/[\s-]/g, ''); // Remove spaces & dashes
                if (!/^\d{10,14}$/.test(cleanedPhone)) { // Ensure 10-14 digits
                    alert('Invalid phone number. Must be between 10 and 14 digits.');
                    return;
                }

                if (phone === '') {
                    alert('Phone number is required.');
                    return;
                }
            }

            if (customerName === '') {
                alert('Customer name is required.');
                return;
            }

            // ✅ "New Client" can proceed with or without a mailbox
            if (custTab !== 'New Clients' && mailbox === '') {
                alert('Mailbox is required unless selecting "New Client".');
                return;
            }

            // ✅ Check if tracking number already exists (AJAX)
            function trackingNumberExists(value, callback) {
                $.ajax({
                    url: '/check-tracking',
                    method: 'POST',
                    data: {
                        tracking_number: value,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    success: function(response) {
                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error checking tracking number.');
                        if (typeof callback === 'function') {
                            callback({ exists: false });
                        }
                    }
                });
            }

            // ✅ Function to add tracking row
            function addTrackingRow(value) {
                const uniqueClass = 'trn-' + classCounter++;
                $('#tracking_table tbody').append(`
                    <tr>
                        <td class="${uniqueClass} p-1 ml-25 flex justify-between items-center">
                            <span class="text-center flex-1">${value}</span>
                            <button class="print-btn bg-blue-500 text-white px-2 py-1 mr-2 rounded hover:bg-blue-600" data-lbl="${value}">Print</button>
                            <button class="delete-track p-1 bg-red-500 text-white rounded">Delete</button>
                        </td>
                    </tr>
                `);
                $('#track_number').val('');
                $('#sms').val('You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');
                updateTotalCount();
            }

            // ✅ Update total package count
            function updateTotalCount() {
                $('#pcounter').text($('#tracking_table tbody tr').length);
            }

            // ✅ Handle 'Outgoing' Status (No mailbox required)
            if (status === 'Outgoing') {
                if ($('#tracking_table tbody tr td span').filter(function() { return $(this).text().trim() === value; }).length > 0) {
                    alert('Tracking number already added.');
                    $('#track_number').val('');
                    return;
                }

                trackingNumberExists(value, function(response) {
                    if (response.exists) {
                        if (response.status === 'Incoming') {
                            addTrackingRow(value);
                            $('#sms').val('Thanks for Picking up the package!');
                        } else if (response.status === 'Outgoing') {
                            alert('Package already picked up.');
                        }
                    } else {
                        alert('Tracking number cannot be found.');
                    }
                });
                return;
            }

            // ✅ Handle 'Incoming' Status
            if (status === 'Incoming') {
                const packageLimit = parseInt($('#pcounter').val()) || 0;
                const mailboxcount = $('#mailbox').attr('data-mc');

                if (custTab !== 'New Clients' && mailboxcount <= 0) {
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
                        if (response.status === 'Incoming') {
                            alert('Package already added.');
                            $('#track_number').val('');
                            return;
                        }
                    } else {
                        addTrackingRow(value);
                        $('#sms').val('You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');
                    }
                });
            }
        }
    });
});
    // table filter
$(document).ready(function () {
    let timeout;

    function updateTotalCount() {
        let visibleRows = $('#clientTable tbody tr:visible').length;
        $('#mailbox').attr('data-mc', visibleRows); // Update data-mc on mailbox input
    }

    function filterTable(inputField) {
        let mailboxInput = $('#mailbox').val().trim();
        let customerInput = $('#customer').val().trim().toLowerCase();
        let mailboxFilled = mailboxInput !== '';
        let customerFilled = customerInput !== '';
        let custTab = $('#custTab1').val(); // Get selected customer tab

        let matchFound = false;
        let matchedMailbox = '';
        let matchedCustomer = '';

        $('#mailbox-error, #customer-error').text(''); // Clear error messages

        // ✅ If 'custTab1' is selected, **skip filtering** and show all rows
        if (custTab === 'New Clients') {
            $('#clientTable tbody tr').show();
            updateTotalCount();
            return;
        }

        if (!mailboxFilled && !customerFilled) {
            $('#mailbox').val('');
            $('#customer').val('');
            $('#clientTable tbody tr').show();
            updateTotalCount();
            return;
        }

        $('#clientTable tbody tr').each(function () {
            let mailboxNumber = $(this).find('td:eq(0)').text().trim();
            let customer = $(this).find('td:eq(3)').text().trim();

            if (inputField === 'mailbox' && mailboxNumber === mailboxInput) {
                $(this).show();
                if (!matchFound) {
                    matchFound = true;
                    matchedMailbox = mailboxNumber;
                    matchedCustomer = customer;
                }
            } else if (inputField === 'customer' && customer.toLowerCase() === customerInput) {
                $(this).show();
                if (!matchFound) {
                    matchFound = true;
                    matchedMailbox = mailboxNumber;
                    matchedCustomer = customer;
                }
            } else {
                $(this).hide();
            }
        });

        if (matchFound) {
            if (inputField === 'mailbox') {
                $('#customer').val(matchedCustomer);
            }
            if (inputField === 'customer') {
                $('#mailbox').val(matchedMailbox);
            }
            $('#mailbox').attr('data-mb', matchedMailbox);
        } else {
            if (inputField === 'mailbox') {
                $('#mailbox-error').text('No match found');
            }
            if (inputField === 'customer') {
                $('#customer-error').text('No match found');
            }
            $('#clientTable tbody tr').show(); // Show all rows if no match is found
        }

        updateTotalCount();
    }

    function handleInputChange(inputField) {
        clearTimeout(timeout);
        timeout = setTimeout(() => filterTable(inputField), 1000);
    }

    $('#mailbox').on('input', function () {
        if ($(this).val().trim() === '') {
            $('#customer').val('');
            $('#clientTable tbody tr').show();
            $('#mailbox').removeAttr('data-mb');
        }
        handleInputChange('mailbox');
    });

    $('#customer').on('input', function () {
        handleInputChange('customer');
    });

    $('input').on('keypress', function (event) {
        if (event.which === 13) {
            event.preventDefault();
            $('#track_number').focus();
            return false;
        }
    });

    // ✅ Detect changes to custTab and apply behavior accordingly
    $('#custTab').on('change', function () {
        if ($(this).val() === 'custTab1') {
            $('#mailbox, #customer').val('').trigger('input'); // Clear filters
            $('#clientTable tbody tr').show(); // Show all rows
        } else {
            updateTotalCount();
        }
    });

    // Initialize table count on page load
    updateTotalCount();
});
    // Package Label
$(document).ready(function () {
    function getLastPackageID(callback) {
        $.ajax({
            url: '/get-last-package-id', // Make sure this route returns the last package ID
            method: 'GET',
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response.last_id || 0);
                }
            },
            error: function() {
                alert('Error fetching last package ID.');
                if (typeof callback === 'function') {
                    callback(0);
                }
            }
        });
    }

    $('#tracking_table').on('click', '.print-btn', function(e) {
        e.preventDefault();
        let isNewClient = $('#custTab1-dropdown-btn').text().trim() === 'New Clients';
        console.log('isNewClient:', isNewClient);

        let customerName = isNewClient ? $('#customer').val().trim() : $('#clientTable tbody tr:visible td:eq(3)').text().trim();
        let customerPhone = isNewClient ? $('#cnumber').val().trim() : $('#clientTable tbody tr:visible td:eq(4)').text().trim();
        let trackingNumber = $(this).attr('data-lbl');
        let rowPosition = $(this).closest('tr').index() + 1;
        let customLbl = $('#lbl').text().trim().split(',').join('<br>');

        getLastPackageID(function(lastPackageID) {
            let newPackageID = lastPackageID + rowPosition;

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
                        <div class="w-[4in] h-[6in] border border-black rounded-lg shadow-lg p-4 flex flex-col justify-between">
                            <div class="flex justify-between items-center text-lg font-semibold">
                                ${isNewClient ? '' : '<span>Mailbox</span>'}
                                <span>Package ID</span>
                            </div>

                            <div class="flex justify-between items-center">
                                ${isNewClient ? '' : `<h1 class="text-6xl font-bold">${$('#mailbox').attr('data-mb') || ''}</h1>`}
                                <h1 class="text-6xl font-bold">${newPackageID}</h1>
                            </div>

                            <div class="flex justify-center">
                                <svg id="barcode" class="w-full"></svg>
                            </div>

                            <div class="mt-2 text-center">
                                <h2 class="font-medium">Tracking Number:</h2>
                                <h2 class="font-bold">${trackingNumber}</h2>
                            </div>

                            <div class="text-center text-lg">
                                <h5 class="font-medium">${customerName ? `Customer: ${customerName}` : ''}</h5>
                                <h5 class="font-medium">${customerPhone ? `Contact: ${customerPhone}` : ''}</h5>
                            </div>

                            <div class="mt-2 text-center text-sm font-medium border-t pt-2">
                                <p>${customLbl}</p>
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
});
    // save package and send message
$(document).ready(function() {
    function getCustomerInfo(mailboxNumber) {
        let customerInfo = { name: '', phone: '' };

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
        let custTab = $('#custTab1-dropdown-btn').text().trim(); // ✅ Get selected tab
        let package_stat = $('.package_stat').attr('data-stat');
        let num_packages = $('#pcounter').val();
        let sms = $('#sms').val();
        let trackingNumbers = [];

        $('#tracking_table tbody tr').each(function() {
            let trackingNumber = $(this).find('span').text().trim();
            trackingNumbers.push(trackingNumber);
        });

        trackingNumbers.forEach(trackingNumber => {
            formData.push({ name: 'tracking_numbers[]', value: trackingNumber });
        });

        let customerName = '';
        let customerPhone = '';
        let mailboxNumber = '';

        if (custTab === 'New Clients') {
            customerName = $('#customer').val().trim();
            customerPhone = $('#cnumber').val().trim().replace(/\D/g, ''); // Remove non-numeric characters
        } else {
            mailboxNumber = $('#mailbox').attr('data-mb');
            let customer = getCustomerInfo(mailboxNumber);
            customerName = customer.name;
            customerPhone = customer.phone;
        }

        // ✅ Final validation
        if (!customerName || !customerPhone) {
            alert('Valid customer name and phone number are required.');
            return;
        }

        if (custTab !== 'New Clients' && (!mailboxNumber || !customerName || !customerPhone)) {
            alert('Invalid mailbox or customer information. Please check and try again.');
            return;
        }

        formData.push({ name: 'customer_name', value: customerName });
        formData.push({ name: 'customer_phone', value: customerPhone });
        formData.push({ name: 'mailbox', value: mailboxNumber || '' });
        formData.push({ name: 'package_status', value: package_stat });
        formData.push({ name: 'num_packages', value: num_packages });
        formData.push({ name: 'sms', value: sms });

        let ajaxUrl = package_stat === 'Incoming' ? '/saveAndNotify' : '/outgoing-packge';

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
    // delete btn on tracking table
$('#tracking_table').on('click', '.delete-track', function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
    $('#pcounter').val($('#tracking_table tbody tr').length);
});
    // switching tabs for clients and status
$(document).ready(function() {
    $('.scanStat li').on('click', function(e) {
        e.preventDefault();
        handleScanStatusChange($(this).text());
    });

    $('.custTab-ul li').on('click', function(e) {
        e.preventDefault();
        handleCustomerTabChange($(this).text());
    });

    function handleScanStatusChange(status) {
        updateScanStatus(status);
        if (status === 'Outgoing') {
            switchToCustomerTab("Current Clients");
        }
    }

    function handleCustomerTabChange(custTab) {
        let isNewCustomer = custTab === 'New Clients';

        // ✅ Show/Hide relevant fields for "New Clients"
        $('.contact-div, .lbl-div').toggle(isNewCustomer);

        // ✅ Set #lbl value when selecting "New Clients"
        if (isNewCustomer) {
            $('#lbl').text("Rent a Mailbox for $15/ month, Avoid Porch Pirates, We accept all packages").show();
        } else {
            $('#lbl').text('').hide();
        }

        // ✅ Reset Mailbox & Customer when switching
        $('#mailbox, #customer').val('').removeAttr('data-mb').removeAttr('data-mc');

        // ✅ Clear errors (if any)
        $('#mailbox-error, #customer-error').text('');

        if (isNewCustomer) {
            switchToScanStatus("Incoming");
        }
    }

    function updateScanStatus(status) {
        let isOutgoing = status === 'Outgoing';

        // ✅ Reset Inputs
        $('#mailbox, #customer, #pcounter').val('').prop('readonly', isOutgoing);
        $('#sms').val(isOutgoing
            ? 'Thanks for Picking up the package!'
            : 'You have a package ready for pickup. Please collect it at your earliest convenience. Thanks!');

        // ✅ Focus on relevant input field
        if (isOutgoing) {
            $('#track_number').focus();
        } else {
            $('#mailbox').focus();
        }

        // ✅ Clear tracking table
        $('#tracking_table tbody').empty();
        $('#pcounter').val($('#tracking_table tbody tr').length);
    }

    function switchToCustomerTab(tabName) {
        let tab = $(`.custTab-ul li:contains("${tabName}")`);
        if (tab.length) {
            tab.trigger('click');
        }
    }

    function switchToScanStatus(statusName) {
        let status = $(`.scanStat li:contains("${statusName}")`);
        if (status.length) {
            status.trigger('click');
        }
    }
});


// Packagelogs
$(document).ready(function () {
    // Handle dropdown selection and fetch packages based on status
    $('.package_Logstat li').on('click', function () {
        let status = $(this).text().trim(); // Get selected status (Incoming/Outgoing)
        // Update the dropdown button text
        $('#custom-dropdown-btn').text(status).data('stat', status);
        // Show/Hide Delete All column based on status
        if (status === 'Outgoing') {
            $('#deleteAllColumn').show();
        } else {
            $('#deleteAllColumn').hide();
        }
        // Fetch updated package logs
        fetchPackages(status);
    });

    // Function to fetch package logs based on status
    function fetchPackages(status) {
        $.ajax({
            url: '/get-packages',
            method: 'GET',
            data: { status: status },
            success: function (response) {
                updatePackageTable(response);
            },
            error: function () {
                alert('Error fetching package logs.');
            }
        });
    }

    // Function to update the package table dynamically
    function updatePackageTable(packages) {
        let tableBody = $('#packageLogs tbody');
        tableBody.empty(); // Clear existing rows

        if (!packages.length) {
            tableBody.append('<tr><td colspan="8" class="text-center text-white py-3">No records found.</td></tr>');
            return;
        }

        packages.forEach(function (packageGroup) {
            let trackingNumbers = packageGroup.tracking_numbers.join(', <br>');
            let isOutgoing = packageGroup.status === 'Outgoing';
            let formattedDate = new Date(packageGroup.date_received).toLocaleDateString('en-GB');

            let row = `
                <tr>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.mailbox_number}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.customer_name}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.phone_number}</td>
                    <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">${packageGroup.package_count}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${trackingNumbers}</td>
                    <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.status}</td>
                    <td class="py-3 pr-5 pl-4 text-left text-sm font-semibold text-white">${formattedDate}</td>
                    ${isOutgoing ? `<td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white"><button class="delete-btn text-red-600 hover:text-red-900" data-id="${packageGroup.id}">Delete</button></td>` : ''}
                </tr>`;

            tableBody.append(row);
        });
    }

    // Handle delete button click for dynamically generated elements
    $(document).on('click', '.delete-btn', function () {
        let row = $(this).closest('tr');
        let mailboxNumber = row.find('td:first').text().trim();
        let status = row.find('td:nth-child(6)').text().trim();

        if (status !== 'Outgoing') {
            alert("Only 'Outgoing' packages can be deleted.");
            return;
        }

        if (confirm("Are you sure you want to delete all 'Outgoing' tracking numbers for Mailbox #" + mailboxNumber + "?")) {
            $.ajax({
                url: '/deletePackage',
                type: 'POST',
                data: { mailbox_number: mailboxNumber, status: status },
                success: function (response) {
                    alert(response.message);
                    fetchPackages($('#custom-dropdown-btn').data('stat')); // Refresh table
                },
                error: function () {
                    alert("Error deleting package.");
                }
            });
        }
    });

    // Delete all outgoing records
    $('#deleteAllBtn').click(function () {
        if (confirm("Are you sure you want to delete all 'Outgoing' packages? This action cannot be undone.")) {
            $.ajax({
                url: '/deleteAllOutgoing',
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    if (response.success) {
                        fetchPackages($('#custom-dropdown-btn').data('stat')); // Refresh table
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("Error deleting outgoing packages.");
                }
            });
        }
    });

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


