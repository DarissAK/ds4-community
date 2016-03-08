/* Client side scripting for user lists */
$(function () {

    // Set the current page
    var page = $('.ds-users-list');

    // If the page exists
    if(page.length) {

        // Bind filter event and update user count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input'),
                page.find('strong')
            )
        );

        // Bind click event to user table for editing users
        page.find('tbody tr').on('click', function() {

            // Get the user from the 1st td in the row
            var id = $(this).find('td:first').data('user-id');

            // Redirect the user
            document.location.href = '/administrator/users/list/edit/' + id;

        });

    }

});