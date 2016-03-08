/* Client side scripting for editing users */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set current page
    var page = $('.ds-user-edit');

    // If the page exists
    if(page.length) {

        // Bind the click event to the password area header
        page.find('#password-collapse').on('click', function() {

            // Toggle the icon class if the area isn't collapsing
            if(!$('.collapsing').length)
                $(this).find('i').toggleClass('fa-plus, fa-minus');

            // Toggle the password area
            $('#password-area').collapse('toggle');

        });

        // Username selector
        var username = $('#username');

        // Set error location
        var error = '.ds-user-edit > .btn-danger';

        // On update user
        page.find('#update-user').on('click', function() {

            // Clear any errors
            ds_clear_errors(true);

            // Set the button
            var button = $(this);

            // Disable the button to prevent multiple updates
            button.lbtn(false, 'Updating User...');

            // Password selectors
            var password_1 = $('#password-1');
            var password_2 = $('#password-2');

            // POST data
            var data = {
                user_id:      username.data('user-id'),
                username:     username.val(),
                username_old: username.data('username-old'),
                status:       $('#status').val(),
                group:        $('#group').val(),
                password_1:   password_1.val(),
                password_2:   password_2.val()
            };

            // Administrator selector
            var select = $('#administrator-select');

            // If the administrator selector is present
            if(select.length) {
                data['administrator'] = select.val();
            }

            // Send the POST request
            $.post(ajax + 'update_user.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

                // OK Response
                if(response.status === 'OK') {

                    // Set the old username to the new username
                    username.data('username-old', username.val());

                    // Set password fields to blank
                    password_1.val('');
                    password_2.val('');

                }

                // Display alert message
                ds_alert(response.message, response.severity, error);

                // If there are password errors
                if(
                    response.status === 'PASSWORD_L_FAIL' ||
                    response.status === 'PASSWORD_FAIL'
                ) ds_error('.password-grp');

                // If there are password errors
                if(
                    response.status === 'USER_FAIL' ||
                    response.status === 'USER_L_FAIL'
                ) ds_error('.username-grp');

                // Re-enable the update button
                button.lbtn(true, 'Update User');

            });

        });

        // On delete user
        page.find('.modal .btn-danger').on('click', function() {

            // Button for deleting users
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Deleting User...');

            // User to be deleted
            var data = {
                user_id:  username.data('user-id'),
                username: username.data('username-old')
            };

            // Send the POST request
            $.post(ajax + 'delete_user.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

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
                    button.lbtn(true, 'Delete User');

                }

                // Display alert message
                ds_alert(response.message, response.severity, error);

                // Close the modal
                $('.modal').modal('toggle');

            });

        });

    }

});