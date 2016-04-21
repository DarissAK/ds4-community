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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest(
    'ds_admin_permission',
    [
        'name',
        'description'
    ]
);

// Formatted name
$name = htmlentities($_POST['name']);

// API Responses
define('NAME_FAIL',   'Group already exists');
define('NAME_L_FAIL', 'Group name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          "Group $name Added");

// Global Settings
define('MIN_NAME_LENGTH', 2);
define('MIN_DESC_LENGTH',  4);

// Group name is too short
if(strlen($_POST['name']) < MIN_NAME_LENGTH)
    die($ds->APIResponse('NAME_L_FAIL', 3, NAME_L_FAIL));

// Description is too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// If the group already exists
foreach($ds->getPermissionGroups() as $group) {
    if(!strcasecmp($group['name'], $_POST['name']))
        die($ds->APIResponse('NAME_FAIL', 3, NAME_FAIL));
}

// Query for adding groups
$query = 'INSERT INTO `ds_group_meta` ' .
         '(`name`, `description`) VALUES (?, ?)';

// Group data
$data = [
    $_POST['name'],
    $_POST['description']
];

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// New row data
$id = $ds->db_conn->lastInsertId();

// Log the event
$ds->logEvent(OK, GROUP_ADDED);

// Row to append to the group table
$tr  = "<tr data-id='$id'>";
$tr .= "<td>$name</td>";
$tr .= '<td>' . htmlentities($_POST['description']) . '</td>';
$tr .= '</tr>';

// Group add success
die($ds->APIResponse('OK', 0, OK, $tr));