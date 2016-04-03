/* Client side scripting for editing permissions */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-permission-edit');

    // If the page exists
    if(page.length) {

        // Permission selector
        var permission = $('#permission');

        // On click of the update permission button
        page.find('#update-permission').on('click', function() {

            // Clear any errors
            ds_clear_errors(true);

            // Set the button
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Updating Permission...');

            // POST request data
            var data = {
                permission:     permission.val(),
                description:    $('#description').val(),
                permission_old: permission.data('permission-old'),
                permission_id:  permission.data('permission-id')
            };

            // Send the POST request
            $.post(ajax + 'update_permission.php', data, function(response) {

                // Update the old values
                if(response.status === 'OK') {
                    permission.data('permission-old', response.data);
                    $('.modal-body strong').html(response.data);
                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-permission-edit > .btn-danger'
                );

                // Permission feedback
                if(
                    response.status === 'PERM_FAIL' ||
                    response.status === 'PERM_L_FAIL'
                ) ds_error('.permission-grp');

                // Description feedback
                if(response.status === 'DESC_L_FAIL')
                    ds_error('.description-grp');

                // Re-enable the button
                button.lbtn(true, 'Update Permission');

            });

        });

        // On click of the delete permission button
        page.find('.modal-footer .btn-danger').on('click', function() {

            // Set the button
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Deleting Permission...');

            // POST request data
            var data = {
                permission_id:  permission.data('permission-id'),
                permission_old: permission.data('permission-old')
            };

            // Send the POST request
            $.post(ajax + 'delete_permission.php', data, function(response) {

                // Toggle the modal
                $('.modal').modal('toggle');

                // If the permission was deleted successfully
                if(response.status === 'OK') {

                    // Redirect the user
                    setTimeout(function() {

                        document.location.href =
                            'administrator/permissions/list';

                    }, 800);

                } else {

                    // Re-enable the button
                    button.lbtn(false, 'Delete Permission')

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