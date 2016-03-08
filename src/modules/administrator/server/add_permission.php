<?php
// +-------------------------------------------------------------------------+
// |  Script for adding permissions                                          |
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
    !isset($_POST['permission']) ||
    !isset($_POST['description'])
)
    die($ds->APIResponse());

// API Responses
define('PERM_FAIL',   'Permission already exists');
define('PERM_L_FAIL', 'Permission name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          'Permission added');

// Global Settings
define('MIN_PERM_LENGTH', 2);
define('MIN_DESC_LENGTH', 4);

// Data to add
$data = [
    $_POST['permission'],
    $_POST['description']
];

// If the permission already exists
foreach($ds->getPermissions() as $permission) {
    if(!strcasecmp($permission['name'], $data[0]))
        die($ds->APIResponse('PERM_FAIL', 3, PERM_FAIL));
}

// If the permission name is too short
if(strlen($data[0]) < MIN_PERM_LENGTH)
    die($ds->APIResponse('PERM_L_FAIL', 3, PERM_L_FAIL));

// If the permission description is too short
if(strlen($data[1]) < MIN_PERM_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// Query for adding the permission
$query = 'INSERT INTO `ds_permissions` ' .
         '(`name`, `description`) VALUES (?, ?)';

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

// New permission ID
$id = $ds->db_conn->lastInsertId();

// New table row
$tr  = '<tr>';
$tr .= "<td data-permission-id='$id'>";
$tr .= htmlentities($data[0]) . '</td>';
$tr .= '<td>' . htmlentities($data[1]) . '</td>';
$tr .= '</tr>';

// Log the event
$ds->logEvent("Permission {$data[0]} Added", PERMISSION_ADDED);

// Send the OK response
die($ds->APIResponse('OK', 0, OK, $tr));