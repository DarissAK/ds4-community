/* Task Scheduler module client side scripting */
$(function() {

    // Server side AJAX directory
    var ajax = '/modules/task-scheduler/server/';

    // Set the current page
    var page = $('.ds-scheduler-tasks');

    // If the page is set
    if(page.length) {

        // Hide the info button when the pre is toggled
        page.find('#info-toggle').on('click', function() {
            $(this).hide();
        });

        page.on('click', 'pre', function() {
            $(this).collapse('hide');
            $(this).on('hidden.bs.collapse', function() {
                $('#info-toggle').show();
            });
        });

        // Global Variables
        var description  = $('#description');
        var command      = $('#command');
        var argument     = $('#argument');
        var minute       = $('#minute');
        var hour         = $('#hour');
        var day_of_month = $('#day-of-month');
        var month        = $('#month');
        var day_of_week  = $('#day-of-week');
        var modal        = $('.modal');

        // Function for updating the table body
        function updateTable() {

            // On page load, update the tasks table
            $.post(ajax + 'get_tasks.php', function(response) {

                // If the request succeeded
                if(response.status === 'OK') {

                    // Update the table
                    page.find('table tbody').hide().html(response.data).fadeIn(800);

                }

            });

        }

        // Update the table body
        updateTable();

        // On the click of the add task button
        page.find('#add-task-modal').on('click', function() {

            // Clear any errors
            ds_clear_errors();

            // Update the modal
            description.val('');
            command.val('');
            argument.val('');
            minute.val('');
            hour.val('');
            day_of_month.val('');
            month.val('');
            day_of_week.val('');
            modal.find('.modal-header h4').html('Add Task');
            modal.find('.modal-footer').html(
                '<button class="btn btn-default" ' +
                'data-dismiss="modal">Close</button>' +
                '<button id="add-task" class="btn ' +
                'btn-primary">Add Task</button>'
            );

            // Toggle the modal
            modal.modal();

        });

        // On the addition of a new task
        page.on('click', '#add-task', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Adding Task...');

            // Post data to send
            var data = {
                description:  description.val(),
                command:      command.val(),
                argument:     argument.val(),
                minute:       minute.val(),
                hour:         hour.val(),
                day_of_month: day_of_month.val(),
                month:        month.val(),
                day_of_week:  day_of_week.val()
            };

            // Send the post request
            $.post(ajax + 'add_task.php', data, function(response) {

                // Set the returned status
                var status = response.status;

                // Task was added
                if(status === 'OK') {

                    // Close the modal
                    modal.modal('toggle');

                    // Display the alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '#top-entry'
                    );

                    // Update the table
                    updateTable();

                }

                // Task was not added
                else {

                    // Show the alert
                    ds_alert(
                        response.message,
                        response.severity,
                        '#alert-entry'
                    );

                    // Set feedback errors
                    if(status === 'DESC_FAIL')  ds_error('.description-grp');
                    if(status === 'CMD_FAIL')   ds_error('.command-grp');
                    if(status === 'ARG_FAIL')   ds_error('.argument-grp');
                    if(status === 'MIN_FAIL')   ds_error('.minute-grp');
                    if(status === 'HR_FAIL')    ds_error('.hour-grp');
                    if(status === 'DOM_FAIL')   ds_error('.day-of-month-grp');
                    if(status === 'MONTH_FAIL') ds_error('.month-grp');
                    if(status === 'DOW_FAIL')   ds_error('.day-of-week-grp');

                }

                // Re-enable the button
                button.lBtn(true, 'Add Task');

            });

        });

        // Cron string for editing cron tasks
        var old_cron;

        // On the click of a table row
        page.on('click', 'tbody tr', function() {

            // Clear any errors
            ds_clear_errors();

            // Update the modal
            modal.find('.modal-header h4').html('Edit Task');
            modal.find('.modal-footer').html(
                '<button class="btn btn-danger" ' +
                'id="delete-task">Delete Task</button>' +
                '<button class="btn btn-default" ' +
                'data-dismiss="modal">Close</button>' +
                '<button id="edit-task" class="btn ' +
                'btn-primary">Update Task</button>'
            );

            // Set the current row
            var tr = $(this);

            // Set the description
            description.val(tr.find('td:eq(0)').html());

            // Set the raw command
            var raw_command = tr.find('td:eq(1)').html().split(' ');
            var schedule    = tr.find('td:eq(2)').html().split(' ');

            // Update the rest of the modal
            command.val(raw_command[0]);
            argument.val(raw_command[1]);
            minute.val(schedule[0]);
            hour.val(schedule[1]);
            day_of_week.val(schedule[2]);
            month.val(schedule[3]);
            day_of_month.val(schedule[4]);

            // Set the cron string
            old_cron  = schedule[0] + ' ' + schedule[1] + ' ' + schedule[2] + ' ';
            old_cron += schedule[3] + ' ' + schedule[4] + ' ' + raw_command[0] + ' ';
            old_cron += raw_command[1] + ' # ' + tr.find('td:eq(0)').html();

            // Toggle the modal
            modal.modal();

        });

        // On click of the edit task button
        page.on('click', '#edit-task', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Updating Task...');

            // Post data
            var data = {
                description:  description.val(),
                command:      command.val(),
                argument:     argument.val(),
                minute:       minute.val(),
                hour:         hour.val(),
                day_of_week:  day_of_week.val(),
                month:        month.val(),
                day_of_month: day_of_month.val(),
                old_cron:     old_cron
            };

            // Send the post request
            $.post(ajax + 'update_task.php', data, function(response) {

                // Set the returned status
                var status = response.status;

                // Task was added
                if(status === 'OK') {

                    // Close the modal
                    modal.modal('toggle');

                    // Display the alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '#top-entry'
                    );

                    // Update the table
                    updateTable();

                }

                // Task was not added
                else {

                    // Show the alert
                    ds_alert(
                        response.message,
                        response.severity,
                        '#alert-entry'
                    );

                    // Set feedback errors
                    if(status === 'DESC_FAIL')  ds_error('.description-grp');
                    if(status === 'CMD_FAIL')   ds_error('.command-grp');
                    if(status === 'ARG_FAIL')   ds_error('.argument-grp');
                    if(status === 'MIN_FAIL')   ds_error('.minute-grp');
                    if(status === 'HR_FAIL')    ds_error('.hour-grp');
                    if(status === 'DOM_FAIL')   ds_error('.day-of-month-grp');
                    if(status === 'MONTH_FAIL') ds_error('.month-grp');
                    if(status === 'DOW_FAIL')   ds_error('.day-of-week-grp');

                }

                // Re-enable the button
                button.lBtn(true, 'Update Task');

            });

        });

        // On deleting tasks
        page.on('click', '#delete-task', function() {

            // Clear any errors
            ds_clear_errors();

            // Set the button
            var button = $(this);

            // Disable the button
            button.lBtn(false, 'Deleting Task...');

            // Post data
            var data = {
                task: old_cron
            };

            // Send the post request
            $.post(ajax + 'delete_task.php', data, function(response) {

                // Set the returned status
                var status = response.status;

                // Task was added
                if(status === 'OK') {

                    // Close the modal
                    modal.modal('toggle');

                    // Display the alert message
                    ds_alert(
                        response.message,
                        response.severity,
                        '#top-entry'
                    );

                    // Update the table
                    updateTable();

                }

                // Task wasn't added
                else {

                    // Show the alert
                    ds_alert(
                        response.message,
                        response.severity,
                        '#alert-entry'
                    );

                }

                // Re-enable the button
                button.lBtn(true, 'Delete Task');

            });

        });

    }

});