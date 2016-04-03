/* Client side scripting for editing groups */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-group-edit');

    // If the page exists
    if(page.length) {

        // Delete modal
        page.on('click', '#delete', function() {

            // Clear any errors
            ds_clear_errors();

            // Open the modal
            $('.modal').modal();

        });

        // Update group
        page.on('click', '#update', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Saving...');

            // POST request data
            var data = {
                id:          page.attr('data-id'),
                name:        $('#name').val(),
                old:         page.attr('data-old'),
                description: $('#description').val(),
                permissions: $('form').serializeArray()
            };

            // Send the POST request
            $.post(ajax + 'group_update.php', data, function(response) {

                // Update the old values
                if (response.status === 'OK') {
                    page.attr('data-old', response.data);
                    $('.modal-body strong').html(response.data);
                }

                // Set feedback
                if(
                    response.status === 'NAME_FAIL' || 
                    response.status === 'NAME_L_FAIL'
                ) ds_error('.name-grp');
                if(
                    response.status === 'DESC_L_FAIL'
                ) ds_error('.description-grp');

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-group-edit > .btn-danger'
                );

                // Re-enable the button
                button.lBtn(true, 'Save Changes');

            });

        });

        // Delete group
        page.on('click', '.modal .btn-danger', function() {

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
            $.post(ajax + 'group_delete.php', data, function(response) {

                // Toggle the modal
                $('.modal').modal('toggle');

                // If the permission was deleted successfully
                if(response.status === 'OK') {

                    // Redirect the user
                    setTimeout(function() {

                        document.location.href =
                            'administrator/permissions/groups';

                    }, 800);

                }

                // Permission was not deleted
                else {

                    // Re-enable the button
                    button.lBtn(true, 'Delete');

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-group-edit > .btn-danger'
                );

            });

        });

    }

});