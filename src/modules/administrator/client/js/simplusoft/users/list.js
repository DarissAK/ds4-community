/* Client side scripting for user lists */
$(function () {

    // Set the current page
    var page = $('.ds-users-list');

    // If the page exists
    if(page.length) {

        // Data table init
        page.find('table').DataTable({
            pageLength: 9,
            lengthMenu: [9, 25, 50, 100]
        });

        // Edit user
        page.on('click', 'tbody tr', function() {

            // Get the user ID
            var id = $(this).attr('data-id');

            // Redirect the user
            if(typeof id !== 'undefined')
                document.location.href =
                    '/administrator/users/list/edit/' + id;

        });

    }

});