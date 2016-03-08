/* Client side scripting for editing groups */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-group-edit');

    // If the page exists
    if(page.length) {

        // Group selector
        var group = $('#group');

        // On click of the update group button
        page.find('#update-group').on('click', function () {

            ds_clear_errors(true);

            // Set the button
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Updating Group...');

            // POST request data
            var data = {
                group:       group.val(),
                group_id:    group.data('group-id'),
                group_old:   group.data('group-old'),
                description: $('#description').val(),
                permissions: $('form').serializeArray()
            };

            // Send the POST request
            $.post(ajax + 'update_group.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

                // Update the old values
                if (response.status === 'OK') {

                    group.data('group-old', group.val());
                    $('.modal-body strong').html(group.val());

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-group-edit > .btn-danger'
                );

                // Re-enable the button
                button.lbtn(true, 'Update Group');

            });

        });

        // On click of the delete group button
        page.find('.modal-footer .btn-danger').on('click', function() {

            // Set the button
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Deleting Group...');

            // POST request data
            var data = {
                group_id: group.data('group-id'),
                group:    group.data('group-old')
            };

            // Send the POST request
            $.post(ajax + 'delete_group.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

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
                    button.lbtn(true, 'Delete Group');

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