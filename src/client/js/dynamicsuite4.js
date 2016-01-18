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
// Arguments:
// message  - Alert message
// severity - Alert Severity (0-3)
// after    - Put the alert after the given selector
// id       - Set the ID of the alert
// group    - Optional bootstrap input group for feedback
function ds_alert(message, severity, after, id, group) {

    // OPTIONAL: group for feedback
    if(typeof group !== 'undefined') {
        ds_error(group);
    }

    // Remove alert messages of the same ID
    $('#' + id).remove();

    // The alert type
    var type;

    // Case switch for severity
    switch(severity) {

        // Success
        case 0:
            type = 'alert-success';
            break;

        // Warning
        case 2:
            type = 'alert-warning';
            break;

        // Danger
        case 3:
            type = 'alert-danger';
            break;

        // Info (Default)
        default:
            type = 'alert-info';
            break;
    }

    // Generate the alert tag
    var alert = '<div id="' + id + '" class="alert ' + type +
                '" role="alert">' + message + '</div>';

    // Place the alert after the given element
    $(after).after(alert);

}

// Bootstrap feedback error
// Arguments:
// selector - The selector to add feedback too
function ds_error(selector) {
    $(selector).addClass('has-error has-feedback');
}

// Clear bootstrap feedback errors
function ds_clear_errors() {
    var errors = $('.has-error, .has-feedback');
    errors.removeClass('has-error has-feedback');
}

// Filter tables by an input string (bind event)
// Arguments:
// sTable - Table Selector
// sInput - Input Selector
// sCount - Optional visible row indicator selector
function ds_table_search(sTable, sInput, sCount) {

    // Bind the event
    sInput.on('input', function() {

        // Get the search value from the input
        var input = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        // Get all of the table rows
        var rows = sTable.find('tbody tr');

        // Show rows, then filter out non-matching ones
        rows.show().filter(function() {

            // Get the row string value
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();

            // Return the index of the input on the row
            return !~text.indexOf(input);

        }).hide();

        // If a count container is given, update it
        if(typeof sCount !== 'undefined') {
            sCount.html(sTable.find('tbody tr:visible').length);
        }

        // If the table is striped, re-stripe it
        if(sTable.hasClass('table-striped')) {
            sTable.find('tbody tr:visible:even').css('background', '#f9f9f9', 'important');
            sTable.find('tbody tr:visible:odd').css('background', 'inherit', 'important');
        }

    });

}

// Dynamic Suite Global Javascript
$(function() {

    // Login Function
    $('.ds-login').on('click', 'input:submit', function() {

        // The login button
        var button = $(this);

        // Disable the login button
        button.attr('disabled', true);

        // Clear any errors
        ds_clear_errors();

        // Data for AJAX request
        var data = {
            username: $('#username').val(),
            password: $('#password').val()
        };

        // Input for group
        var grp  = $('.input-group');

        // Put the response message after this
        var msg_loc = 'form div div:first';

        // ID of the alert response
        var msg_id  = 'login-msg';

        // If username or password is blank
        if(data.username === '' || data.password === '') {
            ds_alert(
                'Invalid username or password',
                3,
                msg_loc,
                msg_id,
                grp
            );

            // Re-enable the login button
            button.attr('disabled', false);

        }

        // If there is a username and password
        else {

            // AJAX request for login
            $.post('server/fn_login.php', data, function(returned) {

                try {

                    // Parsed Response
                    var response = $.parseJSON(returned);

                    // On success, go to the default page
                    if(response.status === 'OK') {
                        window.location.href = response.data;
                    }

                    // On credential fail
                    else if(response.status === 'ACCT_FAIL') {

                        // Alert Message
                        ds_alert(
                            response.message,
                            response.severity,
                            msg_loc,
                            msg_id,
                            grp
                        );

                    }

                    // Any other response
                    else {

                        // Alert Message
                        ds_alert(
                            response.message,
                            response.severity,
                            msg_loc,
                            msg_id
                        );
                    }

                }

                catch(e) {

                    // Alert Message
                    ds_alert(
                        'An internal error occurred, ' +
                        'please contact your system administrator',
                        3,
                        msg_loc,
                        msg_id
                    );

                }

                // Re-enable the login button
                button.attr('disabled', false);

            });

        }

        return false;

    });

    // On navigation tab click
    $('.ds-nav li a').on('click', function() {

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
    if($('.ds-body > div:nth-child(3)').length) {

        // Module Body Object
        var body = $('.ds-body > div:nth-child(3)');

        // Height to subtract from content area
        var sH = body.position().top;

        // Current body height
        var wH = $('.ds-body').height();

        // If there are no tabs
        if(!$('.ds-tabs').length) {

            // Hide the tab container
            $('.ds-body > div:nth-child(2)').hide();

            // Get new position
            sH = body.position().top;

            // Apply padding to the module content
            body.css('padding', '1em');

        }

        // Set module content height
        body.css('height', wH - sH + 'px');

        // On window resize, reset module content height
        $(window).resize(function() {

            // Set new height
            body.css('height', $('.ds-body').height() - sH + 'px');

        });

        // URI Array. See dsInstance::clean_url
        var path = window.location.pathname.substr(1).split('/');

        // The current module
        var module = $('#' + path[0]);

        // The current page (if any)
        var page   = $('#' + path[0] + '-' + path[1]);

        // The current tab (if any)
        var tab    = $('#' + path[0] + '-' + path[1] + '-' + path[2]);

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

            // If no page is found
            else {

                // Try and set the 1st available page to active
                module.parent().find('.ds-nav ul li:first a:first')
                    .addClass('ds-nav-active');

            }

            // If tabs are present
            if(tab.length && tab !== '') {

                // Set the current tab to active
                tab.addClass('active');

            }

            // If no tabs are found
            else {

                // Try and set the 1st available tab to active
                $('.ds-body > div:nth-child(2) li:first')
                    .addClass('active');

            }

        }

    }

});