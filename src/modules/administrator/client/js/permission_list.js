/* Client side scripting for the permission list */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set the current page
    var page = $('.ds-permission-list');

    // If the page exists
    if(page.length) {

        // Bind filter event and update permission count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input:eq(0)'),
                page.find('strong')
            )
        );

        // Bind click event to add permission button
        page.find('#header-area button').on('click', function() {

            // Reset the inputs and errors
            $('#permission').val('');
            $('#description').val('');
            ds_clear_errors(true);

            // Toggle the add modal
            $('.modal').modal();

        });

        // On add permission
        page.find('.modal .btn-primary').on('click', function() {

            // Reset any errors
            ds_clear_errors(true);

            // Set the button
            var button = $(this);

            // Disable the button
            button.lbtn(false, 'Adding Permission...');

            // POST request data
            var data = {
                permission:  $('#permission').val(),
                description: $('#description').val()
            };

            // Send the POST request
            $.post(ajax + 'add_permission.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

                // Add Success
                if(response.status === 'OK') {

                    // Counter updates
                    var counter = page.find('#search-area strong');
                    counter.html(parseInt(counter.html()) + 1);

                    // Append the new permission to the table
                    page.find('table tbody').append(response.data);

                    // Close the modal
                    $('.modal').modal('toggle');

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '#alert-entry'
                    );

                }

                // Internal errors
                else {

                    // If there are permission errors
                    if(
                        response.status === 'PERM_FAIL' ||
                        response.status === 'PERM_L_FAIL'
                    ) ds_error('.permission-grp');

                    // If there are description errors
                    if(response.status === 'DESC_L_FAIL')
                        ds_error('.description-grp');

                    // Display alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '.modal-body input:eq(1)'
                    );

                }

                // Re-enable the button
                button.lbtn(true, 'Add Permission');

            });

        });

        // Bind click event to permission table for editing permissions
        page.find('tbody').on('click', 'tr', function() {

            // Get the permission from the 1st td in the row
            var id = $(this).find('td:first').data('permission-id');

            // Redirect the user
            document.location.href =
                '/administrator/permissions/list/edit/' + id;

        });

    }

});