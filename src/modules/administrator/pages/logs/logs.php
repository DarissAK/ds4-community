<?php
// +-------------------------------------------------------------------------+
// |  Administrator module - Log viewer                                      |
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

// If the user has the proper permissions and session
$ds->validatePermission('ds_admin_logs');

// Table body
$tbody = '';

// Query for getting all of the active users
$query = 'SELECT * FROM `ds_logs` ' .
         'ORDER BY `log_id` DESC ' .
         'LIMIT 2000';

// Execute the query and continue if success
if(is_array($logs = $ds->query($query))) {

    // Loop through last 250 logs
    foreach($logs as $log) {

        // Format the timestamp
        $log_time =
            $ds->timestampSQL2Format($log['time']);

        // Clean values
        $creator  = htmlentities($log['creator']);
        $affected = htmlentities($log['affected']);
        $event    = htmlentities($log['event']);

        // Create the body row
        $tbody .= '<tr>';
        $tbody .= "<td>{$log['log_id']}</td>";
        $tbody .= "<td>$log_time</td>";
        $tbody .= "<td>{$log['type']}</td>";
        $tbody .= "<td>$creator</td>";
        $tbody .= "<td>$affected</td>";
        $tbody .= "<td>$event</td>";
        $tbody .= "<td>{$log['ip']}</td>";
        $tbody .= '</tr>';

    }

    // Template file to load
    $file = '/modules/administrator/templates/logs/list.html';

    // Load the template
    $template = $ds->loadTemplate($file);

    // Add the table body to the template, then display it
    echo str_replace('{{tbody}}', $tbody, $template);

}

// Error loading logs
else {

    echo 'Error loading logs';

}