<?php
// +-------------------------------------------------------------------------+
// |  Task Scheduler module - Add a new cron task                            |
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

// Include and create a new Dynamic Suite Instance
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// If the user has the proper permissions and session
// And the proper POST data is present
if(
    !$ds->checkPermission('ds_task_scheduler') ||
    !isset($_POST['description']) ||
    !isset($_POST['command']) ||
    !isset($_POST['argument']) ||
    !isset($_POST['minute']) ||
    !isset($_POST['hour']) ||
    !isset($_POST['day_of_month']) ||
    !isset($_POST['month']) ||
    !isset($_POST['day_of_week'])
)
    die($ds->APIResponse());

// API Responses
define('DESC_FAIL',  'Description too short');
define('CMD_FAIL',   'Command too short');
define('ARG_FAIL',   'Argument too short');
define('MIN_FAIL',   'Invalid Minute');
define('HR_FAIL',    'Invalid Hour');
define('DOM_FAIL',   'Invalid Day-of-Month');
define('MONTH_FAIL', 'Invalid Month');
define('DOW_FAIL',   'Invalid Day-of-Week');
define('OK',         "Task {$_POST['description']} Added");

// Global Settings
define('MIN_DESC_LENGTH',    4);
define('MIN_COMMAND_LENGTH', 2);
define('MIN_ARG_LENGTH',     2);

define('MIN_MIN_LENGTH',     0);
define('MIN_HR_LENGTH',      0);
define('MIN_DOM_LENGTH',     0);
define('MIN_MONTH_LENGTH',   1);
define('MIN_DOW_LENGTH',     0);

define('MAX_MIN_LENGTH',    59);
define('MAX_HR_LENGTH',     23);
define('MAX_DOM_LENGTH',    31);
define('MAX_MONTH_LENGTH',  12);
define('MAX_DOW_LENGTH',     7);

// Post values
$minute       = $_POST['minute'];
$hour         = $_POST['hour'];
$day_of_month = $_POST['day_of_month'];
$month        = $_POST['month'];
$day_of_week  = $_POST['day_of_week'];

// Set empty values to *
if($minute !== '*')
    $minute = empty($_POST['minute']) ? '*' : (int) $_POST['minute'];
if($hour !== '*')
    $hour = empty($_POST['hour']) ? '*' : (int) $_POST['hour'];
if($day_of_month !== '*')
    $day_of_month = empty($_POST['day_of_month']) ? '*' : (int) $_POST['day_of_month'];
if($month !== '*')
    $month = empty($_POST['month']) ? '*' : (int) $_POST['month'];
if($day_of_week !== '*')
    $day_of_week = empty($_POST['day_of_week']) ? '*' : (int) $_POST['day_of_week'];

// Description too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_FAIL', 3, DESC_FAIL));

// Command too short
if(strlen($_POST['command']) < MIN_COMMAND_LENGTH)
    die($ds->APIResponse('CMD_FAIL', 3, CMD_FAIL));

// Argument too short
if(strlen($_POST['argument']) < MIN_ARG_LENGTH)
    die($ds->APIResponse('ARG_FAIL', 3, ARG_FAIL));

// Invalid Minute
if($minute !== '*' && ($minute < MIN_MIN_LENGTH || $minute > MAX_MIN_LENGTH))
    die($ds->APIResponse('MIN_FAIL', 3, MIN_FAIL));

// Invalid Hour
if($hour !== '*' && ($hour < MIN_HR_LENGTH || $hour > MAX_HR_LENGTH))
    die($ds->APIResponse('HR_FAIL', 3, HR_FAIL));

// Invalid Day of Month
if($day_of_month !== '*' && ($day_of_month < MIN_DOM_LENGTH || $day_of_month > MAX_DOW_LENGTH))
    die($ds->APIResponse('DOM_FAIL', 3, DOM_FAIL));

// Invalid Month
if($month !== '*' && ($month < MIN_MONTH_LENGTH || $month > MAX_MONTH_LENGTH))
    die($ds->APIResponse('MONTH_FAIL', 3, MONTH_FAIL));

// Invalid Day of Week
if($day_of_week !== '*' && ($day_of_week < MIN_DOW_LENGTH || $day_of_week > MAX_DOW_LENGTH))
    die($ds->APIResponse('DOW_FAIL', 3, DOW_FAIL));

// Delete the old cron file
if(file_exists($ds->dir . '/modules/task-scheduler/tmp/cron.tmp'))
    unlink($ds->dir . '/modules/task-scheduler/tmp/cron.tmp');

// String for the new crontab
$new_cron = '';

// Get the current crontab and store it in $crontab
exec('crontab -l 2>&1', $crontab);

// If a crontab is found for the apache user
if(
    is_array($crontab) &&
    isset($crontab[0]) &&
    $crontab[0] !== 'no crontab for ' . exec('whoami')
) {

    // Loop through all the lines of the crontab
    foreach($crontab as $task) {

        // Append the current task to the new cron
        $new_cron .= $task . PHP_EOL;

    }

}

// Append the new cronjob
$new_cron .= "$minute $hour $day_of_month $month $day_of_week " .
             "{$_POST['command']} {$_POST['argument']} # {$_POST['description']}" .
             PHP_EOL;

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
$ds->logEvent("Task {$_POST['description']} Added", TASK_ADDED);

// OK Response
die($ds->APIResponse('OK', 0, OK));