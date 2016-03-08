<?php
// +-------------------------------------------------------------------------+
// |  Script for adding new permission groups                                |
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

// On invalid request
if(
    !$ds->checkPermission('ds_admin_permission') ||
    !isset($_POST['group']) ||
    !isset($_POST['description'])
)
    die($ds->APIResponse());

// API Responses
define('GROUP_FAIL',   'Group already exists');
define('GROUP_L_FAIL', 'Group name too short');
define('DESC_L_FAIL',  'Description too short');
define('OK',           'Group added');

// Global Settings
define('MIN_GROUP_LENGTH', 2);
define('MIN_DESC_LENGTH',  4);

// Group name is too short
if(strlen($_POST['group']) < MIN_GROUP_LENGTH)
    die($ds->APIResponse('GROUP_L_FAIL', 3, GROUP_L_FAIL));

// Description is too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// If the group already exists
foreach($ds->getPermissionGroups() as $group) {
    if(!strcasecmp($group['name'], $_POST['group']))
        die($ds->APIResponse('GROUP_FAIL', 3, GROUP_FAIL));
}

// Query for adding groups
$query = 'INSERT INTO `ds_group_meta` ' .
         '(`name`, `description`) VALUES (?, ?)';

// Group data
$data = [
    $_POST['group'],
    $_POST['description']
];

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// New row data
$id = $ds->db_conn->lastInsertId();

// Log the event
$ds->logEvent("Group {$data[0]} Added", GROUP_ADDED);

// Row to append to the group table
$tr  = '<tr>';
$tr .= "<td data-group-id='$id'>" . htmlentities($data[0]) . "</td>";
$tr .= '<td>' . htmlentities($data[1]) . '</td>';
$tr .= '</tr>';

// Group add success
die($ds->APIResponse('OK', 0, OK, $tr));