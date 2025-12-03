// SMS Inbox functionality
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
                if (typeof window.showToast === 'function') {
                    window.showToast(response.success || 'Reply sent successfully!', 'success');
                } else if (typeof showToast === 'function') {
                    showToast(response.success || 'Reply sent successfully!', 'success');
                } else {
                    alert(response.success || 'Reply sent successfully!');
                }
                form.trigger('reset'); // Clear the form on success
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Failed to send reply.';
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMsg, 'error');
                } else if (typeof showToast === 'function') {
                    showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
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
                if (typeof window.showToast === 'function') {
                    window.showToast(response.success || 'Message sent successfully!', 'success');
                } else if (typeof showToast === 'function') {
                    showToast(response.success || 'Message sent successfully!', 'success');
                } else {
                    alert(response.success || 'Message sent successfully!');
                }
                form.trigger('reset'); // Clear the form on success
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Failed to send message.';
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMsg, 'error');
                } else if (typeof showToast === 'function') {
                    showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        });
    });

    // Handle Text Blast form submission via AJAX
    $('#textblast form').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            success: function (response) {
                // Try to use the dashboard's toast function first
                if (typeof window.showToast === 'function') {
                    window.showToast('Text blast sent successfully!', 'success');
                } else if (typeof showToast === 'function') {
                    showToast('Text blast sent successfully!', 'success');
                } else {
                    alert('Text blast sent successfully!');
                }
                form.trigger('reset'); // Clear the form on success
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Failed to send text blast.';
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMsg, 'error');
                } else if (typeof showToast === 'function') {
                    showToast(errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        });
    });

    // Initialize autocomplete functionality if customer data is available
    if (typeof initializeSMSAutocomplete === 'function') {
        initializeSMSAutocomplete();
    }

    // Close on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#sms-inbox, .autocomplete-suggestions').length) {
            if (!$('#inbox-panel').hasClass('hidden')) {
                $('#inbox-panel').addClass('hidden opacity-0 translate-y-4');
            }
        }
    });
});

// Function to initialize SMS autocomplete (to be called from pages with customer data)
function initializeSMSAutocomplete() {
    // Check if customersData is available globally
    if (typeof customersData === 'undefined') {
        console.log('Customer data not available for SMS autocomplete');
        return;
    }

    // Setup autocomplete for SMS forms
    function setupAutocomplete(input, key, phoneInput) {
        $(input).on('input', function (e) {
            e.stopPropagation();
            const query = $(this).val().toLowerCase();
            const suggestions = customersData.filter(item =>
                item[key] && item[key].toLowerCase().includes(query)
            );
            showSuggestions($(this), suggestions, key, phoneInput);
        });

        $(input).on('click', function (e) {
            e.stopPropagation();
        });
    }

    // Display autocomplete suggestions
    function showSuggestions(input, suggestions, key, phoneInput) {
        let suggestionBox = input.siblings('.autocomplete-suggestions');

        if (suggestionBox.length === 0) {
            suggestionBox = $('<div class="autocomplete-suggestions"></div>');
            input.parent().css('position', 'relative').append(suggestionBox);
        }

        suggestionBox.empty();

        if (suggestions.length === 0 || input.val().length < 2) {
            suggestionBox.hide();
            return;
        }

        suggestions.slice(0, 5).forEach(item => {
            const suggestionItem = $(`
                <div class="suggestion-item">
                    ${item.mailbox || 'N/A'} - ${item.customer || 'N/A'} - ${item.phone || 'No phone'}
                </div>
            `);

            suggestionItem.on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                input.val(item[key]);
                if (phoneInput && item.phone && item.phone !== 'No phone') {
                    // Special handling for text blast phone numbers (append instead of replace)
                    if (phoneInput.attr('id') === 'phone_numbers') {
                        const currentPhones = phoneInput.val().trim();
                        if (currentPhones) {
                            // Add comma and phone if there are existing numbers
                            phoneInput.val(currentPhones + ',' + item.phone);
                        } else {
                            phoneInput.val(item.phone);
                        }
                    } else {
                        // Regular single phone number input
                        phoneInput.val(item.phone);
                    }
                }
                suggestionBox.empty().hide();
            });

            suggestionBox.append(suggestionItem);
        });

        suggestionBox.show();
    }

    // Setup autocomplete for create message form
    setupAutocomplete('#search-mailbox', 'mailbox', $('#phone'));
    setupAutocomplete('#search-customer', 'customer', $('#phone'));

    // Setup autocomplete for text blast form with special handling for multiple phone numbers
    setupAutocomplete('#search-mailbox-blast', 'mailbox', $('#phone_numbers'));
    setupAutocomplete('#search-customer-blast', 'customer', $('#phone_numbers'));

    // Hide suggestions on outside click
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.autocomplete-suggestions, #search-mailbox, #search-customer, #search-mailbox-blast, #search-customer-blast, #sms-inbox').length) {
            $('.autocomplete-suggestions').empty().hide();
        }
    });
}
