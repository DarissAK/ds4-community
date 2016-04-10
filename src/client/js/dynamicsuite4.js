// +-------------------------------------------------------------------------+
// |  Core Javascript File                                                   |
// +-------------------------------------------------------------------------+
// |  Copyright 2014-2015 Simplusoft LLC                                     |
// |  All Rights Reserved.                                                   |
// +-------------------------------------------------------------------------+
// |  This program is free software; you can redistribute it and/or modify   |
// |  it under the terms of the GNU General Public License as published by   |
// |  the Free Software Foundation version 2.                                |
// |                                                                         |
// |  This program is distributed in the hope  that  it will be useful, but  |
// |  WITHOUT  ANY  WARRANTY;   without   even   the  implied  warranty  of  |
// |  MERCHANTABILITY  or  FITNESS  FOR  A PARTICULAR PURPOSE.  See the GNU  |
// |  General Public License for more details.                               |
// |                                                                         |
// |  You should have received a copy of the  GNU  General  Public  License  |
// |  along  with  this  program;   if  not,  write  to  the  Free Software  |
// |  Foundation,  Inc.,  51  Franklin  Street,  Fifth  Floor,  Boston,  MA  |
// |  02110-1301, USA.                                                       |
// +-------------------------------------------------------------------------+

// Add an alert bubble after a given element
// String message - Alert message
// Int severity   - Alert Severity (0-3)
// String after   - Put the alert after the given selector
// String id      - Set the ID of the alert
// String group   - Optional bootstrap input group for feedback
function ds_alert(message, severity, after, id, group) {

    // Optional elements
    if(typeof group !== 'undefined') ds_error(group);
    if(typeof id === 'undefined') id = 'generic-alert';

    // Remove alert messages of the same ID
    $('#' + id).remove();

    // The alert type
    var type;

    if(severity === 0) type = 'alert-success';
    else if(severity === 2) type = 'alert-warning';
    else if(severity === 3) type = 'alert-danger';
    else type = 'alert-info';

    // Generate the alert tag
    var alert = '<div id="' + id + '" class="alert ds-alert ' + type +
                '" role="alert">' + message + '</div>';

    // Place the alert after the given element
    $(after).after(alert);

}

// Bootstrap feedback error
// String selector - The selector to add feedback too
function ds_error(selector) {
    $(selector).addClass('has-error has-feedback');
}

// Clear bootstrap feedback errors and ds_alert alerts
function ds_clear_errors() {
    $('.ds-alert').remove();
    $('.has-error, .has-feedback').removeClass('has-error has-feedback');
}

// Filter tables by an input string (bind event)
// String table - Table Selector
// String input - Input Selector
// String count - Optional visible row indicator selector
function ds_table_search(table, input, count) {

    // Function to set table height
    var tableScroll = function() {
        var doc = $(window).height();
        var top = $('.table-container table').offset().top;
        $('.table-container').height(doc - top);
    };

    // Initial and change events
    $(window).resize(tableScroll);
    $(tableScroll);

    // If a count container is given, update it (initial)
    if(typeof count !== 'undefined') {
        count.html(table.find('tbody tr:visible').length);
    }

    // Bind the event
    input.on('input', function() {

        // Get the search value from the input
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        // Get all of the table rows
        var rows = table.find('tbody tr');

        // Show rows, then filter out non-matching ones
        rows.show().filter(function() {

            // Get the row string value
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();

            // Return the index of the input on the row
            return !~text.indexOf(val);

        }).hide();

        // If a count container is given, update it
        if(typeof count !== 'undefined') {
            count.html(table.find('tbody tr:visible').length);
        }

        // If the table is striped, re-stripe it
        if(table.hasClass('table-striped')) {
            table.find('tbody tr:visible:even').css('background', '#f9f9f9', 'important');
            table.find('tbody tr:visible:odd').css('background', 'inherit', 'important');
        }

    });

}

// Set button state and loading values
// Boolean enabled - If the button is enabled or disabled
// String html - The text to show on the button
$.fn.lBtn = function(enabled, html) {

    // Enable the button
    if(enabled === true) {
        this.attr('disabled', false);
        this.html(html);
    }

    // Disable the button
    else {
        var icon = '<i class="fa fa-spin fa-spinner"></i>';
        this.attr('disabled', true);
        this.html(icon + ' ' + html);
    }

};

