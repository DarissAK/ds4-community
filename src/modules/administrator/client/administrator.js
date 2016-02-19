/* Administrator module client side scripting */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // The active page
    var page;

    // User's list module
    var users_list = $('.ds-users-list');

    // If the page is the active users page or inactive users page
    if(users_list.length) {

        // Set the current page
        page = users_list;

        // Set user count
        page.find('strong').html(page.find('tbody tr').length);

        // Bind filter event and update user count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input'),
                page.find('strong')
            )
        );

        // Bind click event to user table for editing users
        page.on('click', 'tbody tr', function() {

            // Get the user from the 1st td in the row
            var user = $(this).find('td:first').html();

            // Redirect the user
            document.location.href = '/administrator/users/list/edit/' + user;

        });

    }

    // Group list module
    var group_list = $('.ds-group-list');

    // If the page is the group list page
    if(group_list.length) {

        // Set the current page
        page = group_list;

        // Set group count
        page.find('strong').html(page.find('tbody tr').length);

        // Bind filter event and update group count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input:eq(0)'),
                page.find('strong')
            )
        );

        // Bind click event to add group button
        page.on('click', 'a', function() {

            // Reset the inputs and errors
            $('#group').val('');
            $('#description').val('');
            $('#group-alert').remove();
            ds_clear_errors();

            // Toggle the add modal
            $('.modal').modal();

        });

        // Bind click event to group table for editing groups
        page.on('click', 'tbody tr', function() {

            // Get the permission from the 1st td in the row
            var group = $(this).find('td:first').html();

            // Redirect the user
            document.location.href =
                '/administrator/permissions/groups/edit/' + group;

        });

        // On add group
        page.find('.modal').on('click', '.btn-primary', function() {

            // Reset feedback
            ds_clear_errors();
            $('#group-alert').remove();

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // Location of the error message
            var err_loc = '.modal-body input:eq(1)';

            // POST request data
            var data = {
                group:       $('#group').val(),
                description: $('#description').val()
            };

            // If the group name is too short
            if(data['group'].length < 4) {

                // Display alert message
                ds_alert(
                    'Group name too short',
                    3,
                    err_loc,
                    'group-alert',
                    '.group-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // If the group name contains invalid characters
            else if(!/^[0-9A-Za-z_]+$/.test(data['group'])) {

                // Display alert message
                ds_alert(
                    'Invalid group name',
                    3,
                    err_loc,
                    'group-alert',
                    '.group-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else if(data['description'].length < 4) {

                // Display alert message
                ds_alert(
                    'Group description too short',
                    3,
                    err_loc,
                    'group-alert',
                    '.description-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // No issues found
            else {

                // Send the POST request
                $.post(ajax + 'add_group.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // Add Success
                    if(response.status === 'OK') {

                        // Counter updates
                        var counter = page.find('div:eq(1) strong');
                        counter.html(parseInt(counter.html()) + 1);

                        // Append the new permission to the table
                        page.find('table tbody').append(
                            '<tr><td>' + data['group'] + '</td>' +
                            '<td>' + data['description'] + '</td></tr>'
                        );

                        // Close the modal
                        $('.modal').modal('toggle');

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            '#alert-entry',
                            'group-alert'
                        );

                    }

                    // Permission already exists
                    else if(response.status === 'GROUP_FAIL') {

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            err_loc,
                            'group-alert',
                            '.group-grp'
                        );

                    }

                    // Internal errors
                    else {

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            err_loc,
                            'group-alert'
                        );

                    }

                }).done(function() {

                    // Re-enable the button
                    btn.attr('disabled', false);

                });

            }

        });

    }

    // Group edit module
    var group_edit = $('.ds-group-edit');

    // Of the page is the edit group page
    if(group_edit.length) {

        // Set the current page
        page = group_edit;

        // On click of the update group button
        page.on('click', '> button:eq(0)', function() {

            // Clear any messages
            $('#group-alert').remove();

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // POST request data
            var data = {
                group:       $('#group').val(),
                group_old:   $('#group-old').val(),
                description: $('#description').val(),
                permissions: $('form').serializeArray()
            };

            // If the permission name is too short
            if(data['group'].length < 4) {

                // Display alert message
                ds_alert(
                    'Group name too short',
                    3,
                    '.btn-danger',
                    'group-alert',
                    '.group-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // If the permission name contains invalid characters
            else if(!/^[0-9A-Za-z_]+$/.test(data['group'])) {

                // Display alert message
                ds_alert(
                    'Invalid group name',
                    3,
                    '.btn-danger',
                    'group-alert',
                    '.group-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else if(data['description'].length < 4) {

                // Display alert message
                ds_alert(
                    'Group description too short',
                    3,
                    '.btn-danger',
                    'group-alert',
                    '.description-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else {

                // Send the POST request
                $.post(ajax + 'update_group.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // Update the old values
                    if(response.status === 'OK') {

                        $('#group-old').val(data['group']);
                        $('.modal-body strong').html(data['group']);

                    }

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '.btn-danger',
                        'group-alert'
                    );

                }).done(function() {

                    // Re-enable the button
                    btn.attr('disabled', false);

                });

            }

        });

        // On click of the delete group button
        page.on('click', '.modal-footer button:eq(1)', function() {

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // POST request data
            var data = {
                group: $('.modal-body strong').html()
            };

            // Send the POST request
            $.post(ajax + 'delete_group.php', data, function(returned) {

                console.log(returned);

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

                } else {

                    // Re-enable the button
                    btn.attr('disabled', false);

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-group-edit > .btn-danger',
                    'group-alert'
                );

            });

        });

    }

    // Permission list page
    var permission_list = $('.ds-permission-list');

    // If the page is the permission list page
    if(permission_list.length) {

        // Set the current page
        page = permission_list;

        // Set permission count
        page.find('strong').html(page.find('tbody tr').length);

        // Bind filter event and update permission count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input:eq(0)'),
                page.find('strong')
            )
        );

        // Bind click event to add permission button
        page.on('click', 'a', function() {

            // Reset the inputs and errors
            $('#permission').val('');
            $('#description').val('');
            $('#perm-alert').remove();
            ds_clear_errors();

            // Toggle the add modal
            $('.modal').modal();

        });

        // On add permission
        page.find('.modal').on('click', '.btn-primary', function() {

            // Reset feedback
            ds_clear_errors();
            $('#perm-alert').remove();

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // Location of the error message
            var err_loc = '.modal-body input:eq(1)';

            // POST request data
            var data = {
                permission:  $('#permission').val(),
                description: $('#description').val()
            };

            // If the permission name is too short
            if(data['permission'].length < 4) {

                // Display alert message
                ds_alert(
                    'Permission name too short',
                    3,
                    err_loc,
                    'perm-alert',
                    '.permission-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // If the permission name contains invalid characters
            else if(!/^[0-9A-Za-z_]+$/.test(data['permission'])) {

                // Display alert message
                ds_alert(
                    'Invalid permission name',
                    3,
                    err_loc,
                    'perm-alert',
                    '.permission-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else if(data['description'].length < 4) {

                // Display alert message
                ds_alert(
                    'Permission description too short',
                    3,
                    err_loc,
                    'perm-alert',
                    '.description-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // No issues found
            else {

                // Send the POST request
                $.post(ajax + 'add_permission.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // Add Success
                    if(response.status === 'OK') {

                        // Counter updates
                        var counter = page.find('div:eq(1) strong');
                        counter.html(parseInt(counter.html()) + 1);

                        // Append the new permission to the table
                        page.find('table tbody').append(
                            '<tr><td>' + data['permission'] + '</td>' +
                            '<td>' + data['description'] + '</td></tr>'
                        );

                        // Close the modal
                        $('.modal').modal('toggle');

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            '#alert-entry',
                            'perm-alert'
                        );

                    }

                    // Permission already exists
                    else if(response.status === 'PERM_FAIL') {

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            err_loc,
                            'perm-alert',
                            '.permission-grp'
                        );

                    }

                    // Internal errors
                    else {

                        // Display alert message
                        ds_alert(
                            response.message,
                            response.severity,
                            err_loc,
                            'perm-alert'
                        );

                    }

                }).done(function() {

                    // Re-enable the button
                    btn.attr('disabled', false);

                });

            }

        });

        // Bind click event to permission table for editing permissions
        page.on('click', 'tbody tr', function() {

            // Get the permission from the 1st td in the row
            var permission = $(this).find('td:first').html();

            // Redirect the user
            document.location.href =
                '/administrator/permissions/list/edit/' + permission;

        });

    }

    // Permission edit page
    var permission_edit = $('.ds-permission-edit');

    // If the page is the edit permission page
    if(permission_edit.length) {

        // Set the current page
        page = permission_edit;

        // On click of the update permission button
        page.on('click', '> button:eq(0)', function() {

            // Clear any messages
            $('#perm-alert').remove();

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // POST request data
            var data = {
                permission:     $('#permission').val(),
                description:    $('#description').val(),
                permission_old: $('#permission-old').val()
            };

            // If the permission name is too short
            if(data['permission'].length < 4) {

                // Display alert message
                ds_alert(
                    'Permission name too short',
                    3,
                    '.btn-danger',
                    'perm-alert',
                    '.permission-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            // If the permission name contains invalid characters
            else if(!/^[0-9A-Za-z_]+$/.test(data['permission'])) {

                // Display alert message
                ds_alert(
                    'Invalid permission name',
                    3,
                    '.btn-danger',
                    'perm-alert',
                    '.permission-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else if(data['description'].length < 4) {

                // Display alert message
                ds_alert(
                    'Permission description too short',
                    3,
                    '.btn-danger',
                    'perm-alert',
                    '.description-grp'
                );

                // Re-enable the button
                btn.attr('disabled', false);

            }

            else {

                // Send the POST request
                $.post(ajax + 'update_permission.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // Update the old values
                    if(response.status === 'OK') {

                        $('#permission-old').val(data['permission']);
                        $('.modal-body strong').html(data['permission']);

                    }

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '.btn-danger',
                        'perm-alert'
                    );

                }).done(function() {

                    // Re-enable the button
                    btn.attr('disabled', false);

                });

            }

        });

        // On click of the delete permission button
        page.on('click', '.modal-footer button:eq(1)', function() {

            // Set the button
            var btn = $(this);

            // Disable the button
            btn.attr('disabled', true);

            // POST request data
            var data = {
                permission: $('.modal-body strong').html()
            };

            // Send the POST request
            $.post(ajax + 'delete_permission.php', data, function(returned) {

                console.log(returned);

                // Parse the response
                var response = $.parseJSON(returned);

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
                    btn.attr('disabled', false);

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.ds-permission-edit > .btn-danger',
                    'perm-alert'
                );

            });

        });

    }

    // Logs view page
    var logs_view = $('.ds-logs-view');

    // If the page is the log viewer page
    if(logs_view.length) {

        // Set the current page
        page = logs_view;

        // Set user count
        page.find('strong').html(page.find('tbody tr').length);

        // Bind filter event and update user count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input'),
                page.find('strong')
            )
        );

    }

    // User edit page
    var user_edit = $('.ds-user-edit');

    // If the page is the edit user page
    if(user_edit.length) {

        // Set the current page
        page = user_edit;

        // On update user
        page.on('click', '.btn-primary', function() {

            $('#user-alert').remove();

            // Disable the button to prevent multiple updates
            $(this).attr('disabled', true);

            // POST data
            var data = {
                user:       $('#user').html(),
                status:     $('#status').val(),
                group:      $('#group').val(),
                password_1: $('#password-1').val(),
                password_2: $('#password-2').val()
            };

            // Administrator selector
            var administrator_select = $('#administrator-select');

            // If the administrator selector is present
            if(administrator_select.length) {

                data['administrator'] = administrator_select.val();

            }

            // If passwords don't match
            if(data['password_1'] !== data['password_2']) {

                // Display alert message
                ds_alert(
                    'Passwords do not match',
                    3,
                    '.btn-danger',
                    'user-alert',
                    '.password-grp'
                );

                // Re-enable the update button
                $(this).attr('disabled', false);

            }

            // If the passwords length are greater than 0 and less than 4
            else if(
                (
                    data['password_1'].length < 4 ||
                    data['password_2'].length < 4
                ) && (
                    data['password_1'].length ||
                    data['password_2'].length
                )
            ) {

                // Display alert message
                ds_alert(
                    'Password too short',
                    3,
                    '.btn-danger',
                    'user-alert',
                    '.password-grp'
                );

                // Re-enable the update button
                $(this).attr('disabled', false);

            }

            // Everything looks good, send the request
            else {

                // Send the POST request
                $.post(ajax + 'update_user.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '.btn-danger',
                        'user-alert'
                    );

                    // Re-enable the update button
                    $('.btn-primary').attr('disabled', false);

                });

            }

        });

        // On delete user
        page.on('click', '.modal .btn-danger', function() {

            // Disable all buttons
            $('.btn').attr('disabled', true);

            // User to be deleted
            var data = {
                user: $('#user').html()
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

                    // Disable all buttons
                    $('.btn').attr('disabled', false);

                }

                // Display alert message
                ds_alert(
                    response.message,
                    response.severity,
                    '.btn-danger',
                    'user-alert'
                );

                // Close the modal
                $('.modal').modal('toggle');

            });

        });

    }

    // If the page is the add user page
    if($('.ds-user-add').length) {

        // On add user button click
        $(this).on('click', 'button', function() {

            // Button to disable
            var button = $(this);

            // Disable the button
            button.attr('disabled', true);

            // Request data (user data)
            var data = {
                user:       $('#user').val(),
                password_1: $('#password-1').val(),
                password_2: $('#password-2').val(),
                status:     $('#status').val(),
                group:      $('#group').val()
            };

            // Remove any feedback errors
            $('.has-error, .has-feedback')
                .removeClass('has-error has-feedback');
            $('#user-alert').remove();

            // Invalid username
            if(data['user'].length < 2) {

                // Display alert message
                ds_alert(
                    'Username too short',
                    3,
                    '.btn-primary',
                    'user-alert',
                    '.user-grp'
                );

                // Re-enable the button
                button.attr('disabled', false);

            }

            // Passwords are too short
            else if(
                data['password_1'].length < 4 ||
                data['password_2'].length < 4
            ) {

                // Display alert message
                ds_alert(
                    'Password too short',
                    3,
                    '.btn-primary',
                    'user-alert',
                    '.password-grp'
                );

                // Re-enable the button
                button.attr('disabled', false);

            }

            // Passwords don't match
            else if(
                data['password_1'] !==
                data['password_2']
            ) {

                // Display alert message
                ds_alert(
                    'Passwords do not match',
                    3,
                    '.btn-primary',
                    'user-alert',
                    '.password-grp'
                );

                // Re-enable the button
                button.attr('disabled', false);

            }

            // No issues found, send the request
            else {

                // Send the request
                $.post(ajax + 'add_user.php', data, function(returned) {

                    // Parse the response
                    var response = $.parseJSON(returned);

                    // User added successfully
                    if(response.status === 'OK') {

                        // Redirect the user
                        setTimeout(function() {

                            document.location.href =
                                'administrator/users/list/edit/' + data['user'];

                        }, 800);


                    }

                    else {

                        // Re-enable the button
                        button.attr('disabled', false);

                    }

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '.btn-primary',
                        'user-alert'
                    );

                });

            }

        });

    }

});
