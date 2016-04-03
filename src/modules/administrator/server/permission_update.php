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

// Include dependencies
require_once $_SERVER['DOCUMENT_ROOT'] . '/server/fn_init.php';

// Check for valid request
$ds->checkRequest(
    'ds_admin_permission',
    ['id', 'name', 'old', 'description']
);

// Formatted name
$name = htmlentities($_POST['name']);

// API Responses
define('NAME_FAIL',   'Permission already exists');
define('NAME_L_FAIL', 'Permission name too short');
define('DESC_L_FAIL', 'Description too short');
define('OK',          "Permission $name Updated");

// Global Settings
define('MIN_NAME_LENGTH', 2);
define('MIN_DESC_LENGTH', 4);

// Data to update
$data = [
    $_POST['name'],
    $_POST['description'],
    $_POST['id']
];

// If the name is changing
if(strcasecmp($_POST['name'], $_POST['old'])) {

    // Query for seeing if the permission already exists
    $test = 'SELECT * FROM `ds_permissions` WHERE `name` = ?';

    // If the name is already in use
    if(is_array($ds->query($test, $_POST['name'])))
        die($ds->APIResponse('NAME_FAIL', 3, NAME_FAIL));

}

// If the name is too short
if(strlen($_POST['name']) < MIN_NAME_LENGTH)
    die($ds->APIResponse('NAME_L_FAIL', 3, NAME_L_FAIL));

// If the description is too short
if(strlen($_POST['description']) < MIN_DESC_LENGTH)
    die($ds->APIResponse('DESC_L_FAIL', 3, DESC_L_FAIL));

// Query for updating the permission
$query = 'UPDATE `ds_permissions` SET `name` = ?, ' .
         '`description` = ? WHERE `permission_id` = ?';

// On query failure
if(!$ds->query($query, $data))
    die($ds->APIResponse());

$ds->logEvent(OK, PERMISSION_UPDATED);

die($ds->APIResponse('OK', 0, OK, $name));