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

        // Add group modal
        page.on('click', '#header-area button', function() {

            // Clear any errors
            ds_clear_errors();

            // Reset the inputs
            $('#name').val('');
            $('#description').val('');

            // Toggle the add modal
            $('.modal').modal();

        });

        // Edit group
        page.on('click', 'tbody tr', function() {

            // Get the permission ID
            var id = $(this).attr('data-id');

            // Redirect the user
            document.location.href =
                '/administrator/permissions/groups/edit/' + id;

        });

        // Add group
        page.on('click', '.modal .btn-primary', function() {

            // Reset feedback
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Disable the button and add the spinner
            button.lBtn(false, 'Adding...');

            // POST request data
            var data = {
                name:        $('#name').val(),
                description: $('#description').val()
            };

            // Send the POST request
            $.post(ajax + 'group_add.php', data, function(response) {

                // Set error location
                var error = '.modal-body input:last';

                // Add Success
                if(response.status === 'OK') {

                    // Counter updates
                    page.find('#search-area strong').cUpdate();

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
                    response.status === 'NAME_FAIL' ||
                    response.status === 'NAME_L_FAIL'
                ) ds_error('.name-grp');

                // Feedback for description failure
                if(response.status === 'DESC_L_FAIL')
                    ds_error('.description-grp');

                // Set button state
                button.lBtn(true, 'Add');

            });

        });

    }

});