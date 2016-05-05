/* Client side scripting for editing permissions */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/api/permissions/';

    // Set the current page
    var page = $('.ds-permission-edit');

    // If the page exists
    if(page.length) {

        // Update permission
        page.on('click', '#update', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Saving...');

            // Request data
            var data = {
                id:          page.attr('data-id'),
                name:        $('#name').val(),
                old:         page.attr('data-old'),
                description: $('#description').val()
            };

            // Send the request
            $.post(ajax + 'update.php', data, function(response) {

                // Update the old values
                if(response.status === 'OK') {
                    page.attr('data-old', response.data);
                    $('.modal-body strong').html(response.data);
                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-permission-edit > .btn-danger',
                    response.status
                );

                // Set button state
                button.lBtn(true, 'Save Changes');

            });

        });

        // Delete permission modal
        page.on('click', '#delete', function() {

            // Clear any errors
            ds_clear_errors();

            // Open the modal
            $('.modal').modal('show');

        });

        // Delete permission
        page.on('click', '.modal-footer .btn-danger', function() {

            // Set the button
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Deleting...');

            // POST request data
            var data = {
                id:   page.attr('data-id'),
                name: page.attr('data-old')
            };

            // Send the POST request
            $.post(ajax + 'delete.php', data, function(response) {

                // Close the modal
                $('.modal').modal('hide');

                // If the permission was deleted successfully
                if(response.status === 'OK') {

                    // Redirect the user
                    setTimeout(function() {

                        document.location.href =
                            'administrator/permissions/list';

                    }, 800);

                } else {

                    // Set the button state
                    button.lBtn(false, 'Delete')

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-permission-edit > .btn-danger'
                );

            });

        });

    }

});