// Set counter value up or down by one from the current
$.fn.cUpdate = function(type) {

    // If type if false, increment the counter
    if(type !== true) {
        this.html(parseInt(this.html()) + 1);
    }

    // if type is true, decrement the counter
    else {
        this.html(parseInt(this.html()) - 1);
    }

};

// Dynamic Suite Global Javascript
$(function() {

    // Login Function
    $('.ds-login').find('input:submit').on('click', function() {

        // Clear any errors
        ds_clear_errors();

        // The login button
        var button = $(this);

        // Disable the login button
        button.attr('disabled', true).val('Logging in...');

        // Data for AJAX request
        var data = {
            username: $('#username').val(),
            password: $('#password').val()
        };

        // AJAX request for login
        $.post('server/fn_login.php', data, function(response) {

            // On success, go to the default page
            if(response.status === 'OK') {
                window.location.href = response.data;
            }

            // On credential fail
            else {

                // Alert Message
                ds_alert(
                    response.message,
                    response.severity,
                    'form div div:first'
                );

                // Set input group error
                if(response.status === 'ACCT_FAIL') {
                    ds_error('.input-group')
                }

                // Re-enable the login button
                button.attr('disabled', false).val('Login');

            }

        });

    });

    // Navigation bar selector
    var nav = $('#ds-nav-main');

    // Body content selector
    var body = $('#ds-body-content');

    // Tab bar selector
    var tabs = $('#ds-body-tabs');

    // On navigation tab click
    nav.find('li a').on('click', function() {

        // Get the parent
        var parent = $(this).parent().parent().parent().not(':has(div)').find('a');

        // Remove all active tabs that aren't the parent
        $('.ds-nav-active').not(parent).removeClass('ds-nav-active');

        // Don't toggle if collapsing or if child button
        if(!$('.collapsing').length && $(this).parent().find('ul').length) {

            // Toggle the chevron for this
            $(this).find('> i:nth-child(2)')
                .toggleClass('fa-chevron-right fa-chevron-down');

            // Toggle other chevrons
            $('.fa-chevron-down').not($(this).find('i:nth-child(2)'))
                .toggleClass('fa-chevron-right fa-chevron-down');

        }

        // Don't collapse if there are no children
        if($(this).parent().find('ul').length) {

            // Collapse any open accordion menus
            $('ul').not($(this).parent().parent()).collapse('hide');

        }

        // Toggle the active class on the clicked button
        $(this).toggleClass('ds-nav-active');

    });

    // Body resizing (if body exists)
    if(body.length) {

        // Set module content height
        body.css('height', $(window).height() - body.position().top + 'px');

        // On window resize, reset module content height
        $(window).resize(function () {

            // Set new height
            body.css('height', $(window).height() - body.position().top + 'px');

        });

        // URL Array. See dsInstance::url
        var path = window.location.pathname.substr(1).split('/');

        // The current module
        var module = $('#ds-nav-' + path[0]);

        // The current page (if any)
        var page = $('#ds-nav-' + path[0] + '-' + path[1]);

        // The current tab (if any)
        var tab = $('#ds-nav-' + path[0] + '-' + path[1] + '-' + path[2]);

        // If a module is present
        if(module.length) {

            // Set the module to active
            module.parent().find('a:first').addClass('ds-nav-active');

            // Toggle the chevron
            module.parent().find('a:first i:nth-child(2)')
                .toggleClass('fa-chevron-down fa-chevron-right');

            // Open the module accordion menu (if any)
            module.parent().find('ul').addClass('in');

            // If a page is present
            if(page.length && page !== '') {

                // Set the page navigation tab to active
                page.addClass('ds-nav-active');

            }

            // If the tab container isn't found
            if(!tabs.length) {

                // Add padding to tab-less modules
                body.css('padding-top', '1em');

            }

            // If tabs are present
            if(tab.length && tab !== '') {

                // Set the current tab to active
                tab.addClass('active');

            }

            // If no tabs are found
            else {

                // Try and set the 1st available tab to active
                tabs.find('li:first').addClass('active');

            }

        }

    }

});