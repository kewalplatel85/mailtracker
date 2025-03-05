import $ from 'jquery';

$(document).ready(function () {

    const currentPath = window.location.pathname;
    // Highlight the active link dynamically
    $('.nav-link').each(function () {
        const linkRoute = $(this).attr('data-route'); // Get the route from the data-route attribute
        const linkPath = new URL(linkRoute).pathname;

        if (currentPath === linkPath) {
            $(this)
                .removeClass('text-gray-300 hover:bg-gray-700 hover:text-white')
                .addClass('bg-gray-900 text-white');
        }
    });

    // Mobile menu toggle
    const $mobileMenuButton = $('[aria-controls="mobile-menu"]');
    const $mobileMenu = $('#mobile-menu');

    $mobileMenuButton.on('click', function () {
        const isHidden = $mobileMenu.hasClass('hidden');
        if (isHidden) {
            $mobileMenu.removeClass('hidden')
                .addClass('transition ease-out duration-100 transform opacity-0 scale-95');
            setTimeout(() => {
                $mobileMenu.removeClass('opacity-0 scale-95')
                    .addClass('opacity-100 scale-100');
            }, 50);
        } else {
            $mobileMenu.removeClass('opacity-100 scale-100')
                .addClass('opacity-0 scale-95');
            setTimeout(() => {
                $mobileMenu.addClass('hidden');
            }, 150);
        }
    });

    // Profile dropdown toggle
    const $userMenuButton = $('#user-menu-button');
    const $userMenu = $('#user-menu');

    $userMenuButton.on('click', function () {
        const isHidden = $userMenu.hasClass('hidden');
        if (isHidden) {
            $userMenu.removeClass('hidden')
                .addClass('transition ease-out duration-100 transform opacity-0 scale-95');
            setTimeout(() => {
                $userMenu.removeClass('opacity-0 scale-95')
                    .addClass('opacity-100 scale-100');
            }, 50);
        } else {
            $userMenu.removeClass('opacity-100 scale-100')
                .addClass('opacity-0 scale-95');
            setTimeout(() => {
                $userMenu.addClass('hidden');
            }, 150);
        }
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function (event) {
        if (!$userMenuButton.is(event.target) && !$userMenuButton.has(event.target).length) {
            $userMenu.removeClass('opacity-100 scale-100')
                .addClass('opacity-0 scale-95');
            setTimeout(() => {
                $userMenu.addClass('hidden');
            }, 150);
        }
    });
});

// dropdown toggle
$(document).ready(function () {
    const select = $('#packageStat');
    const dropdownBtn = $('#custom-dropdown-btn');
    const dropdown = $('#custom-dropdown');

    // Populate Custom Dropdown
    function populateDropdown() {
        dropdown.empty();
        select.find('option').each(function () {
            dropdown.append(`<li class="dropdown-item cursor-pointer px-4 py-2 hover:bg-indigo-600 hover:text-white">${$(this).text()}</li>`);
        });
    }

    populateDropdown();

    // Toggle Dropdown
    dropdownBtn.on('click', function (e) {
        e.preventDefault();
        dropdown.stop(true, true).slideToggle(200);
    });

    // Handle Selection
    dropdown.on('click', 'li', function () {
        const selectedText = $(this).text();
        dropdownBtn.text(selectedText);
        select.val(selectedText);
        dropdown.slideUp(200);
        dropdownBtn.attr('data-stat',selectedText);
        $('#track_number').val('');
    });

    // Close Dropdown on Outside Click
    $(document).on('click', function (e) {
        if (!dropdownBtn.is(e.target) && !dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
            dropdown.slideUp(200);
        }
    });

    $('#pcounter').on('keypress', function(event) {
        if (event.which === 13) { // 13 is the Enter key code
            event.preventDefault(); // Stop form submission
            $('#track_number').focus(); // Move focus to tracking number input
        }
    });
});


