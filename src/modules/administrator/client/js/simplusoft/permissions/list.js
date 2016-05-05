/* Client side scripting for the permission list */
$(function() {

    // Ajax script directory
    var ajax = '/modules/administrator/server/api/permissions/';

    // Set the current page
    var page = $('.ds-permission-list');

    // If the page exists
    if(page.length) {

        // Data table init
        var t = page.find('table').DataTable({
            pageLength: 9,
            lengthMenu: [9, 25, 50, 100]
        });

        // Add permission modal
        page.on('click', '#header-area button', function() {

            // Clear any errors
            ds_clear_errors();

            // Reset the inputs
            $('#name, #description').val('');

            // Toggle the add modal
            $('.modal').modal('show');

        });

        // Edit permission
        page.on('click', 'tbody tr', function() {

            // Get the permission ID
            var id = $(this).attr('data-id');

            // Redirect the user
            if(typeof id !== 'undefined')
                document.location.href =
                    '/administrator/permissions/list/edit/' + id;

        });

        // Add permission
        page.on('click', '.modal .btn-primary', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Set button state
            button.lBtn(false, 'Adding...');

            // Request data
            var data = {
                name:        $('#name').val(),
                description: $('#description').val()
            };

            // Send the request
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

                    // Set error location
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