<?php
// +-------------------------------------------------------------------------+
// |  Task Scheduler module - Deleting an existing cron task                 |
// +-------------------------------------------------------------------------+
// |  Copyright 2016 Simplusoft LLC                                          |
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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest('ds_task_scheduler', ['task']);

// If the user has the proper permissions and session
// And the proper POST data is present
if(
    !$ds->checkPermission('ds_task_scheduler') ||
    !isset($_POST['task'])
)
    die($ds->APIResponse());

// Set the task description
$description = explode('#', $_POST['task'])[1];

// API Responses
define('OK', "Task $description Deleted");

// Delete the old cron file
if(file_exists($ds->dir . '/modules/task-scheduler/tmp/cron.tmp'))
    unlink($ds->dir . '/modules/task-scheduler/tmp/cron.tmp');

// String for the new crontab
$new_cron = '';

// Get the current crontab and store it in $crontab
exec('crontab -l 2>&1', $crontab);

// If a crontab is found for the apache user
if(is_array($crontab) && $crontab[0] !== 'no crontab for ' . exec('whoami')) {

    // Loop through all the lines of the crontab
    foreach($crontab as $task) {

        // Skip the task to delete it
        if($task === $_POST['task'])
            continue;

        // Append the current task to the new cron
        $new_cron .= $task . PHP_EOL;

    }

}

// Create the new crontab
file_put_contents(
    $ds->dir . '/modules/task-scheduler/tmp/cron.tmp',
    $new_cron,
    FILE_APPEND
);

// Clear the crontab
exec('crontab -r');

// Create the new crontab
exec('crontab ' . $ds->dir . '/modules/task-scheduler/tmp/cron.tmp');

// Log the event
$ds->logEvent("Task $description Deleted", TASK_DELETED);

// OK Response
die($ds->APIResponse('OK', 0, OK));