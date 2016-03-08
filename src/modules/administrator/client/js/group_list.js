/* Client side scripting for listing groups */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/';

    // Set current page
    var page = $('.ds-group-list');

    // If the page is the group list page
    if(page.length) {

        // Bind filter event and update group count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input:eq(0)'),
                page.find('#search-area strong')
            )
        );

        // Bind click event to add group button
        page.find('#header-area button').on('click', function() {

            // Clear any errors
            ds_clear_errors(true);

            // Reset the inputs
            $('#group').val('');
            $('#description').val('');

            // Toggle the add modal
            $('.modal').modal();

        });

        // Bind click event to group table for editing groups
        page.find('tbody tr').on('click', function() {

            // Get the permission from the 1st td in the row
            var id = $(this).find('td:first').data('group-id');

            // Redirect the user
            document.location.href =
                '/administrator/permissions/groups/edit/' + id;

        });

        // On add group
        page.find('.modal .btn-primary').on('click', function() {

            // Reset feedback
            ds_clear_errors(true);

            // Set the button
            var button = $(this);

            // Disable the button and add the spinner
            button.lbtn(false, 'Adding Group...');

            // POST request data
            var data = {
                group:       $('#group').val(),
                description: $('#description').val()
            };

            // Send the POST request
            $.post(ajax + 'add_group.php', data, function(returned) {

                // Parse the response
                var response = $.parseJSON(returned);

                // Set error location
                var error = '.modal-body input:eq(1)';

                // Add Success
                if(response.status === 'OK') {

                    // Counter updates
                    var counter = page.find('#search-area strong');
                    counter.html(parseInt(counter.html()) + 1);

                    // Append the new group to the table
                    page.find('table tbody').append(response.data);

                    // Close the modal
                    $('.modal').modal('toggle');

                    // Update error location
                    error = '#alert-entry';

                }

                // Display alert message
                ds_alert(response.message, response.severity, error);

                // Feedback for group failure
                if(
                    response.status === 'GROUP_FAIL' ||
                    response.status === 'GROUP_L_FAIL'
                ) ds_error('.group-grp');

                // Feedback for description failure
                if(response.status === 'DESC_L_FAIL')
                    ds_error('.description-grp');

                // Re-enable the button and remove the spinner
                button.lbtn(true, 'Add Group');

            });

        });

    }

});