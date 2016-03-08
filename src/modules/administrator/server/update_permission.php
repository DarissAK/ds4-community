<?php
// +-------------------------------------------------------------------------+
// |  Script for updating permissions                                        |
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
    !isset($_POST['description']) ||
    !isset($_POST['permission_old']) ||
    !isset($_POST['permission_id'])
)
    die($ds->APIResponse());

// API Responses
define('PERM_FAIL',   'Permission already exists');
define('PERM_L_FAIL', 'Permission name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          'Permission Updated');

// Global Settings
define('MIN_PERM_LENGTH', 2);
define('MIN_DESC_LENGTH', 4);

// Data to update
$data = [
    $_POST['permission'],
    $_POST['description'],
    $_POST['permission_id']
];

// If the permission name is changing
if(strcasecmp($data[0], $_POST['permission_old'])) {

    // Query for seeing if the permission already exists
    $test = 'SELECT * FROM `ds_permissions` WHERE `name` = ?';

    // If the username is already in use
    if(is_array($ds->query($test, $data[0])))
        die($ds->APIResponse('PERM_FAIL', 3, PERM_FAIL));

}

// If the permission name is too short
if(strlen($data[0]) < MIN_PERM_LENGTH)
    die($ds->APIResponse('PERM_L_FAIL', 3, PERM_L_FAIL));

// If the description is too short
if(strlen($data[1]) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// Query for updating the permission
$query = 'UPDATE `ds_permissions` SET `name` = ?, ' .
         '`description` = ? WHERE `permission_id` = ?';

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

$ds->logEvent("Permission {$data[0]} Updated", PERMISSION_UPDATED);

die($ds->APIResponse('OK', 0, OK, $data[0]));