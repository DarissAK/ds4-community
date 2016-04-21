/* Client side scripting for the permission list */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-permission-list');

    // If the page exists
    if(page.length) {

        // Bind filter event and update permission count
        page.find('#search-area strong').html(
            ds_table_search(
                page.find('table'),
                page.find('#search-area input'),
                page.find('#search-area strong')
            )
        );

        // Add permission modal
        page.on('click', '#header-area button', function() {

            // Clear any errors
            ds_clear_errors();

            // Reset the inputs and errors
            $('#name').val('');
            $('#description').val('');

            // Toggle the add modal
            $('.modal').modal();

        });

        // Add permission
        page.on('click', '.modal .btn-primary', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Adding...');

            // Request data
            var data = {
                name:        $('#name').val(),
                description: $('#description').val()
            };

            // Send the request
            $.post(ajax + 'permission_add.php', data, function(response) {

                // Set error location
                var error = '.modal-body input:last';

                // Add Success
                if(response.status === 'OK') {

                    // Counter updates
                    page.find('#search-area strong').cUpdate();

                    // Append the new row
                    page.find('table tbody').append(response.data);

                    // Close the modal
                    $('.modal').modal('toggle');

                    // Set error location
                    error = '#alert-entry';

                }

                // Display alert
                ds_alert(
                    response.message,
                    response.severity,
                    error,
                    response.status
                );

                // Set button state
                button.lBtn(true, 'Add');

            });

        });

        // Edit permission
        page.on('click', 'tbody tr', function() {

            // Get the permission ID
            var id = $(this).attr('data-id');

            // Redirect the user
            document.location.href =
                '/administrator/permissions/list/edit/' + id;

        });

    }

});