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
    const dropdownButton = $('button[aria-haspopup="listbox"]');
    const dropdownMenu = $('ul[role="listbox"]');

    // Toggle dropdown visibility
    dropdownButton.on('click', function () {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        $(this).attr('aria-expanded', !isExpanded);
        dropdownMenu.toggleClass('hidden');
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!dropdownButton.is(e.target) && !dropdownMenu.is(e.target) && dropdownMenu.has(e.target).length === 0) {
            dropdownButton.attr('aria-expanded', 'false');
            dropdownMenu.addClass('hidden');
        }
    });

    // Handle option selection
    dropdownMenu.on('click', 'li', function () {
        const selectedText = $(this).find('span.truncate').text().trim();
        dropdownButton.find('.truncate').text(selectedText);

        dropdownMenu.find('li').removeClass('bg-indigo-600 text-white');
        $(this).addClass('bg-indigo-600 text-white');

        dropdownButton.attr('aria-expanded', 'false');
        dropdownMenu.addClass('hidden');
    });

    // Highlight on hover
    dropdownMenu.on('mouseenter', 'li', function () {
        $(this).addClass('bg-indigo-600 text-white');
    }).on('mouseleave', 'li', function () {
        if (!$(this).hasClass('selected')) {
            $(this).removeClass('bg-indigo-600 text-white');
        }
    });
});

