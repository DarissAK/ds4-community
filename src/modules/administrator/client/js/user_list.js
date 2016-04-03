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

        // Edit user
        page.on('click', 'tbody tr', function() {

            // Get the user ID
            var id = $(this).attr('data-id');

            // Redirect the user
            document.location.href =
                '/administrator/users/list/edit/' + id;

        });

    }

});