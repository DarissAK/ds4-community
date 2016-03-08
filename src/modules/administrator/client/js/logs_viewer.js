/* Client side scripting for the log viewer */
$(function() {

    // Set the current page
    var page = $('.ds-logs-view');

    // If the page exists
    if(page.length) {

        // Bind filter event and update log count
        page.find('strong').html(
            ds_table_search(
                page.find('table'),
                page.find('input'),
                page.find('strong')
            )
        );

    }

});