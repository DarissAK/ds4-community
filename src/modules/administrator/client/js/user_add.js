/* Client side scripting for adding users */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-user-add');

    // If the page exists
    if(page.length) {

        // Add user
        page.on('click', 'button', function() {

            // Button to disable
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Adding...');

            // Request data
            var data = {
                username:   $('#username').val(),
                password_1: $('#password-1').val(),
                password_2: $('#password-2').val(),
                status:     $('#status').val(),
                group:      $('#group').val()
            };

            // Clear any errors
            ds_clear_errors();

            // Send the request
            $.post(ajax + 'user_add.php', data, function(response) {

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
                    button.lBtn(true, 'Add User');

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