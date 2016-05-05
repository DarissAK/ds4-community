/* Client side scripting for listing groups */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/api/groups/';

    // Set current page
    var page = $('.ds-group-list');

    // If the page is the group list page
    if(page.length) {

        // Data table init
        var t = page.find('table').DataTable({
            pageLength: 9,
            lengthMenu: [9, 25, 50, 100]
        });

        // Add group modal
        page.on('click', '#header-area button', function() {

            // Clear any errors
            ds_clear_errors();

            // Reset the inputs
            $('#name, #description').val('');

            // Toggle the add modal
            $('.modal').modal('show');

        });

        // Edit group
        page.on('click', 'tbody tr', function() {

            // Get the permission ID
            var id = $(this).attr('data-id');

            // Redirect the user
            if(typeof id !== 'undefined')
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
            $.post(ajax + 'add.php', data, function(response) {

                // Set error location
                var error = '.modal-body input:last';

                // Add Success
                if(response.status === 'OK') {

                    // Append the new row to the table
                    var row = t.row.add([
                        response.data.name,
                        response.data.description
                    ]).draw().node();

                    // Update the ID
                    $(row).attr('data-id', response.data.id);

                    // Close the modal
                    $('.modal').modal('hide');

                    // Update error location
                    error = '#alert-entry';

                }

                // Display alert
                ds_alert(
                    response.message,
                    response.severity,
                    error,
                    response.status
                );

                // Set button state
                button.lBtn(true, 'Add');

            });

        });

    }

});