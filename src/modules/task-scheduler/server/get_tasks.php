<?php
// +-------------------------------------------------------------------------+
// |  Task Scheduler module - Get a formatted table of scheduled tasks       |
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

// NOTE:
// This module will run/add tasks ONLY FOR THE PHP USER (apache user)
// This module is not designed for running system tasks/non-php tasks

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest('ds_task_scheduler');

// API Responses
define('OK', 'Success');

// Get the current crontab and store it in $crontab
exec('crontab -l 2>&1', $crontab);

// Table body to return
$tbody = '';

// If a crontab is found for the apache user
if(
    is_array($crontab) &&
    isset($crontab[0]) &&
    $crontab[0] !== 'no crontab for ' . exec('whoami')
) {

    // Loop through all the lines of the crontab
    foreach($crontab as $task) {

        // 1st character of the line
        $start = substr($task, 0, 1);

        // If the line is a comment line, continue
        if($start === '#') continue;

        // Split the task line at comment characters
        $task = explode('#', $task);

        // Trim whitespaces off of the command
        $task[0] = trim($task[0]);

        // Set the job description
        $description = count($task) > 1 ? $task[1] : '';

        // Trim spaces from the beginning of the description
        $description = ltrim($description, ' ');

        // Split the cronjob body at spaces
        $cronjob = explode(' ', $task[0]);

        // Set the schedule values
        $minute       = $cronjob[0];
        $hour         = $cronjob[1];
        $day_of_month = $cronjob[2];
        $month        = $cronjob[3];
        $day_of_week  = $cronjob[4];

        // Count the amount of commands and initialize the pieces
        $cmd_count = count($cronjob);
        $pieces    = [];

        // Add all the commands to a single array
        for($i = 5; $i < $cmd_count; $i++) {
            array_push($pieces, $cronjob[$i]);
        }

        // Combine all the command pieces into a single string
        $command = implode(' ', $pieces);

        // Create the table body
        $tbody .= '<tr>';
        $tbody .= "<td>$description</td>";
        $tbody .= "<td>$command</td>";
        $tbody .= "<td>$minute $hour $day_of_month $month $day_of_week</td>";
        $tbody .= '</tr>';

    }

}

// OK Response
die($ds->APIResponse('OK', 0, OK, $tbody));