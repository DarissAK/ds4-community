/* Client side scripting for editing users */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

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

            // Disable the button to prevent multiple updates
            button.lBtn(false, 'Saving...');

            // Password selectors
            var password_1 = $('#password-1');
            var password_2 = $('#password-2');

            // POST data
            var data = {
                id:         page.attr('data-id'),
                username:   $('#username').val(),
                old:        page.attr('data-old'),
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
            $.post(ajax + 'user_update.php', data, function(response) {

                // OK Response
                if(response.status === 'OK') {

                    // Update old values
                    page.attr('data-old', response.data);
                    page.find('.modal strong').html(response.data);

                    // Set password fields to blank
                    password_1.val('');
                    password_2.val('');

                }

                // Display alert message
                ds_alert(response.message, response.severity, error);

                // Set feedback
                if(
                    response.status === 'PASSWORD_L_FAIL' ||
                    response.status === 'PASSWORD_FAIL'
                ) ds_error('.password-grp');
                if(
                    response.status === 'USER_FAIL' ||
                    response.status === 'USER_L_FAIL'
                ) ds_error('.username-grp');

                // Re-enable the update button
                button.lBtn(true, 'Update User');

            });

        });

        // Delete user modal
        page.on('click', '#delete', function() {

            // Clear any errors
            ds_clear_errors();

            // Open the modal
            $('.modal').modal();

        });

        // Delete user
        page.on('click', '.modal .btn-danger', function() {

            // Button for deleting users
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Deleting...');

            // User to be deleted
            var data = {
                id:       page.attr('data-id'),
                username: page.attr('data-old')
            };

            // Send the POST request
            $.post(ajax + 'user_delete.php', data, function(response) {

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

                    // Re-enable the button
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