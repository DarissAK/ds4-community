/* Client side scripting for editing users */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/api/users/';

    // Set current page
    var page = $('.ds-user-edit');

    // If the page exists
    if(page.length) {

        // Set tab text
        $('#ds-nav-administrator-users-list').find('a').html('Edit User');

        // Bind the click event to toggle the password area
        page.find('#reset-password a').on('click', function() {
            $('#password-area').toggleClass('hide');
        });

        // Set error location
        var error = '.ds-user-edit > .btn-danger';

        // Update user
        page.on('click', '#update', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Saving...');

            // Password selectors
            var password_1 = $('#password-1');
            var password_2 = $('#password-2');

            // POST data
            var data = {
                id:         page.data('id'),
                username:   $('#username').val(),
                old:        page.data('old'),
                status:     $('#status').val(),
                group:      $('#group').val(),
                password_1: password_1.val(),
                password_2: password_2.val()
            };

            // Administrator selector
            var select = $('#administrator-select');

            // If the administrator selector is present
            if(select.length) {
                data['administrator'] = select.val();
            }

            // Send the POST request
            $.post(ajax + 'update.php', data, function(response) {

                // OK Response
                if(response.status === 'OK') {

                    // Update old values
                    page.data('old', response.data);
                    page.find('.modal strong').html(response.data);

                    // Set password fields to blank
                    password_1.val('');
                    password_2.val('');

                }

                // Display alert
                ds_alert(
                    response.message,
                    response.severity,
                    error,
                    response.status
                );

                // Set button state
                button.lBtn(true, 'Update User');

            });

        });

        // Delete user modal
        page.on('click', '#delete', function() {

            // Clear any errors
            ds_clear_errors();

            // Open the modal
            $('.modal').modal('show');

        });

        // Delete user
        page.on('click', '.modal .btn-danger', function() {

            // Button for deleting users
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Deleting...');

            // User to be deleted
            var data = {
                id:       page.data('id'),
                username: page.data('old')
            };

            // Send the POST request
            $.post(ajax + 'delete.php', data, function(response) {

                // Request successful
                if(response.status === 'OK') {

                    // Redirect the user
                    setTimeout(function() {

                        document.location.href =
                            'administrator/users/list';

                    }, 800);

                }

                // Request failed
                else {

                    // Set button state
                    button.lBtn(true, 'Delete User');

                }

                // Display alert message
                ds_alert(response.message, response.severity, error);

                // Close the modal
                $('.modal').modal('hide');

            });

        });

    }

});