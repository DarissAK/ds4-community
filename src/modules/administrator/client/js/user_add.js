/* Client side scripting for adding users */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-user-add');

    // If the page exists
    if(page.length) {

        // On add user button click
        page.find('button').on('click', function() {

            // Button to disable
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Adding User...');

            // Request data (user data)
            var data = {
                username:   $('#username').val(),
                password_1: $('#password-1').val(),
                password_2: $('#password-2').val(),
                status:     $('#status').val(),
                group:      $('#group').val()
            };

            // Remove any feedback errors
            ds_clear_errors(true);

            // Send the request
            $.post(ajax + 'add_user.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

                // User added successfully
                if(response.status === 'OK') {

                    // Redirect the user to edit user page
                    setTimeout(function() {

                        document.location.href =
                            'administrator/users/list/edit/' + response.data;

                    }, 800);

                }

                else {

                    // Re-enable the button
                    button.lbtn(true, 'Add User');

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.btn-primary'
                );

                // Username error
                if(
                    response.status === 'USER_L_FAIL' ||
                    response.status === 'USER_FAIL'
                ) {
                    ds_error('.username-grp')
                }

                // Password Error
                if(
                    response.status === 'PASSWORD_L_FAIL' ||
                    response.status === 'PASSWORD_FAIL'
                ) {
                    ds_error('.password-grp')
                }

            });

        });

    }

});