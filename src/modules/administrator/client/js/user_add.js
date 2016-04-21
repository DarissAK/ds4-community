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

            // Clear any errors
            ds_clear_errors();

            // Button to disable
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Adding...');

            // Request data
            var data = {
                username:   $('#username').val(),
                password_1: $('#password-1').val(),
                password_2: $('#password-2').val(),
                status:     $('#status').val(),
                group:      $('#group').val()
            };

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

                    // Set button state
                    button.lBtn(true, 'Add User');

                }

                // Display alert
                ds_alert(
                    response.message,
                    response.severity,
                    '.btn-primary',
                    response.status
                );

            });

        });

    }

});