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
            if (custTab === 'New Clients') {
                let phoneInput = $('#cnumber').val().trim(); // Get the input value and trim whitespace

                if ($('#cnumber').is(':visible') && phoneInput !== '') {
                    let cleanedPhone = phoneInput.replace(/[\s-]/g, ''); // Remove spaces & dashes

                    if (!/^\d{10,14}$/.test(cleanedPhone)) { // Ensure 10-14 digits
                        alert('Invalid phone number. Must be between 10 and 14 digits.');
                        return;
                    }
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
            let mailboxNumber = $(this).find('td:eq(0) input').val().trim();
            let customer = $(this).find('td:eq(3) input').val().trim();

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
                                ${isNewClient ? `<p>${customLbl}</p>` : ''}
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
                                ${isNewClient ? '': `<h5 class="font-medium">${customerPhone ? `Contact: ${customerPhone}` : ''}</h5>`}
                            </div>
                            <div>
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
$(document).ready(function () {
    let capturedImages = [];
    let mediaStream = null;

    function previewImage(blob) {
        const url = URL.createObjectURL(blob);
        const img = $('<img>').attr('src', url).addClass('w-20 h-20 object-cover rounded border');
        $('#imagePreview').append(img);
    }

    // Handle file input preview
    $('#package_image').on('change', function (e) {
        $('#imagePreview').empty(); // clear existing preview if you want fresh batch
        for (let file of e.target.files) {
            previewImage(file);
        }
    });

    // Start camera
    $('#startCamera').on('click', function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                mediaStream = stream;
                $('#cameraStream').removeClass('hidden').get(0).srcObject = stream;
                $('#captureImage').removeClass('hidden');
                $('#cancelCamera').removeClass('hidden');
            })
            .catch(err => {
                alert('Camera not available: ' + err.message);
            });
    });

    // Capture image
    $('#captureImage').on('click', function () {
        const video = document.getElementById('cameraStream');
        const canvas = document.getElementById('snapshot');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            const file = new File([blob], `captured_${Date.now()}.jpg`, { type: 'image/jpeg' });
            capturedImages.push(file);
            previewImage(file);
        }, 'image/jpeg');

        stopCamera();
    });

    $('#cancelCamera').on('click', function () {
        stopCamera();
    });

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
        $('#cameraStream').addClass('hidden').get(0).srcObject = null;
        $('#captureImage').addClass('hidden');
        $('#cancelCamera').addClass('hidden');
    }

    function getEmail(mailboxNumber){
        let email = '';
        $('#clientTable tbody tr').each(function () {
            let mailbox = $(this).find('td:eq(0) input').val().trim();
            if (mailbox == mailboxNumber) {
                email = $(this).find('td:eq(8) input').val().trim();
                return false; // Stop iteration once we find the match
            }
        });
        return email;
    }

    function getCustomerInfo(mailboxNumber) {
        let customerInfo = { name: '', phone: '' };

        $('#clientTable tbody tr').each(function () {
            let mailbox = $(this).find('td:eq(0) input').val().trim();
            if (mailbox == mailboxNumber) {
                customerInfo.name = $(this).find('td:eq(3) input').val().trim();
                customerInfo.phone = $(this).find('td:eq(4) input').val().trim();
                return false;
            }
        });

        return customerInfo;
    }

    function appendUploadedImages(formData) {
        const files = $('#package_image')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('package_images[]', files[i]);
        }
    }

    function appendCapturedImages(formData) {
        capturedImages.forEach((file, index) => {
            formData.append('captured_images[]', file);
        });
    }

    $('#packageForm').submit(function (event) {
        event.preventDefault();

        let formData = new FormData();
        let custTab = $('#custTab1-dropdown-btn').text().trim();
        let package_stat = $('.package_stat').attr('data-stat');
        let num_packages = $('#pcounter').val();
        // let email = $('#email').val();
        let sms = $('#sms').val();
        let trackingNumbers = [];

        $('#tracking_table tbody tr').each(function () {
            trackingNumbers.push($(this).find('span').text().trim());
        });

        trackingNumbers.forEach(num => formData.append('tracking_numbers[]', num));

        let customerName = '', customerPhone = '', mailboxNumber = '', customerEmail = '';

        if (custTab === 'New Clients') {
            customerName = $('#customer').val().trim();
            customerPhone = $('#cnumber').val().trim().replace(/\D/g, '');
            email = $('#email').val().trim();
        } else {
            mailboxNumber = $('#mailbox').attr('data-mb');
            let customer = getCustomerInfo(mailboxNumber);
            customerName = customer.name;
            customerPhone = customer.phone;
            customerEmail = getEmail(mailboxNumber);
        }


        if (!customerName) {
            alert('Valid customer name and phone number are required.');
            return;
        }

        if (custTab !== 'New Clients' && (!mailboxNumber || !customerName || !customerPhone)) {
            alert('Invalid mailbox or customer information. Please check and try again.');
            return;
        }

        formData.append('customer_name', customerName);
        formData.append('customer_phone', customerPhone);
        formData.append('mailbox', mailboxNumber || '');
        formData.append('package_status', package_stat);
        formData.append('num_packages', num_packages);
        formData.append('customer_email', customerEmail || '');
        formData.append('sms', sms);

        appendUploadedImages(formData);
        appendCapturedImages(formData);

        let ajaxUrl = package_stat === 'Incoming' ? '/saveAndNotify' : '/outgoing-packge';

        // alert(customerEmail);
        $('#loadingScreen').removeClass('hidden');
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $('#loadingScreen').addClass('hidden');
                alert(response.message);
                location.reload();
            },
            error: function (xhr) {
                $('#loadingScreen').addClass('hidden');
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

    // table editing
$(document).ready(function() {
    $("[class^='edit-']").on("click", function(e) {
        e.preventDefault();
        $(this).prop('hidden', true);
        const $row = $(this).closest('tr');

        // Loop over all <td>s with an <input> (exclude index 0)
        $row.find('td:gt(0)').each(function () {
            const $input = $(this).find('input');
            if ($input.length) {
                $input.prop('disabled', false).addClass('border-1 border-white'); // Enable all except the first column

                const colIndex = parseInt($input.data('index'));
                const raw = $input.data('raw');
                const value = (colIndex === 5 || colIndex === 7) ? parseToISODate(raw) : $input.val();
                const type = (colIndex === 5 || colIndex === 7) ? 'date' : $input.attr('type');

                const newInput = $('<input>', {
                    type: type,
                    value: value,
                    class: 'edit-info w-full border-1 border-white rounded-sm',
                    'data-index': colIndex,
                    'data-raw': raw
                });

                $input.replaceWith(newInput);
            }
        });
        $row.find('.cancel-edit').prop('hidden', false);
        $row.find('.save-edit').prop('hidden', false); // show the save button
    });
});
    // cancel edit
$(document).on("click", ".cancel-edit", function(e) {
    e.preventDefault();
    $(this).prop('hidden', true); // Hide the cancel button

    const $row = $(this).closest('tr');

    // Loop over all <td>s with an <input> (exclude index 0)
    $row.find('td:gt(0)').each(function () {
        const $input = $(this).find('input');
        if ($input.length) {
            $input.prop('disabled', true).removeClass('border-1 border-white');
        }
    });
    $row.find("[class^='edit-']").prop('hidden', false); // Show the edit button again
    $row.find('.save-edit').prop('hidden', true); // Hide the save button
});
    // edit info
$(document).on("input change", ".edit-info", function(e) {
    e.preventDefault();
    const $row = $(this).closest("tr");

    let rowData = {};
    let isValid = true;
    // Index-based mapping from CSV columns
    $row.find("input.edit-info").each(function () {
        const index = $(this).data("index");

        // Read native DOM value directly, with a fallback to jQuery val()
        let value = this.value ?? $(this).val();

        // For date inputs, treat empty string as null
        if ($(this).attr('type') === 'date') {
            if (!value) {
                value = null;
            }
        } else {
            value = value.trim();
        }

        // 📞 Format phone number
        if (index === 4) {
            let digits = value.replace(/\D/g, ''); // Remove non-digits

            if (digits.length > 10) {
                digits = digits.slice(0, 10); // Limit to 10 digits
            }

            if (digits.length >= 7) {
                value = `(${digits.slice(0,3)}) ${digits.slice(3,6)}-${digits.slice(6)}`;
            } else if (digits.length >= 4) {
                value = `(${digits.slice(0,3)}) ${digits.slice(3)}`;
            } else if (digits.length >= 1) {
                value = `(${digits}`;
            }

            $(this).val(value);
        }

        switch (index) {
            case 0:
                rowData.mailbox = value; break;
            case 1:
                rowData.size = value; break;
            case 2:
                rowData.status = value; break;
            case 3:
                rowData.customer = value; break;
            case 4:
                rowData.phone = value; break;
            case 5:
                rowData.date_close = value; break;
            case 6:
                rowData.term = value; break;
            case 7:
                rowData.due_date = value; break;
            case 8:
                rowData.email = value; break;
        }
    });
    // console.log('Updated rowData:', rowData);
});
$(document).on("keypress", 'input.edit-info[data-index="4"]', function (e) {
    const char = String.fromCharCode(e.which);
    if (!/[0-9]/.test(char)) {
        e.preventDefault();
    }
});
    // Email validation
function isValidEmail(email) {
    // Simple regex for email validation
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
    // save edited info
$(document).on("click", ".save-edit", function(e) {
    e.preventDefault();
    const $row = $(this).closest("tr");

    let rowData = {};

    $row.find("input.edit-info").each(function () {
        const index = $(this).data("index");
        let value = this.value ?? $(this).val();

        // For date inputs, treat empty string as null
        if ($(this).attr('type') === 'date') {
            if (!value) {
                value = null;
            }
        } else {
            value = value.trim();
        }

        // Email validation for index 8
        if (index === 8) {
            if (value && !isValidEmail(value)) {
                alert('Please enter a valid email address.');
                $(this).focus();
                isValid = false;
                return false;  // stop the each() loop
            }
        }

        switch (index) {
            case 0: rowData.mailbox = value; break;
            case 1: rowData.size = value; break;
            case 2: rowData.status = value; break;
            case 3: rowData.customer = value; break;
            case 4: rowData.phone = value; break;
            case 5: rowData.date_close = value; break;
            case 6: rowData.term = value; break;
            case 7: rowData.due_date = value; break;
            case 8: rowData.email = value; break;
        }
    });



        $.post("/update-csv", rowData, function (response) {
            alert(response.message);
            $row.find('td:gt(0)').each(function () {
                const $input = $(this).find('input');
                if ($input.length) {
                    $input.prop('disabled', true).removeClass('border-1 border-white');
                }
            });
            $row.find("[class^='edit-']").prop('hidden', false); // Show the edit button again
            $row.find('.save-edit').prop('hidden', true);
            $row.find('.cancel-edit').prop('hidden', true);
        }).fail(function () {
            alert("Error saving data.");
        });


});
    // dateformatting for input fields
function parseToISODate(value) {
    if (!value) return '';

    // Match ISO yyyy-mm-dd or yyyy/mm/dd
    const isoMatch = value.match(/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/);
    if (isoMatch) {
        const y = isoMatch[1];
        const m = isoMatch[2].padStart(2, '0');
        const d = isoMatch[3].padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // Match US mm/dd/yyyy
    const usMatch = value.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
    if (usMatch) {
        const m = usMatch[1].padStart(2, '0');
        const d = usMatch[2].padStart(2, '0');
        const y = usMatch[3];
        return `${y}-${m}-${d}`;
    }

    // Attempt native Date parsing as fallback
    const date = new Date(value);
    if (!isNaN(date.getTime())) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    return '';
}


// Packagelogs
$(document).ready(function () {
    $('.package_Logstat li').on('click', function () {
        let status = $(this).text().trim();
        $('#custom-dropdown-btn').text(status).data('stat', status);

        if (status === 'Outgoing') {
            $('#actionsText').hide();
            $('#deleteAllBtn').show();
        } else {
            $('#actionsText').show();
            $('#deleteAllBtn').hide();
        }

        fetchPackages(status);
    });
});

function fetchPackages(status) {
    $.ajax({
        url: '/get-packages',
        method: 'GET',
        data: { status: status },
        success: function (response) {
            updatePackageTable(response, status);
        },
        error: function () {
            alert('Error fetching package logs.');
        }
    });
}

function updatePackageTable(packages) {
    let tableBody = $('#packageLogs tbody');
    tableBody.empty();

    if (!packages.length) {
        tableBody.append('<tr><td colspan="10" class="text-center text-white py-3">No records found.</td></tr>');
        return;
    }

    packages.forEach(function (packageGroup) {
        let formattedDate = new Date(packageGroup.date_received).toLocaleDateString('en-GB');
        let isOutgoing = packageGroup.status === 'Outgoing';
        let actionButtons = '';

        if (isOutgoing) {
            actionButtons = `<button class="delete-btn text-red-600 hover:text-red-900" data-id="${packageGroup.id}">Delete</button>`;
        } else {
            // Create a single claim button for the whole group
            actionButtons = `
                <button class="update-group-status-btn rounded-sm text-white border-blue-950 bg-blue-800 px-1 py-0.5 hover:bg-blue-900 hover:text-gray-500 whitespace-nowrap"
                    data-ids="${packageGroup.id.join(',')}"
                    data-trackings="${packageGroup.tracking_numbers.join(',')}"
                    data-customer="${packageGroup.customer_name}">
                    Claim Package
                </button>`;
        }

        let row = `
            <tr>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.mailbox_number}</td>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.customer_name}</td>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.phone_number}</td>
                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">${packageGroup.package_count}</td>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.tracking_numbers.join('<br>')}</td>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${packageGroup.status}</td>
                <td class="py-3 pr-3 pl-4 text-left text-sm font-semibold text-white">${formattedDate}</td>
                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">${packageGroup.id.join('<br>')}</td>
                <td class="py-3 pr-3 pl-4 text-center text-sm font-semibold text-white">${actionButtons}</td>
            </tr>`;

        tableBody.append(row);
    });
}
    // Handle Claim Package → Change to Outgoing
$(document).on('click','.update-group-status-btn', function() {
        var ids = $(this).data('ids').toString().split(',');
        var trackings = $(this).data('trackings').toString().split(',');
        var customer = $(this).data('customer');

        var packages = ids.map(function(id, index) {
            return {
                id: parseInt(id),
                tracking_number: trackings[index]
            };
        });

        const payload = {
            packages: packages,
            status: "Outgoing",
            sms: "Your package has been claimed!"
        };

        console.log("Sending bulk payload:", payload);

        ids.forEach(function(id, index) {
            $.ajax({
                url: "/updatePackageStatus",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(
                    payload
                    // id: parseInt(id),
                    // tracking_number: trackings[index],
                    // status: "Outgoing",
                    // sms: "Your package has been claimed!"
                ),
                success: function(response) {
                    if (response.success === false) {
                        console.error(response.message);
                    } else {
                        console.log(response.message);
                        // location.reload(); // Optional: refresh page after update
                        fetchPackages($('#custom-dropdown-btn').data('stat'));
                    }
                },
                error: function(xhr) {
                    console.error("Request failed: " + xhr.responseText);
                }
            });
        });
    let initialStatus = $('#custom-dropdown-btn').data('stat') || 'Incoming';
    fetchPackages(initialStatus);
});
    // Delete a single package
$(document).on('click', '.delete-btn', function () {
    let packageId = $(this).data('id');

    $.ajax({
        url: '/delete-package',
        method: 'POST',
        data: {
            package_id: packageId,
            status: 'Outgoing',
            _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF
        },
        success: function (response) {
            alert(response.message);
            fetchPackages('Outgoing'); // Refresh table
        },
        error: function (xhr) {
            alert(xhr.responseJSON.message);
        }
    });
});
    // Delete all outgoing packages
$('#deleteAllBtn').on('click', function () {
    if (!confirm('Are you sure you want to delete all outgoing packages?')) return;

    $.ajax({
        url: '/delete-package',
        method: 'POST',
        data: {
            status: 'Outgoing',
            _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF
        },
        success: function (response) {
            alert(response.message);
            fetchPackages('Outgoing'); // Refresh table
        },
        error: function (xhr) {
            alert(xhr.responseJSON.message);
        }
    });
});
    // seacrch function
$(document).ready(function () {
    $("#searchInput").on("keyup", function () {
        let query = $(this).val().toLowerCase();

        $(".package-row").each(function () {
            let mailbox = $(this).find("td:nth-child(1)").text().toLowerCase();
            let customer = $(this).find("td:nth-child(2)").text().toLowerCase();
            let packageId = $(this).find("td:nth-child(8)").text().toLowerCase();
            let tracking_number = $(this).find("td:nth-child(5)").text().toLowerCase();

            if (mailbox.includes(query) || customer.includes(query) || packageId.includes(query) || tracking_number.includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
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
            mailbox: $(this).find('td:eq(0) input').val().trim(),
            customer: $(this).find('td:eq(3) input').val().trim(),
            phone: $(this).find('td:eq(4) input').val().trim()
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
