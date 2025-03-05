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
            const mailbox = parseInt($('#mailbox').val()) || 0;
            const mailboxcount = $('#mailbox').attr('data-mc');

            if (mailbox > 0) {
                if(mailboxcount >0){
                    if ($('#tracking_table tbody tr').length < packageLimit) {
                        const value = $.trim($('#track_number').val());
                        if (value) {
                            $('#tracking_table tbody').attr('data-total', classCounter);
                            $('#tracking_table tbody').append('<tr><td class="'+ uniqueClass +' p-1 ml-25 flex justify-between items-center"> <span class="text-center flex-1">' + value +
                                '</span><button class="print-btn bg-blue-500 text-white px-2 py-1 mr-2 rounded hover:bg-blue-600"  data-lbl="'+ value +'">Print</button>' +
                                '<button class="delete-btn p-1 bg-red-500 text-white rounded">Delete</button></td></tr>');
                            $('#track_number').val('');
                        }
                    } else {
                        alert('Package limit reached!');
                        $('#track_number').val('');
                    }
                    updateTotalCount();
                }else{
                    alert('Mailbox does not Exist select a new one');
                    $('#track_number').val('');
                }
            }else{
                alert('Add Mailbox First!');
                $('#track_number').val('');
            }

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
    $('#tracking_table').on('click', '.print-btn', function() {
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
                        }
                    </style>
                </head>
                <body class="h-screen m-0 p-1">
                    <div class="grid grid-rows-4 border justify-around border-black w-[4in] h-[6in]">
                        <div class="flex justify-between">
                            <span class="px-2">Mailbox</span><span class="px-2">Package ID</span>
                        </div>
                        <div class="flex justify-betwen">
                            <h1 class="px-2 mx-2 text-7xl">${mailbox}</h1><h1 class="px-2 mx-2 text-7xl">005</h1>
                        </div>
                        <div class="flex justify-center">
                            <svg id="barcode"></svg>
                        </div>
                        <div class="flex justify-between px-2">
                            <h2>Tracking Number:</h2>
                            <h2>${trackingNumber}</h2>
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
        printWindow.print();
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

        $.ajax({
            url: '/saveAndNotify',
            type: 'POST',
            data: formData,
            success: function(response) {
                let message = `
                Mailbox: ${response.mailbox}\n
                Number of Packages: ${response.num_packages}\n
                Tracking Numbers: ${response.tracking_numbers}\n
                Customer Name: ${response.customer_name}\n
                Customer Phone: ${response.customer_phone}\n
                Package Status: ${response.package_status}
            `;
            alert(message);
                // location.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
// twilio
$(document).ready(function() {
    // Toggle the SMS inbox panel
    $('#toggle-inbox').on('click', function() {
        $('#inbox-panel').toggleClass('hidden');
    });

    // Handle reply form submission with AJAX
    $('.reply-form').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let formData = form.serialize();

        $.post(form.attr('action'), formData, function(response) {
            alert('Message sent successfully!');
            form.find('textarea').val('');
        }).fail(function(xhr) {
            alert('Error: ' + xhr.responseText);
        });
    });
});
