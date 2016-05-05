/* Client side scripting for the log viewer */
$(function() {

    // Set the current page
    var page = $('.ds-logs-view');

    // If the page exists
    if(page.length) {

        console.log(1);

        page.find('table').DataTable({
            pageLength: 9,
            lengthMenu: [9, 25, 50, 100],
            order: [[0, 'desc']]
        });

    }

});